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
        private static string[]? log_contents;
        static void Main(string[] args)
        {
            // boot server
            Console.WriteLine("Server starting up ...");
            DateTime current = DateTime.Now;
            // set the local log file to the current day.
            string path = current.Day + "-" + current.Month + "-" + current.Year + ".txt";
            Console.WriteLine("Today is: {0}-{1}-{2}", current.Day, current.Month, current.Year);
            // if the log file does not exist create a new one with a header
            if (!File.Exists(path))
            {
                File.Create(path);
            }
            else
            {
                log_contents = File.ReadAllLines(path);
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
            String data = "";
            String info = "";
            String client_name = "";
            String term_signal = "#CKWBo63DfFxgsHGXv6PAZ4l4ms"
                                + "7pU0DqcQZX950VY9H9b4TFF2Feyogwx7jqGwLdHYhm"
                                + "r0wACxZ61yYfaQczNs2Ce4yemd35erDgw";
            // get stream of client
            NetworkStream stream = client.GetStream();
            SendLog(stream);
            try
            {
                Byte[] bytes = new Byte[256];
                int i;
                i = stream.Read(bytes, 0, bytes.Length);
                client_name = Encoding.ASCII.GetString(bytes, 0, i);
                bytes = new Byte[256];
                Console.WriteLine("'{0}' has connected to the server!", client_name);
                // while client continues to message
                while (true)
                {
                    // receive first part of message as user information
                    i = stream.Read(bytes, 0, bytes.Length);
                    info = Encoding.ASCII.GetString(bytes, 0, i);
                    if (info.Equals(term_signal))
                        throw new Exception("[Connection Terminated]");
                    bytes = new Byte[256];
                    // receive second part of message as message contents
                    i = stream.Read(bytes, 0, bytes.Length);
                    data = Encoding.ASCII.GetString(bytes, 0, i);
                    if (data.Equals(term_signal))
                        throw new Exception("[Connection Terminated]");
                    bytes = new Byte[256];
                    // write to file or output
                    if (!data.Equals(""))
                    {
                        Console.WriteLine("{1} {2}: {0}", data, info, client_name);
                        // TODO: process data 
                        
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

        private static void SendLog(NetworkStream stream)
        {
            try
            {
                string data;
                string log_head = "IaLzozT3Wfk8f05ELoDUPnObApoYdbuJ0UvqUTLPd4M8G9"
                    + "0qLhGJ92khDiacHhKUaOY42oNJyCXTIByjfEaMTkjZ0ZOYQTHhhy1S";
                string log_done = "DAC1wim5Ta0jcyf9fXe8Ckj7YDYzTYkf9EmKDBJOLQU9Os"
                    + "0WGeustNH0PaDn9Tzf0k9rVqsHvzc6XTBHXgRyP1nsJlHaw7NGvq1Z";
                WriteString(stream, log_head);
                data = ReadBytes(stream);
                if (!data.Equals(log_head))
                {
                    throw new Exception("Handshake Error");
                }
                foreach (string line in log_contents!)
                {
                    WriteString(stream, line);
                    
                }
                WriteString(stream, log_done);
                data = ReadBytes(stream);
                if (!data.Equals(log_done))
                {
                    throw new Exception("Handshake Error");
                }
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                stream.Close();
            }
        }

        private static void WriteString(NetworkStream stream, string contents)
        {
            byte[] bytes = new byte[256];
            bytes = Encoding.ASCII.GetBytes(contents);
            stream.Write(bytes);
        }

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
