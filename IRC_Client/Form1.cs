// IRC_Client.cs
// This program runs a client for an IRC, that takes messages from the user,
// encrypts them, sends the message to the server, and waits for messages
// to be received from the server asynchronously.
// Author: Alexander Mack
// 5/29/2023

using Microsoft.VisualBasic;
using System;
using System.IO;
using System.Linq;
using System.Net;
using System.Net.Sockets;
using System.Security.Cryptography;
using System.Text;
using System.Windows.Forms;

namespace IRC_Client
{
    public partial class Form1 : Form
    {
        private NetworkStream stream;
        private string nL = Environment.NewLine;
        private string user;
        private static byte[] aes_key;
        private static byte[] aes_iv = { 109, 157, 48, 146, 170, 221, 230,
            234, 92, 103, 123, 166, 175, 111, 51, 40 };

        public Form1()
        {
            InitializeComponent();
            try
            {
                // Get name from user as a text box
                user = Interaction.InputBox("Please enter your screen name: ",
                    "Required Input", "Geck Squid");
                if (user.Length <= 0)
                {
                    throw new Exception("Cancelling!");
                }
                // Address may need to change from here
                // 10.0.0.177 is a local machine
                IPAddress server_address = IPAddress.Parse("10.0.0.177");
                Int32 server_port = 11000;
                IPEndPoint ep = new IPEndPoint(server_address, server_port);
                TcpClient server = new TcpClient();
                server.Connect(ep);
                stream = server.GetStream();
                // Trade public keys with the server, pass on the name of user
                TradeKeys(stream, user);
                // run log reception
                ReceiveLog(stream);
                // launch receiver
                timer1.Enabled = true;
            }
            catch (SocketException e)
            {
                MessageBox.Show(e.Message);
                this.Close();
            }
            catch (NullReferenceException e)
            {
                MessageBox.Show(e.Message);
                this.Close();
            }
            catch (Exception e)
            {
                MessageBox.Show(e.Message);
                this.Close();
            }
        }

        /// <summary>
        /// This method fires every time the send button is pressed on the
        /// form, and sends the message in the output box when activated.
        /// Clears output box after sending.
        /// </summary>
        /// <param name="sender"> The send button object </param>
        /// <param name="e"> The event </param>
        private void send_button_Click(object sender, EventArgs e)
        {
            DateTime current = DateTime.Now;
            // format timestamp info
            string timestamp = "[" + current.ToString("HH:mm:ss") + "]";
            if (!output_box.Text.Equals(""))
            {
                WriteString(stream, timestamp);
                WriteString(stream, output_box.Text);
            }
            output_box.Text = "";
        }

        /// <summary>
        /// This method fires every time a key is pressed while the output box
        /// is selected. If the key is the enter key, the send_button_Click
        /// method is activated.
        /// </summary>
        /// <param name="sender"> The message box object </param>
        /// <param name="e"> The event </param>
        private void output_box_KeyPress(object sender, KeyPressEventArgs e)
        {
            if (e.KeyChar == (char)13) //  The enter key
            {
                e.Handled = true; // prevent error noise
                send_button_Click(sender, e);
            }
        }

        /// <summary>
        /// This method receives the log from the server and outputs it to the
        /// client's screen.
        /// </summary>
        /// <param name="stream">
        /// The NetworkStream of the client
        /// </param>
        private void ReceiveLog(NetworkStream stream)
        {
            // This will print the whole log file from the server before
            // allowing messages
            try
            {
                string data;
                byte[] bytes = new byte[2048];
                // 102 character string to indicate start of log transmission
                string log_head = "#CIaLzozT3Wfk8f05ELoDUPnObApoYdbuJ0UvqUTLPd"
                    + "4M8G90qLhGJ92khDiacHhKUaOY42oNJyCXTIByjfEaMTkjZ0ZOYQTHh"
                    + "hy1S";
                // 102 character string to indicate end of log transmission
                string log_done = "#CDAC1wim5Ta0jcyf9fXe8Ckj7YDYzTYkf9EmKDBJOL"
                    + "QU9Os0WGeustNH0PaDn9Tzf0k9rVqsHvzc6XTBHXgRyP1nsJlHaw7NG"
                    + "vq1Z";
                // wait for start of transmission code (SOT)
                data = ReadBytes(stream);
                // if not SOT, error and close
                if (!data.Equals(log_head))
                {
                    throw new Exception("Handshake Error");
                }
                // return SOT to server to confirm handshake success
                WriteString(stream, log_head);
                do
                {
                    // wait for log message
                    data = ReadBytes(stream);
                    // if not EOT, append message to box and return SOT
                    if (!data.Equals(log_done))
                    {
                        message_log.AppendText(data);
                        message_log.AppendText(nL);
                        WriteString(stream, log_head);
                    }
                } while (!data.Equals(log_done)); // while not EOT
                // return EOT to complete handshake and begin normal
                // communication
                WriteString(stream, log_done);
            }
            catch (Exception e)
            {
                MessageBox.Show(e.Message);
                stream.Close();
            }
        }

