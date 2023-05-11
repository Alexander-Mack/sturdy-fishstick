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
        private static string path = "";
        static void Main(string[] args)
        {
            Console.WriteLine("Server starting up ...");
            DateTime current = DateTime.Now;
            path = current.Day + "-" + current.Month + "-" + current.Year + ".txt";
            if (!File.Exists(path))
            {
                File.Create(path);
                File.WriteAllText(path, "[File Head]");
            }
            StartServer(path);
        }

        private static void StartServer(string logs)
        {
            TcpListener server;
            Int32 port = 11000;
            IPAddress local = IPAddress.Parse("68.148.70.170");
            server = new TcpListener(local, port);
            server.Start();
            Console.WriteLine("Server is started ...");
            while (true)
            {
                TcpClient client = server.AcceptTcpClient();
                ThreadPool.QueueUserWorkItem(ThreadProc, client);
            }

        }

        private static void ThreadProc(object obj)
        {
            var client = (TcpClient)obj;
            try
            {
                NetworkStream stream = client.GetStream();
                String data = "";
                Byte[] bytes = new Byte[256];
                int i;
                while((i = stream.Read(bytes, 0, bytes.Length))!=0)
                {
                    data = Encoding.ASCII.GetString(bytes, 0, i);
                    Console.WriteLine("Received: {0}", data);
                    // TODO: process data 
                    // Use semaphores to prevent race conditions
                    // Lock(semaphore);
                    data = data.ToUpper(); 
                    // WriteToLog(path, log);
                    // Unlock(semaphore);
                    byte[] msg = Encoding.ASCII.GetBytes(data);
                    stream.Write(msg, 0, msg.Length);
                    Console.WriteLine("Sent: {0}", data);
                    stream.Close();
                    client.Close();
                }
            }catch(SocketException e)
            {
                Console.WriteLine("SocketException: {0}", e);
            }
            finally
            {
                client.Close();
            }


        }

    }
}
