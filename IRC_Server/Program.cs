// IRC_Server.cs
// This program runs a server for an IRC, that takes encrypted transmissions,
// decrypts them, writes the received message to all other connected clients,
// which it encrypts before sending.
// Author: Alexander Mack
// 5/29/2023

using System.Net;
using System.Net.Sockets;
using System.Text;
using System.Security.Cryptography;

namespace gIRC_squIRC
{
    class gIRC_squIRC
    {
        // The received messages are stored here and then echoed then nulled
        private static string? new_content;
        // This semaphore prevents more than one message from being sent at
        // the same time, as well as tracking how many clients are connected
        private static Semaphore stream_sem = new Semaphore(1, 1);
        // This semaphore prevents more than one message from being added to
        // the log file at once
        private static Semaphore file_sem = new Semaphore(1, 1);
        private static int num_clients = 0; // track connected clients
        private static int sent_clients = 0; // track new message sending
        private static string path = "";
        // Store a copy of the IV for encryption
        private static byte[] aes_iv = { 109, 157, 48, 146, 170, 221, 230,
            234, 92, 103, 123, 166, 175, 111, 51, 40 };
        static void Main(string[] args)
        {
            // boot server
            Console.WriteLine("Server starting up ...");
            // set the local log file to the current day.
            DateTime current = DateTime.Now;
            path = current.Day + "-" + current.Month + "-" + current.Year + ".txt";
            Console.WriteLine("Today is: {0}-{1}-{2}", current.Day, current.Month, current.Year);
            // launch server
            StartServer();
        }

        /// <summary>
        /// This method starts the server on the desired IP and port.
        /// It then uses a TCP listener to wait for clients to connect,
        /// and creates new threads to handle the clients.
        /// </summary>
        private static void StartServer()
        {
            try
            {
                TcpListener server;
                Int32 port = 11000;
                // Set local address, 10.0.0.177 for now
                IPAddress local = IPAddress.Parse("10.0.0.177");
                server = new TcpListener(local, port);
                // launch server on ip and port
                server.Start();
                Console.WriteLine("Server is started ...");
                // loop while running
                while (true)
                {
                    // if a new client is received
                    TcpClient client = server.AcceptTcpClient();
                    // create a new thread to handle it.
                    Console.WriteLine("New user connecting!");
                    // increment connected users
                    stream_sem.WaitOne();
                    num_clients++;
                    stream_sem.Release();
                    ThreadPool.QueueUserWorkItem(ThreadProc, client);
                }
            }
            catch (Exception e)
            {
                Console.WriteLine("Exception: {0}", e);
            }
        }

        /// <summary>
        /// This method is the thread process created when a new client
        /// connects to the server. It connects to the network stream of the
        /// client and then creates a thread to handle sending new logs to the
        /// client.
        /// </summary>
        /// <param name="obj">
        /// The TcpClient object of the newly connected client.
        /// </param>
        private static void ThreadProc(object? obj)
        {
            var client = (TcpClient)obj!;
            String data = "";
            String timestamp = "";
            String client_name = "";
            // 102 digit string to detect EOT
            String term_signal = "#CKWBo63DfFxgsHGXv6PAZ4l4ms"
                                + "7pU0DqcQZX950VY9H9b4TFF2Feyogwx7jqGwLdHYhm"
                                + "r0wACxZ61yYfaQczNs2Ce4yemd35erDgw";
            // 32 byte symmetrical key from the client
            byte[] aes_key = new byte[32];
            // get stream of client
            NetworkStream stream = client.GetStream();
            try
            {
                // try to receive the aes key from the client and the client
                // name
                (client_name, aes_key) = TradeKeys(stream);
                Console.WriteLine("{0}", client_name);
            }
            catch (Exception e)
            {
                Console.WriteLine("'{0}' has disconnected {1}",
                    client_name, e.Message);
            }
            // create thread for sending new messages to all clients
            Thread log_updater = new Thread(
                () => SendUpdatedLog(stream, aes_key));
            try
            {
                // send existing logs to user
                SendLog(stream, client_name, aes_key);
                // launch the sender thread
                log_updater.Start();
                string logTime = "[" + DateTime.Now.ToString("HH:mm:ss") + "]";
                // write the welcome message to the file and echo
                file_sem.WaitOne();
                WriteToFile(String.Format("{1} {0} has connected to the "
                    + "server!~", client_name, logTime));
                file_sem.Release();
                // while client continues to message
                while (true)
                {
                    // receive first part of message as user information
                    timestamp = ReadBytes(stream, aes_key);
                    // if EOT is received, end transmission
                    if (timestamp.Equals(term_signal))
                        throw new Exception("[Connection Terminated]");
                    // receive second part of message as message contents
                    data = ReadBytes(stream, aes_key);
                    // if EOT is received, end transmission
                    if (data.Equals(term_signal))
                        throw new Exception("[Connection Terminated]");
                    // write data to file and send to all clients
                    if (!data.Equals(""))
                    {
                        // write the received message and echo to all clients
                        file_sem.WaitOne();
                        WriteToFile(timestamp, client_name, data);
                        file_sem.Release();
                    }
                    // clear strings
                    data = "";
                    timestamp = "";
                }
            }
            catch (SocketException e)
            {
                Console.WriteLine("Socket Exception: {0}", e);
            }
            catch (IOException e)
            {
                Console.WriteLine("IO Exception: {0}", e);
            }
            catch (ObjectDisposedException e)
            {
                Console.WriteLine("Object Disposed Exception: {0}", e);
            }
            catch (Exception e)
            {
                Console.WriteLine("'{0}' has disconnected {1}",
                    client_name, e.Message);
            }
            finally
            {
                // close the stream gracefully
                client.Close();
                // decrement the number of connected users
                stream_sem.WaitOne();
                num_clients--;
                stream_sem.Release();
                // write the disconnection message and echo to all users
                string logTime = "[" + DateTime.Now.ToString("HH:mm:ss") + "]";
                file_sem.WaitOne();
                WriteToFile(String.Format("{1} {0} has disconnected"
                    + " [Connection Terminated]", client_name, logTime));
                file_sem.Release();
                // join the sender thread
                log_updater.Join();
            }
            // (server only) clean up message
            Console.WriteLine("{0} Cleaned up ...", client_name);
        }