        /// <summary>
        /// This method sends a string to the server as bytes.
        /// </summary>
        /// <param name="stream">
        /// The NetworkStream of the client
        /// </param>
        /// <param name="contents">
        /// The contents of the message to send
        /// </param>
        private void WriteString(NetworkStream stream, string message)
        {
            // Encrypt the message using generated AES key
            byte[] message_bytes = AESEncrypt(message);
            // Append the length to the encrypted message
            byte[] encrypted_message = AppendLengthToFront(message_bytes);
            // write the message to the server
            stream.Write(encrypted_message, 0, encrypted_message.Length);
        }

        /// <summary>
        /// This method receives an array of bytes from the server and
        /// converts it to a string.
        /// </summary>
        /// <param name="stream">
        /// The NetworkStream of the client
        /// </param>
        /// <returns>The string received from the server</returns>
        private string ReadBytes(NetworkStream stream)
        {
            try
            {
                byte[] message_bytes = new byte[2048];
                int i = stream.Read(message_bytes, 0, message_bytes.Length);
                // read the first 4 bytes to get the array length
                byte[] message = ReadFromLength(message_bytes);
                // decrypt the message using generated AES key
                byte[] decrypted_message = AESDecrypt(message);
                // return plaintext message
                return Encoding.UTF8.GetString(decrypted_message);
            }
            catch (NullReferenceException) // if there is no message??
            {
                // sanity check
                return "";
            }
        }

        /// <summary>
        /// This method receives a RSA key from the server, then encrypts an 
        /// AES key using RSA encryption, and sends it to the server. 
        /// Afterwards, it sends the client's username with the AES key to 
        /// confirm the message
        /// </summary>
        /// <param name="stream"> The NetworkStream of the server </param>
        /// <param name="name"> The name of the client </param>
        private void TradeKeys(NetworkStream stream, string name)
        {
            using (RSACryptoServiceProvider rsa = 
                new RSACryptoServiceProvider(2048))
            {
                string rsa_key;
                byte[] skb = new byte[2048];
                byte[] len = new byte[4];
                int i = stream.Read(skb, 0, skb.Length);
                // get length of message from front of message
                byte[] rsa_key_bytes = ReadFromLength(skb);
                // get the XML string of the RSA key
                rsa_key = Encoding.UTF8.GetString(rsa_key_bytes);
                // set the RSA key to the received key
                rsa.FromXmlString(rsa_key);
                using(Aes aes = Aes.Create())
                {
                    byte[] encrypted_key;
                    aes_key = aes.Key; // set AES key to use
                    // encrypt the key with RSA
                    encrypted_key = RSAEncrypt(aes.Key, 
                        rsa.ExportParameters(false), false);
                    encrypted_key = AppendLengthToFront(encrypted_key);
                    stream.Write(encrypted_key, 0, encrypted_key.Length);
                    // Send name with aes encryption
                    byte[] name_bytes = AESEncrypt(name);
                    name_bytes = AppendLengthToFront(name_bytes);
                    stream.Write(name_bytes, 0, name_bytes.Length);
                }
            }
            
        }

        /// <summary>
        /// This method appends the length of the message as a 4 byte array
        /// to the front of the message
        /// </summary>
        /// <param name="message"> The message to be sent </param>
        /// <returns> The message with its length appended to the front 
        /// </returns>
        private static byte[] AppendLengthToFront(byte[] message)
        {
            byte[] length = new byte[4];
            // Get the length integer->bytes
            length = BitConverter.GetBytes(message.Length);
            // concatenate length to the front of the message
            return length.Concat(message).ToArray();
        }

        /// <summary>
        /// This method reads the length of the message from the front as a
        /// 4 byte array and creates a byte array of that length with the
        /// rest of the message.
        /// </summary>
        /// <param name="message"> The message that was received </param>
        /// <returns> The message of specified length </returns>
        private static byte[] ReadFromLength(byte[] message)
        {
            byte[] length = new byte[4];
            // Copy first 4 bytes to length
            Buffer.BlockCopy(message, 0, length, 0, length.Length);
            // Convert bytes to int
            int buf_len = BitConverter.ToInt32(length, 0);
            // Create buffer of that length
            byte[] buffer = new byte[buf_len];
            // Copy rest of message to buffer, up to the buffer length
            Buffer.BlockCopy(message, length.Length, buffer, 0, buffer.Length);
            return buffer;
        }

        /// <summary>
        /// This method encrypts a message with AES encryption, using a 
        /// user generated AES key, and a hardcoded IV
        /// </summary>
        /// <param name="message"> The message to be encrypted </param>
        /// <returns> The encrypted message </returns>
        private static byte[] AESEncrypt(string message)
        {
            byte[] encrypted;
            using (Aes aes = Aes.Create())
            {
                aes.Key = aes_key; // generated from TradeKeys method
                aes.IV = aes_iv; // hardcoded
                ICryptoTransform enc = aes.CreateEncryptor(aes.Key, aes.IV);
                using (MemoryStream ms = new MemoryStream())
                {
                    using (CryptoStream cs = new CryptoStream((Stream)ms, enc, 
                        CryptoStreamMode.Write))
                    {
                        using (StreamWriter sw = new StreamWriter(cs))
                            sw.Write(message); // write to memory stream
                    }
                    encrypted = ms.ToArray(); // copy from memory stream
                }
            }
            return encrypted;
        }

