using System;
using System.Net;
using System.Net.Sockets;
using System.Text;
using System.Threading;

namespace gsIRC_Client
{
    class gsIRC_Client
    {
        static void Main(string[] args)
        {
            // args will be username
            // if not given ask for one
            // try to connect to server
            // wait for user to send a message
            // return confirmation message
            // send logs to user?
            string user;
            // Get username from command line argument or ask for a username
            // TODO: Needs validator
            if (args.Length == 0)
            {
                Console.Write("Please enter your screen name: ");
                user = Console.ReadLine();
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
                Thread receiver = new Thread(IncomingHandler);
                receiver.Start(stream);
                // go to message handler
                OutgoingHandler(user, stream);
                
            }
            catch (SocketException e)
            {
                Console.WriteLine("SocketException: {0}", e);
            }
        }
        private static void OutgoingHandler(string user, NetworkStream stream)
        {
            string message = "";
            int i;
            // open the message stream
            
            // continue until user signs off
            while (true)
            {
                try
                {
                    // timestamp and wait for input
                    DateTime current = DateTime.Now;
                    // format timestamp and user info
                    String info = "[" + current.ToString("HH:mm:ss") + "] " + user;
                    // Console.Write("{0}: ", info);
                    // receive message contents from user
                    message = Console.ReadLine();
                    // send first part of message as user info and timestamp
                    byte[] sender = Encoding.ASCII.GetBytes(info);
                    stream.Write(sender, 0, sender.Length);
                    // send second part of message as message contents
                    byte[] msg = Encoding.ASCII.GetBytes(message);
                    stream.Write(msg, 0, msg.Length);
                }
                catch (Exception e)
                {
                    Console.WriteLine("Exception: {0}", e);
                }
            }
        }

        private static void IncomingHandler(object obj)
        {
            var stream = (NetworkStream) obj;
            byte[] bytes = new byte[256];
            int i;
            string data = "";
            // continue until user signs off
            while(true)
            {
                try
                {
                    // receive new message from other users
                    i = stream.Read(bytes, 0, bytes.Length);
                    data = Encoding.ASCII.GetString(bytes, 0, i);
                    // write to screen, will need mutual exclusion
                    Console.WriteLine("{0}: ", data);
                }
                catch(Exception e)
                {
                    Console.WriteLine("Exception: {0}", e);
                }
            }
        }
    }
}