        /// <summary>
        /// This function takes a newly received message and passes it to the 
        /// log updater, as well as sending it to the clients. It will wait 
        /// for all the clients to send the new message before clearing the 
        /// message and resetting the counter.
        /// </summary>
        /// <param name="timestamp">
        /// The timestamp information of the message
        /// </param>
        /// <param name="client">
        /// The name of the client that sent the message
        /// </param>
        /// <param name="data"> The contents of the message </param>
        private static void WriteToFile(string timestamp, string client,
            string data)
        {
            new_content = timestamp + " " + client + ": " + data;
            // append to file
            File.AppendAllText(path, new_content + "\n");
            // reflect onto console
            Console.WriteLine(new_content);
            // wait until all clients have received the new message
            while (sent_clients < num_clients) ;
            // reset global variables
            new_content = null;
            sent_clients = 0;
        }

        /// <summary>
        /// This function takes a newly received message and passes it to the 
        /// log updater, as well as sending it to the clients. It will wait 
        /// for all the clients to send the new message before clearing the 
        /// message and resetting the counter.
        /// </summary>
        /// <param name="formatted"> The formatted message to send </param>
        private static void WriteToFile(string formatted)
        {
            new_content = formatted;
            // append to file
            File.AppendAllText(path, formatted + "\n");
            // reflect onto console
            Console.WriteLine(new_content);
            // wait until all clients have received the new message
            while (sent_clients < num_clients) ;
            // reset global variables
            new_content = null;
            sent_clients = 0;
        }

        /// <summary>
        /// This method sends new messages to it's designated client, each
        /// client has a thread with this function active.
        /// </summary>
        /// <param name="obj"> The NetworkStream of the current client </param>
        private static void SendUpdatedLog(object? o_stream, object? o_aes_key)
        {
            NetworkStream stream = (NetworkStream)o_stream!;
            byte[] aes_key = (byte[])o_aes_key!;
            bool sent = false;
            try
            {
                while (true)
                {
                    // if new message received and it hasn't been sent yet
                    if (new_content != null && !sent)
                    {
                        // send message to client
                        WriteString(stream, new_content, aes_key);
                        // increment sent client counter
                        stream_sem.WaitOne();
                        sent_clients++;
                        stream_sem.Release();
                        // declare that it has been sent
                        sent = true;
                    }
                    // if no new message and the sent flag is set
                    else if (new_content == null && sent)
                    {
                        // reset flag
                        sent = false;
                    }
                    // check that the stream is still open
                    if (!stream.CanWrite)
                    {
                        throw new ObjectDisposedException(null);
                    }
                }
            }
            catch (ObjectDisposedException)
            {
                // This catches the leftover log_updater when a message is
                // queued to the closed stream
            }
        }