        /// <summary>
        /// This method decrypts a message with AES encryption, using a
        /// user generated AES key and a hardcoded IV
        /// </summary>
        /// <param name="message"> The message to be decrypted </param>
        /// <returns> The decrypted message </returns>
        private static byte[] AESDecrypt(byte[] message)
        {
            string temp;
            using (Aes aes = Aes.Create())
            {
                aes.Key = aes_key; // generated from TradeKeys method
                aes.IV = aes_iv; // hardcoded
                ICryptoTransform dec = aes.CreateDecryptor(aes.Key, aes.IV);
                // put encrypted message into the memory stream
                using (MemoryStream ms = new MemoryStream(message))
                {
                    using (CryptoStream cs = new CryptoStream(ms, dec, 
                        CryptoStreamMode.Read))
                    {
                        using (StreamReader reader = new StreamReader(cs))
                            temp = reader.ReadToEnd(); // read from the stream
                    }
                }
                // convert back to bytes because it doesn't work otherwise
                message = Encoding.UTF8.GetBytes(temp);
            }
            return message;
        }

        /// <summary>
        /// This method encrypts a message with RSA encryption via a public
        /// key supplied by the server
        /// </summary>
        /// <param name="message"> The message to encrypt with RSA </param>
        /// <param name="RSAKey"> The RSA key suppplied by the client </param>
        /// <param name="DoOAEPPadding"> 
        /// Whether or not DoOaEPPadding should be used for encryption
        /// (currently only false)
        /// </param>
        /// <returns> The encrypted message </returns>
        private static byte[] RSAEncrypt(byte[] message, RSAParameters RSAKey,
            bool DoOAEPPadding)
        {
            try
            {
                byte[] encryptedData;
                using (RSACryptoServiceProvider rsa = new RSACryptoServiceProvider(1024))
                {
                    rsa.ImportParameters(RSAKey);
                    encryptedData = rsa.Encrypt(message, DoOAEPPadding);
                }
                return encryptedData;
            }
            catch (CryptographicException e)
            {
                Console.WriteLine(e.Message);
                return null;
            }
        }

        /// <summary>
        /// This method catches the form closing event and presents a dialog
        /// box for the user to confirm closing and logging out.
        /// </summary>
        /// <param name="sender"> The form </param>
        /// <param name="e"> The parameters of the from closing </param>
        private void Form1_FormClosing(object sender, FormClosingEventArgs e)
        {
            // if the close reason was not Application.Exit()
            if (e.CloseReason != CloseReason.ApplicationExitCall)
            {
                try
                {
                    // present user with logout message box
                    DialogResult logout
                        = MessageBox.Show("Would you like to log out?", "",
                        MessageBoxButtons.YesNo);
                    // if the user would like to log out
                    if (logout == DialogResult.Yes)
                    {
                        // 102 character string to signal EOT
                        string term_string = "#CKWBo63DfFxgsHGXv6PAZ4l4ms"
                            + "7pU0DqcQZX950VY9H9b4TFF2Feyogwx7jqGwLdHYhm"
                            + "r0wACxZ61yYfaQczNs2Ce4yemd35erDgw";
                        WriteString(stream, term_string);
                        // terminate client
                        throw new Exception("Disconnected!");
                    }
                    else
                    {
                        e.Cancel = true;
                    }
                }
                catch (Exception ex)
                {
                    MessageBox.Show(ex.Message);
                }
            }
        }

        /// <summary>
        /// This method activates each time the timer inside the form ticks
        /// (set to tick every 100ms). This program will pause the timer when
        /// activated, and then asynchronously read a message from the stream.
        /// Only once a message has been read, then will the timer be restarted
        /// so that a new message can be read.
        /// </summary>
        /// <param name="sender"> The form </param>
        /// <param name="e"> The parameters of the event </param>
        private async void timer1_Tick(object sender, EventArgs e)
        {
            try
            {
                // Pause the timer to prevent more streams from opening
                timer1.Stop();
                string data = "";
                byte[] message_bytes = new byte[2048];
                byte[] len = new byte[4];
                // Asynchronously read from the stream
                int i = await stream.ReadAsync(message_bytes, 0, 
                    message_bytes.Length);
                byte[] message = ReadFromLength(message_bytes);
                byte[] decrypted_message = AESDecrypt(message);
                data = Encoding.UTF8.GetString(decrypted_message);
                // if for some reason the message is empty, do not append
                if (i != 0)
                {
                    message_log.AppendText(data);
                    message_log.AppendText(nL);
                }
                // Resume the timer so that more messages may be read
                timer1.Start();
            }
            // If the stream is closed, close the timer.
            catch (ObjectDisposedException)
            {
                this.Close();
            }
        }
    }
}