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
            // boot server
            Console.WriteLine("Server starting up ...");
            DateTime current = DateTime.Now;
            // set the local log file to the current day.
            path = current.Day + "-" + current.Month + "-" + current.Year + ".txt";
            Console.WriteLine("Today is: {0}-{1}-{2}", current.Day, current.Month, current.Year);
            // if the log file does not exist create a new one with a header
            if (!File.Exists(path))
            {
                File.Create(path);
                File.WriteAllText(path, "[File Head]");
            }
            // launch server
            StartServer(path);
        }

        private static void StartServer(string logs)
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
                    ThreadPool.QueueUserWorkItem(ThreadProc, client);
                }
            }
            catch (Exception e)
            {
                Console.WriteLine("Exception: {0}", e);
            }

        }

        private static void ThreadProc(object? obj)
        {
            var client = (TcpClient)obj!;
            // get stream of client
            NetworkStream stream = client.GetStream();
            try
            {
                String data = "";
                Byte[] bytes = new Byte[256];
                int i;
                // while client continues to message
                while (true)
                {
                    // receive first part of message as user information
                    i = stream.Read(bytes, 0, bytes.Length);
                    string info = Encoding.ASCII.GetString(bytes, 0, i);
                    // receive second part of message as message contents
                    i = stream.Read(bytes, 0, bytes.Length);
                    data = Encoding.ASCII.GetString(bytes, 0, i);
                    // write to file or output
                    Console.WriteLine("Received: {0}\nFrom: {1}", data, info);
                    // TODO: process data 
                    // Use semaphores to prevent race conditions
                    // Lock(semaphore);
                    data = info + ": " + data;
                    // WriteToLog(path, log);
                    // Unlock(semaphore);
                    byte[] msg = Encoding.ASCII.GetBytes(data);
                    // return updated log? maybe some other trigger here
                    stream.Write(msg, 0, msg.Length);
                    Console.WriteLine("Sent: {0}", data);
                }

            }
            catch (SocketException e)
            {
                Console.WriteLine("SocketException: {0}", e);

            }
            finally
            {
                stream.Close();
                client.Close();
            }


        }

    }
}
