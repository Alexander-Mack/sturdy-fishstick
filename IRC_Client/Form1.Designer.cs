namespace IRC_Client
{
    partial class Form1
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.components = new System.ComponentModel.Container();
            this.message_log = new System.Windows.Forms.TextBox();
            this.client_info_box = new System.Windows.Forms.TextBox();
            this.output_box = new System.Windows.Forms.TextBox();
            this.send_button = new System.Windows.Forms.Button();
            this.client_info_label = new System.Windows.Forms.Label();
            this.timer1 = new System.Windows.Forms.Timer(this.components);
            this.SuspendLayout();
            // 
            // message_log
            // 
            this.message_log.Location = new System.Drawing.Point(12, 12);
            this.message_log.Multiline = true;
            this.message_log.Name = "message_log";
            this.message_log.ReadOnly = true;
            this.message_log.ScrollBars = System.Windows.Forms.ScrollBars.Vertical;
            this.message_log.Size = new System.Drawing.Size(564, 400);
            this.message_log.TabIndex = 0;
            // 
            // client_info_box
            // 
            this.client_info_box.Location = new System.Drawing.Point(582, 28);
            this.client_info_box.Multiline = true;
            this.client_info_box.Name = "client_info_box";
            this.client_info_box.ReadOnly = true;
            this.client_info_box.Size = new System.Drawing.Size(206, 410);
            this.client_info_box.TabIndex = 1;
            // 
            // output_box
            // 
            this.output_box.Location = new System.Drawing.Point(12, 418);
            this.output_box.Name = "output_box";
            this.output_box.Size = new System.Drawing.Size(483, 20);
            this.output_box.TabIndex = 2;
            this.output_box.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.output_box_KeyPress);
            // 
            // send_button
            // 
            this.send_button.Location = new System.Drawing.Point(501, 418);
            this.send_button.Name = "send_button";
            this.send_button.Size = new System.Drawing.Size(75, 23);
            this.send_button.TabIndex = 3;
            this.send_button.Text = "Send";
            this.send_button.UseVisualStyleBackColor = true;
            this.send_button.Click += new System.EventHandler(this.send_button_Click);
            // 
            // client_info_label
            // 
            this.client_info_label.AutoSize = true;
            this.client_info_label.Location = new System.Drawing.Point(639, 12);
            this.client_info_label.Name = "client_info_label";
            this.client_info_label.Size = new System.Drawing.Size(89, 13);
            this.client_info_label.TabIndex = 4;
            this.client_info_label.Text = "Connected Users";
            // 
            // timer1
            // 
            this.timer1.Tick += new System.EventHandler(this.timer1_Tick);
            // 
            // Form1
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(800, 450);
            this.Controls.Add(this.client_info_label);
            this.Controls.Add(this.send_button);
            this.Controls.Add(this.output_box);
            this.Controls.Add(this.client_info_box);
            this.Controls.Add(this.message_log);
            this.Name = "Form1";
            this.Text = "Geck Squid";
            this.FormClosing += new System.Windows.Forms.FormClosingEventHandler(this.Form1_FormClosing);
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.TextBox message_log;
        private System.Windows.Forms.TextBox client_info_box;
        private System.Windows.Forms.TextBox output_box;
        private System.Windows.Forms.Button send_button;
        private System.Windows.Forms.Label client_info_label;
        private System.Windows.Forms.Timer timer1;
    }
}