        /// <summary>
        /// This method sends the current log to the client
        /// </summary>
        /// <param name="stream">
        /// The NetworkStream of the current client
        /// </param>
        /// <param name="client"> The name of the current client </param>
        private static void SendLog(NetworkStream stream, string client,
            byte[] aes_key)
        {
            try
            {
                string[]? log_contents = File.ReadAllLines(path);
                string data;
                // 102 character string to indicate start of log transmission
                string log_head = "#CIaLzozT3Wfk8f05ELoDUPnObApoYdbuJ0UvqUTLPd"
                    + "4M8G90qLhGJ92khDiacHhKUaOY42oNJyCXTIByjfEaMTkjZ0ZOYQTHh"
                    + "hy1S";
                // 102 character string to indicate end of log transmission
                string log_done = "#CDAC1wim5Ta0jcyf9fXe8Ckj7YDYzTYkf9EmKDBJOL"
                    + "QU9Os0WGeustNH0PaDn9Tzf0k9rVqsHvzc6XTBHXgRyP1nsJlHaw7NG"
                    + "vq1Z";
                // send start of transmission code (SOT)
                WriteString(stream, log_head, aes_key);
                // wait for confirmation of SOT
                data = ReadBytes(stream, aes_key);
                // Console.WriteLine(data);
                // if not SOT, error and close
                if (!data.Equals(log_head))
                {
                    Console.WriteLine("{0} \n {1} length", data, data.Length);
                    Console.WriteLine("{0} \n {1} length", log_head, log_head.Length);
                    throw new Exception("[Handshake Error]");
                }
                // send each line of the log to the client
                foreach (string line in log_contents!)
                {
                    WriteString(stream, line, aes_key);
                    data = ReadBytes(stream, aes_key);
                    if (!data.Equals(log_head))
                    {
                        throw new Exception("[Handshake Error]");
                    }
                }
                Console.WriteLine("Sending EOT to {0}...", client);
                // send end of transmission code (EOT)
                WriteString(stream, log_done, aes_key);
                // wait for confirmation of EOT
                data = ReadBytes(stream, aes_key);
                // if not EOT, error and close
                if (!data.Equals(log_done))
                {
                    throw new Exception("[Handshake Error]");
                }
            }
            // catch handshake errors and close
            catch (Exception e)
            {
                Console.WriteLine("{0} disconnected due to an error: {1}",
                client, e.Message);
                // decrement number of clients
                stream_sem.WaitOne();
                num_clients--;
                stream_sem.Release();
                stream.Close();
            }
        }

        /// <summary>
        /// This method sends a string to the given client as bytes.
        /// </summary>
        /// <param name="stream"> The NetworkStream of the client </param>
        /// <param name="message"> The contents of the message to send </param>
        /// <param name="aes_key">
        /// The AES encryption key to use for encryption
        /// </param>
        private static void WriteString(NetworkStream stream, string message,
            byte[] aes_key)
        {
            // Encrypt the message using the user's key
            byte[] message_bytes = AESEncrypt(message, aes_key);
            // append a length to the front of the message to simplify format
            byte[] encrypted_message = AppendLengthToFront(message_bytes);
            // send the message to the client
            stream.Write(encrypted_message, 0, encrypted_message.Length);
        }

        /// <summary>
        /// This method receives an array of bytes from the client and
        /// converts it to a string.
        /// </summary>
        /// <param name="stream"> The NetworkStream of the client </param>
        /// <param name="aes_key">
        /// The AES encryption key to use for decryption
        /// </param>
        /// <returns>The string received from the client</returns>
        private static string ReadBytes(NetworkStream stream,
            byte[] aes_key)
        {
            byte[] message_bytes = new byte[2048];
            // read the message from the stream
            int i = stream.Read(message_bytes, 0, message_bytes.Length);
            // use the first 4 bytes to get a strict-size byte array
            byte[] message = ReadFromLength(message_bytes);
            // decrypt the message
            byte[] decrypted_message = AESDecrypt(message, aes_key);
            // return plaintext message
            return Encoding.UTF8.GetString(decrypted_message);
        }

        /// <summary>
        /// This method receives an array of bytes from the client and
        /// converts it to a byte array and a string.
        /// </summary>
        /// <param name="stream"> The NetworkStream of the client </param>
        /// <returns>
        /// The string received from the client and the byte array
        /// </returns>
        private static Tuple<string, byte[]> TradeKeys(NetworkStream stream)
        {
            string client_name = "";
            byte[] aes_key = new byte[32];
            using (RSACryptoServiceProvider rsa = new RSACryptoServiceProvider(2048))
            {
                // Send public key to client
                string pub_key = rsa.ToXmlString(false);
                byte[] key_bytes = Encoding.UTF8.GetBytes(pub_key);
                key_bytes = AppendLengthToFront(key_bytes);
                stream.Write(key_bytes, 0, key_bytes.Length);
                // Receive encrypted symmetrical key from client
                byte[] encrypted_key = new byte[2048];
                int i = stream.Read(encrypted_key, 0, encrypted_key.Length);
                encrypted_key = ReadFromLength(encrypted_key);
                // decrypt the key
                aes_key = RSADecrypt(encrypted_key, rsa.ExportParameters(true), false);
                client_name = ReadBytes(stream, aes_key);
            }
            return Tuple.Create(client_name, aes_key);
        }

