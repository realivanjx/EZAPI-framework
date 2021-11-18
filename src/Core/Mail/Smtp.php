<?php
    namespace Core\Mail;
    use \Exception;

    /**
    * This SMTP module is still in its early development process
    * report bugs or request implementation directly from the author.
    * furder documentation RFC0821, RFC0822, RFC1869, RFC2045, RFC2821
    *
    * TODO
    * 1-PIPELINE implementation
    * 
    * What will not be implemented!
    * 1-SSL support. It has been replaced with the TLS protocol.
    * 2- port 25 || 2525 support. not secure and many email clients are not supporting it either.
    * 
    */


    class Smtp
    {
        public 
            $subject, 
            $body, #Plain text or html 
            $replyTo, #empty by default     
            $from = [], #Name => Email default is empty
            $to = [], #Name => Email
            $attachment = [], #Name => file path  
            $cc = [], #empty by default
            $bcc = []; #empty by default

        /**
         * Helper strings
         */
        private $smtp, $data = null;

        private $config = [
            "CHARSET" => "UTF-8",
            "MAIL_PRIORITY" => 3,
            "CONSOLE_LOG" => true,
            "AUTH_METHOD" => "STANDARD", # OR TOKEN 
            "CONNECTION_TIMEOUT" => 20, #Seconds
        ];


        public function __construct()
        {
            $this->connect();
        }


        public function __destruct()
        {
          #Disconnect
          fclose($this->smtp);
        }
        
        
        /**
         * @method connect
         * This is the handshake process
         */
        public  function connect()
        {
            try
            {
                #Connection
                $connection = fsockopen(
                    EZENV["SMTP_HOST"], 
                    EZENV["SMTP_PORT"], 
                    $errno, 
                    $errstr, 
                    $this->config["CONNECTION_TIMEOUT"]
                );

                if(empty($connection))
                {
                    throw new Exception("$errstr ($errno)");
                }

                #Connection response
                $response = fgets($connection, 512); 

                $responseCode = (int) substr($response, 0, 3);

                #Validate response
                if($responseCode !== 220)
                {
                    throw new Exception($response);
                }

                $this->smtp = $connection;
                
                #Handshake
                $this->sendCommand("HELO", 250);
            
                #secure
                $this->sendCommand("STARTTLS", 220);

                if(!stream_socket_enable_crypto($connection, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) 
                {
                    throw new Exception("Failed to start TLS");
                }

                #Encrypted Handshake
                $this->sendCommand("HELO", 250);

                if($this->config["AUTH_METHOD"] == "STANDARD")
                {
                    #Auth login using username and password
                    $this->sendCommand("AUTH LOGIN", 334);
                    $this->sendCommand(base64_encode(EZENV["SMTP_USERNAME"]), 334);
                    $this->sendCommand(base64_encode(EZENV["SMTP_PASSWORD"]), 235);
                }
                else if($this->config["AUTH_METHOD"] == "TOKEN")
                {
                    $token = base64_encode(sprintf("user=%s%sauth=Bearer %s%s%s",
                        EZENV["SMTP_USERNAME"], chr(1),
                        EZENV["SMTP_AUTH_TOKEN"], chr(1), chr(1)
                    ));
        
                    $this->sendCommand("AUTH XOAUTH2 {$token}", 235);
                }
            }
            catch(Exception $ex)
            {
                throw new Exception($ex);
            }
        }


        /**
         * @method send
         * @return string
         * @throws exceptions
         * 
         * @before attempting to send an email you must first set the
         * required strings to prevent connection errors.
         */
        public function send()
        {
            if(empty($this->subject) || empty($this->body) || empty($this->to))
            {
                return "subject, body and to strings are requried to send an email";
            }

            $this->sendCommand(sprintf("MAIL FROM: <%s>", EZENV["SMTP_USERNAME"]), 250);

            foreach($this->to as $name => $email)
            {
                $this->sendCommand("RCPT TO: <{$email}>", 250);
            }

            $this->sendCommand("DATA", 354);
    
            #Create mail string
            $this->createData();

            $this->sendCommand($this->data, 250);

            return "Email sent successfully";
        }


        /**
         * @method createData
         * STOP :: WARNING :: before modifying this file 
         * you must read and understant how mime works.
         */
        private function createData()
        {
            #Email header
            $this->addString(["MIME-Version" => "1.0"]);
            $this->addString(["X-PoweredBy" => EZENV["APP_NAME"]]);
            $this->addString(["X-Mailer" => EZENV["APP_NAME"]]);
            $this->addString(["Date" => date('r')]);
            $this->addString(["X-Priority" => $this->config["MAIL_PRIORITY"]]);  
            $this->addString(["Subject" => $this->subject], 1, true);
            $this->addString(["Return-Path" => EZENV["SMTP_USERNAME"]]);
        
            #Default value
            if(empty($this->from))
            {
                $this->from = [EZENV["APP_NAME"] => EZENV["SMTP_USERNAME"]];
            }

            $this->addString(["From" => sprintf("%s <%s>", key($this->from), end($this->from))]);
            $this->addString(["Message-ID" => sprintf("<%s.%s>", md5(uniqid()),  end($this->from))]);
            
            #To Header 
            $tostring = null;
            foreach ($this->to as $toName => $toEmail) 
            {
                if(!empty($toName))
                {
                    $toName = $this->encodeString($toName);
                }

                $tostring .=  "{$toName}<{$toEmail}>,";
            }

            #Remove the last comma
            $tostring = rtrim($tostring, ",");

            $this->addString(["To" => $tostring]);


            #CC Header 
            $ccString = null;
            foreach ($this->cc as $ccName => $ccEmail) 
            {
                if(!empty($ccName))
                {
                    $ccName = $this->encodeString($ccName); 
                }

                $ccString .=  "{$ccName} <{$ccEmail}>,";
            }

            #Remove the last comma
            $ccString = rtrim($ccString, ",");

            $this->addString(["Cc" => $ccString]);


            #BCC Header 
            $bccstring = null;
            foreach ($this->bcc as $bccName => $bccEmail) 
            {
                if(!empty($bccName))
                {
                    $bccName = $this->encodeString($bccName);
                }

                $bccstring .=  "{$bccName} <{$bccEmail}>,";
            }

            #Remove the last comma
            $bccstring = rtrim($bccstring, ",");

            $this->addString(["Bcc" => $bccstring]);

            #Reply to
            if(empty($this->replyTo))
            {
                $this->replyTo = EZENV["SMTP_USERNAME"];
            }

            $this->addString(["Reply-To" => $this->replyTo]);

            
            $boundary = md5(uniqid(rand(), true));

            $multiPart = !$this->attachment ? "alternative" : "mixed";

            $this->addString(["Content-Type" => "multipart/{$multiPart}; boundary=\"{$boundary}\""]); 

            #html content
            $this->addString("--{$boundary}");
            $this->addString(["Content-Type" => "text/html; charset=\"UTF-8\""]);
            $this->addString(["Content-Transfer-Encoding" => "base64"], 2); #Two line breaks
            $this->addString(chunk_split(base64_encode($this->body)));

            #Attachments
            if(!empty($this->attachment))
            {
                foreach ($this->attachment as $name => $path)
                {
                    #Add file extension to the name
                    $name = sprintf("%s.%s", $name, pathinfo($path, PATHINFO_EXTENSION));

                    $this->addString("--{$boundary}");
                    $this->addString(["Content-Type" => "application/octet-stream; name=\"{$name}\""]);
                    $this->addString(["Content-Transfer-Encoding" => "base64"]);
                    $this->addString(["Content-Disposition" => "attachment; filename=\"{$name}\""], 2);
                    $this->addString(chunk_split(base64_encode(file_get_contents($path)))); 
                }
            }

            #End alternative
            $this->addString("--{$boundary}--");

            #End content
            $this->addString(".");
        }


        /**
         * @method encodeString
         * @param string 
         * @return string
         */
        private function encodeString($string)
        {
            return sprintf("=?utf-8?B?%s?= ", base64_encode($string));
        }


        /**
         * @method addString 
         * @param string | array content
         * @param int breakNumber
         * @param boolean encoded
         * Appends to data
         */
        private function addString($content, $breakNumber = 1, $encoded = false)
        {
            #determine line breaks
            $lineBreak = $breakNumber == 1 ? PHP_EOL : PHP_EOL . PHP_EOL;

            #Content is not an array
            if(!is_array($content))
            {
                $this->data .= sprintf("%s%s", $content, $lineBreak);

                return;
            }

            #Content is encoded
            if($encoded)
            {
                $this->data .= sprintf("%s: =?utf-8?B?%s?=%s", key($content), base64_encode(end($content)), $lineBreak);

                return;
            }
            
            #Default
            $this->data .= sprintf("%s: %s%s", key($content), end($content), $lineBreak);
        }


        /**
         * @method sendCommand
         * @param string command
         * @param int validCode
         * @return boolean
         * @throws exceptions
         * @print to console on debug mode
         */
        private function sendCommand($command, $validCode)
        {
            #Send Command with line breaks
            fputs($this->smtp, $command . PHP_EOL);

            #Read response string
            $response = fgets($this->smtp, 512); 

            #Response code
            $responseCode = substr(trim($response), 0, 3);

            #Validate response
            if($responseCode != (string)$validCode)
            {
                throw new Exception($response);
            }

            #print to console later
            //print_r($response);
            return true;
        }
    }