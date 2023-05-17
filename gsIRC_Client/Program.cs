using System;
using System.Net;
using System.Net.Sockets;
using System.Text;
using System.Threading;

namespace gsIRC_Client
{
    class gsIRC_Client
    {
        public static bool running = true;
        public static Semaphore sem = null;
        public static bool incoming = false;
        static void Main(string[] args)
        {
            // args will be username
            // if not given ask for one
            // try to connect to server
            // wait for user to send a message
            // return confirmation message
            // send logs to user?
            string user;
            try
            {
                sem = Semaphore.OpenExisting("output");
            }
            catch (Exception e)
            {
                sem = new Semaphore(1, 1);
            }
            // Get username from command line argument or ask for a username
            // TODO: Needs validator
            if (args.Length == 0)
            {
                Console.Write("Please enter your screen name: ");
                user = Console.ReadLine()!;
            }
            else
            {
                user = args[0];
            }
            // Attempt to connect to the server
            try
            {
                // Address may need to change from here
                IPAddress server_address = IPAddress.Parse("10.0.0.177");
                Int32 server_port = 11000;
                IPEndPoint ep = new IPEndPoint(server_address, server_port);
                TcpClient server = new TcpClient();
                server.Connect(ep);
                NetworkStream stream = server.GetStream();
                // send user info to server
                byte[] user_info = Encoding.ASCII.GetBytes(user);
                stream.Write(user_info, 0, user_info.Length);
                // launch reception thread
                Thread receiver = new Thread(IncomingHandler!);

                receiver.Start(stream);
                // launch output thread
                OutgoingHandler(user, stream);
                // clean up after programs are complete
                receiver.Join();
                server.Close();
            }
            catch (SocketException e)
            {
                Console.WriteLine("SocketException: {0}", e);
            }
            catch (NullReferenceException e)
            {
                Console.WriteLine("NullReferenceException: {0}", e);
            }
        }
        
        /// <summary>
        /// This method handles the outgoing messages from the user to the 
        /// server.
        /// <param name="user">
        /// The client's name
        /// </param>
        /// <param name="stream">
        /// The NetworkStream of the client
        /// </param>
        /// </summary>
        private static void OutgoingHandler(string user, NetworkStream stream)
        {
            Console.WriteLine("Outgoing launched ...");
            // event handler for catching Ctrl-C events. 
            Console.CancelKeyPress += delegate
                        {
                            Console.WriteLine("Logging out!");
                            // 102 character string to signal EOT
                            string term_string = "#CKWBo63DfFxgsHGXv6PAZ4l4ms"
                                + "7pU0DqcQZX950VY9H9b4TFF2Feyogwx7jqGwLdHYhm"
                                + "r0wACxZ61yYfaQczNs2Ce4yemd35erDgw";
                            WriteString(stream, term_string);
                            // terminate client
                            running = false;
                        };
            string message = "";
            string info = "";
            DateTime current;
            // loop until user signs off
            while (running)
            {
                try
                {
                    current = DateTime.Now;
                    // format timestamp info
                    info = "[" + current.ToString("HH:mm:ss") + "]";
                    // Console.Write("{0}: ", info);
                    // receive message contents from user


                    sem.WaitOne();
                    Console.Write("{0} {1}: ", info, user);
                    message = Console.ReadLine()!;
                    if (!message.Equals(""))
                    {
                        // send first part of message as user info and timestamp
                        current = DateTime.Now;
                        // format timestamp info
                        info = "[" + current.ToString("HH:mm:ss") + "]";
                        WriteString(stream, info); // send timestamp
                        // send second part of message as message contents
                        WriteString(stream, message);
                    }
                    sem.Release();
                }
                catch (Exception e)
                {
                    Console.WriteLine("Exception: {0}", e);
                }
            }
        }

        /// <summary>
        /// This method handles the incoming messages from the server.
        /// <param name="obj">
        /// The NetworkStream object of the client
        /// </param>
        /// </summary>
        private static void IncomingHandler(object obj)
        {
            Console.WriteLine("Incoming launched ...");
            var stream = (NetworkStream)obj;
            ReceiveLog(stream);
            string data = "";
            // continue until user signs off
            while (running)
            {
                try
                {
                    // receive new message from other users
                    data = ReadBytes(stream);
                    sem.WaitOne(); // lock the screen
                    Console.WriteLine("{0}", data);
                    sem.Release(); // unlock the screen
                }
                catch (Exception e)
                {
                    Console.WriteLine("Exception: {0}", e);
                }
            }
        }

        /// <summary>
        /// This method receives the log from the server and outputs it to the
        /// client's screen.
        /// <param name="stream">
        /// The NetworkStream of the client
        /// </param>
        /// </summary>
        private static void ReceiveLog(NetworkStream stream)
        {
            // This will print the whole log file from the server before allowing messages
            try
            {
                string data;
                byte[] bytes = new byte[256];
                string log_head = "#CIaLzozT3Wfk8f05ELoDUPnObApoYdbuJ0UvqUTLPd4M8G9"
                        + "0qLhGJ92khDiacHhKUaOY42oNJyCXTIByjfEaMTkjZ0ZOYQTHhhy1S";
                string log_done = "#CDAC1wim5Ta0jcyf9fXe8Ckj7YDYzTYkf9EmKDBJOLQU9Os"
                    + "0WGeustNH0PaDn9Tzf0k9rVqsHvzc6XTBHXgRyP1nsJlHaw7NGvq1Z";
                data = ReadBytes(stream);
                if (!data.Equals(log_head))
                {
                    throw new Exception("Handshake Error");
                }
                WriteString(stream, log_head);
                do
                {
                    data = ReadBytes(stream);
                    Console.Write(data);
                } while (!data.Equals(log_done));
                WriteString(stream, log_done);
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                stream.Close();
            }
        }

        /// <summary>
        /// This method sends a string to the server as bytes.
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
            bytes = Encoding.ASCII.GetBytes(contents);
            stream.Write(bytes);
        }

        /// <summary>
        /// This method receives an array of bytes from the server and
        /// converts it to a string.
        /// <param name="stream">
        /// The NetworkStream of the client
        /// </param>
        /// <returns>The string received from the server</returns>
        /// </summary>
        private static string ReadBytes(NetworkStream stream)
        {
            string data = "";
            byte[] bytes = new byte[256];
            int i = stream.Read(bytes, 0, bytes.Length);
            data = Encoding.ASCII.GetString(bytes, 0, i);
            return data;
        }
    }
}