        /// <summary>
        /// This method appends a 4 byte array to the front of the message
        /// byte array to state how long the message is (in bytes) before it
        /// is sent so that it can be converted to an exact array.
        /// </summary>
        /// <param name="message">
        /// The byte array of the message to be sent
        /// </param>
        /// <returns> 
        /// The message with 4 bytes representing the length appended to the
        /// front
        /// </returns>
        private static byte[] AppendLengthToFront(byte[] message)
        {
            byte[] length = new byte[4];
            // get the length as integer -> byte[]
            length = BitConverter.GetBytes(message.Length);
            // concatenate to the front of message
            return length.Concat(message).ToArray();
        }

        /// <summary>
        /// This method reads the first 4 bytes of the message to determine
        /// the true length of the message, and cuts off extraneous bytes
        /// from the array.
        /// </summary>
        /// <param name="message"> 
        /// The byte array of the message received
        /// </param>
        /// <returns> 
        /// The message without the first 4 bytes, to the exact length
        /// to the exact length specified
        /// </returns>
        private static byte[] ReadFromLength(byte[] message)
        {
            byte[] length = new byte[4];
            // copy the first four bytes to the length array
            Buffer.BlockCopy(message, 0, length, 0, length.Length);
            // conver the bytes to an integer
            int buf_len = BitConverter.ToInt32(length, 0);
            // create buffer of that length
            byte[] buffer = new byte[buf_len];
            // copy the remaining bytes to the buffer, up to the length
            Buffer.BlockCopy(message, length.Length, buffer, 0, buffer.Length);
            return buffer;
        }

        /// <summary>
        /// This method encrypts a given message using AES encryption via a
        /// key supplied by the user, and a hardcoded IV.
        /// </summary>
        /// <param name="message"> The message to encrypt with AES </param>
        /// <param name="aes_key"> The AES key supplied by the client </param>
        /// <returns> The encrypted message </returns>
        private static byte[] AESEncrypt(string message, byte[] aes_key)
        {
            byte[] encrypted;
            using (Aes aes = Aes.Create())
            {
                // set key and IV
                aes.Key = aes_key;
                aes.IV = aes_iv;
                ICryptoTransform enc = aes.CreateEncryptor(aes.Key, aes.IV);
                using (MemoryStream ms = new MemoryStream())
                {
                    using (CryptoStream cs = new CryptoStream((Stream)ms, enc,
                        CryptoStreamMode.Write))
                    {
                        using(StreamWriter sw = new StreamWriter(cs))
                            sw.Write(message); // write to the encryptor
                    }
                    // copy from the memory stream
                    encrypted = ms.ToArray();
                }
            }
            return encrypted;
        }

        /// <summary>
        /// This method decrypts a given message using AES encryption via a
        /// key supplied by the user, and a hardcoded IV.
        /// </summary>
        /// <param name="message"> The message to decrypt with AES </param>
        /// <param name="aes_key"> The AES key supplied by the client </param>
        /// <returns> The decrypted message </returns>
        private static byte[] AESDecrypt(byte[] message, byte[] aes_key)
        {            
            string temp;
            using (Aes aes = Aes.Create())
            {
                // set key and IV
                aes.Key = aes_key;
                aes.IV = aes_iv;
                ICryptoTransform dec = aes.CreateDecryptor(aes.Key, aes.IV);
                // put encrypted message into the memory stream
                using (MemoryStream ms = new MemoryStream(message))
                {
                    using (CryptoStream cs = new CryptoStream(ms, dec,
                        CryptoStreamMode.Read))
                    {
                        using(StreamReader reader = new StreamReader(cs))
                        temp = reader.ReadToEnd(); // read from the stream
                    }
                }
                // convert back to bytes because it doesn't work otherwise
                message = Encoding.UTF8.GetBytes(temp);
            }
            return message;
        }

        /// <summary>
        /// This method decrypts a given message using RSA encryption via a
        /// private key created by the server
        /// </summary>
        /// <param name="message"> The message to decrypt with RSA </param>
        /// <param name="RSAKey"> The RSA key created by the server </param>
        /// <param name="DoOAEPPadding">
        /// Whether or not DoOAEPPadding should be used for decryption
        /// (currently only false)
        /// </param>
        /// <returns> The decrypted message </returns>
        private static byte[] RSADecrypt(byte[] message, RSAParameters RSAKey, bool DoOAEPPadding)
        {
            try
            {
                byte[] decryptedData;
                using (RSACryptoServiceProvider rsa = new RSACryptoServiceProvider(2048))
                {
                    rsa.ImportParameters(RSAKey);
                    decryptedData = rsa.Decrypt(message, DoOAEPPadding);
                }
                return decryptedData;
            }
            catch (CryptographicException e)
            {
                Console.WriteLine(e.ToString());
                return null;
            }
        }
    }
}