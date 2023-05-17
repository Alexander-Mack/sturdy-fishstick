using System;
using System.Net;
using System.Net.Sockets;
using System.Threading;
using System.Text;
using System.IO;

namespace gIRC_squIRC
{
    class gIRC_squIRC
    {
        private static string? new_content;
        private static string[]? log_contents;
        private static Semaphore stream_sem = null;
        private static Semaphore file_sem = null;
        private static int num_clients = 0;
        private static int sent_clients = 0;
        private static string path = "";
        static void Main(string[] args)
        {
            // boot server
            Console.WriteLine("Server starting up ...");
            // initialize semaphores
            file_sem = new Semaphore(1, 1);
            stream_sem = new Semaphore(1, 1);

            // set the local log file to the current day.
            DateTime current = DateTime.Now;
            path = current.Day + "-" + current.Month + "-" + current.Year + ".txt";
            Console.WriteLine("Today is: {0}-{1}-{2}", current.Day, current.Month, current.Year);
            // if the log file does not exist create a new one
            if (!File.Exists(path))
            {
                File.Create(path);
            }
            else
            {
                // set log contents to the lines
                log_contents = File.ReadAllLines(path);
            }
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
                    num_clients++;
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
        /// <param name="obj">
        /// The TcpClient object of the newly connected client.
        /// </param>
        /// </summary>
        private static void ThreadProc(object? obj)
        {
            var client = (TcpClient)obj!;
            String data = "";
            String info = "";
            String client_name = "";
            // 102 digit string to detect EOT
            String term_signal = "#CKWBo63DfFxgsHGXv6PAZ4l4ms"
                                + "7pU0DqcQZX950VY9H9b4TFF2Feyogwx7jqGwLdHYhm"
                                + "r0wACxZ61yYfaQczNs2Ce4yemd35erDgw";
            // get stream of client
            NetworkStream stream = client.GetStream();
            // create thread for sending new messages to all clients
            Thread log_updater = new Thread(SendUpdatedLog);
            log_updater.Start(stream);
            // send existing logs to user
            SendLog(stream);
            try
            {
                client_name = ReadBytes(stream);
                Console.WriteLine("'{0}' has connected to the server!", client_name);
                // while client continues to message
                while (true)
                {
                    // receive first part of message as user information
                    info = ReadBytes(stream);
                    // if EOT is received, end transmission
                    if (info.Equals(term_signal))
                        throw new Exception("[Connection Terminated]");
                    // receive second part of message as message contents
                    data = ReadBytes(stream);
                    // if EOT is received, end transmission
                    if (data.Equals(term_signal))
                        throw new Exception("[Connection Terminated]");
                    // write data to file and send to all clients
                    if (!data.Equals(""))
                    {
                        file_sem.WaitOne();
                        WriteToFile(info, client_name, data);
                        file_sem.Release();
                    }
                    data = "";
                    info = "";
                }
            }
            catch (SocketException e)
            {
                Console.WriteLine("SocketException: {0}", e);
                client.Close();
            }
            catch (IOException e)
            {
                Console.WriteLine("IO Exception: {0}", e);
                client.Close();
            }
            catch (Exception e)
            {
                Console.WriteLine("'{0}' has disconnected {1}",
                    client_name, e.Message);
            }
            finally
            {
                client.Close();
            }
            Console.WriteLine("{0} Cleaned up ...", client_name);
        }

        /// <summary>
        /// This function takes a newly received message and passes it to the 
        /// log updater, as well as sending it to the clients. It will wait 
        /// for all the clients to send the new message before clearing the 
        /// message and resetting the counter.
        /// <param name="info">
        /// The timestamp information of the message
        /// </param>
        /// <param name="client">
        /// The name of the client that sent the message
        /// </param>
        /// <param name="data">
        /// The contents of the message
        /// </param>
        /// </summary>
        private static void WriteToFile(string info, string client, string data)
        {
            new_content = info + " " + client + ": " + data + "\n";
            // append to file
            File.AppendAllText(path, new_content);
            // reflect onto console
            Console.Write(new_content);
            // wait until all clients have received the new message
            while (sent_clients < num_clients) ;
            // reset global variables
            new_content = null;
            sent_clients = 0;
        }

        /// <summary>
        /// This method sends new messages to it's designated client, each
        /// client has a thread with this function active.
        /// <param name="obj">
        /// The NetworkStream of the current client
        /// </param>
        /// </summary>
        private static void SendUpdatedLog(object? obj)
        {
            NetworkStream stream = (NetworkStream)obj!;
            bool sent = false;
            while (true)
            {
                // if new message received and it hasn't been sent yet
                if (new_content != null && !sent)
                {
                    // send message to client
                    WriteString(stream, new_content);
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
            }
        }

        /// <summary>
        /// This method sends the current log to the client
        /// <param name="stream">
        ///The NetworkStream of the current client
        /// </param>
        /// </summary>
        private static void SendLog(NetworkStream stream)
        {
            try
            {
                string data;
                // 102 character string to indicate logs incoming
                string log_head = "#CIaLzozT3Wfk8f05ELoDUPnObApoYdbuJ0UvqUTLPd4M8G9"
                    + "0qLhGJ92khDiacHhKUaOY42oNJyCXTIByjfEaMTkjZ0ZOYQTHhhy1S";
                // 102 character string to indicate logs complete
                string log_done = "#CDAC1wim5Ta0jcyf9fXe8Ckj7YDYzTYkf9EmKDBJOLQU9Os"
                    + "0WGeustNH0PaDn9Tzf0k9rVqsHvzc6XTBHXgRyP1nsJlHaw7NGvq1Z";
                // send start of transmission code (SOT)
                WriteString(stream, log_head);
                // wait for confirmation of SOT
                data = ReadBytes(stream);
                // if not SOT, error and close
                if (!data.Equals(log_head))
                {
                    throw new Exception("Handshake Error");
                }
                // send each line of the log to the client
                foreach (string line in log_contents!)
                {
                    WriteString(stream, line);
                }
                // send end of transmission code (EOT)
                WriteString(stream, log_done);
                // wait for confirmation of EOT
                data = ReadBytes(stream);
                // if not EOT, error and close
                if (!data.Equals(log_done))
                {
                    throw new Exception("Handshake Error");
                }
            }
            // catch handshake errors and close
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                stream.Close();
            }
        }

        /// <summary>
        /// This method sends a string to the given client as bytes.
        /// <param name="stream">
        /// The NetworkStream of the client
        /// </param>
        /// <param name="contents">
        /// The contents of the message to send
        /// </param>
        /// </summary>
        private static void WriteString(NetworkStream stream, string contents)
        {
            byte[] bytes = new byte[256];
            // convert contents to bytes
            bytes = Encoding.ASCII.GetBytes(contents);
            stream.Write(bytes);
        }

        /// <summary>
        /// This method receives an array of bytes from the client and
        /// converts it to a string.
        /// <param name="stream">
        /// The NetworkStream of the client
        /// </param>
        /// <returns>The string received from the client</returns>
        /// </summary>
        private static string ReadBytes(NetworkStream stream)
        {
            string data = "";
            byte[] bytes = new byte[256];
            int i = stream.Read(bytes, 0, bytes.Length);
            // convert byte array to string
            data = Encoding.ASCII.GetString(bytes, 0, i);
            return data;
        }
    }
}
