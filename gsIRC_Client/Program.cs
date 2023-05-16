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

        private static void OutgoingHandler(string user, NetworkStream stream)
        {
            Console.WriteLine("Outgoing launched ...");
            Console.CancelKeyPress += delegate
                        {
                            Console.WriteLine("Logging out!");
                            string term_string = "#CKWBo63DfFxgsHGXv6PAZ4l4ms"
                                + "7pU0DqcQZX950VY9H9b4TFF2Feyogwx7jqGwLdHYhm"
                                + "r0wACxZ61yYfaQczNs2Ce4yemd35erDgw";
                            byte[] term_signal = Encoding.ASCII.GetBytes(term_string);
                            stream.Write(term_signal, 0, term_signal.Length);
                            running = false;
                        };
            string message = "";
            String info = "";
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
                        byte[] sender = Encoding.ASCII.GetBytes(info);
                        stream.Write(sender, 0, sender.Length); // send timestamp
                        // send second part of message as message contents
                        byte[] msg = Encoding.ASCII.GetBytes(message);
                        stream.Write(msg, 0, msg.Length);
                    }
                    sem.Release();

                }
                catch (Exception e)
                {
                    Console.WriteLine("Exception: {0}", e);
                }
            }
        }

        private static void IncomingHandler(object obj)
        {
            Console.WriteLine("Incoming launched ...");
            var stream = (NetworkStream)obj;
            ReceiveLog(stream);
            byte[] bytes = new byte[256];
            int i;
            string data = "";
            // continue until user signs off
            while (running)
            {
                try
                {
                    // receive new message from other users

                    // wait for a message
                    i = stream.Read(bytes, 0, bytes.Length);
                    data = Encoding.ASCII.GetString(bytes, 0, i);
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

        private static void ReceiveLog(NetworkStream stream)
        {
            // This will print the whole log file from the server before allowing messages
        }
    }
}