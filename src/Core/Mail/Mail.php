<?php
    namespace Core\Mail;
    use Core\Mail\EZMAIL\EZMAIL;
    use Core\lang\Translator;
    use Core\HtmlCompiler;
    use Core\Constant;


    class Mail
    { 
        #Instance objects
        private  
            $ezmail,
            $lang;

        #The default html template name. This is case sensitive
        private string $htmlTemplate = "Default";

        #Holds the parameters taht will be assigned to the html template.
        private array $htmlParameters = [];
        
        #Constructor
        public function __construct()
        {
            #Create new instances
            $this->ezmail = new EZMAIL();
            $this->ezmail->appName = EZENV["APP_NAME"];
            $this->ezmail->hostName = EZENV["SMTP_HOST"];
            $this->ezmail->portNumber = EZENV["SMTP_PORT"];
            $this->ezmail->username = EZENV["SMTP_USERNAME"];
            $this->ezmail->password = EZENV["SMTP_PASSWORD"];

            if(!empty(EZENV["SMTP_AUTH_TOKEN"]))
            {
                $this->ezmail->authType = 3;
                $this->ezmail->authToken = EZENV["SMTP_AUTH_TOKEN"];
            }
            
            $this->lang = new Translator();

            #Get the current langauge config
            $currentLanguageInfo = $this->lang->info();

            #Assign default config.
            $this->htmlParameters = [
                "locale" => $currentLanguageInfo["locale"],
                "charset" => $currentLanguageInfo["charset"],
                "title" => EZENV["APP_NAME"], #optional
                "header" => sprintf("<h1>%s</h1>", EZENV["APP_NAME"]), #can also be html
                "footer" => "" # Can also be html
            ];
        }  

        /**
         * @method send
         * @param string subject
         * @param string to
         * @param string name
         * @throws exception
         */
        private function send(string $subject, string $to, string $name) : void
        {
            #Templates folder location.
            $templatePath = sprintf("%s%sMail%sTemplates%s%s.html", dirname(__DIR__), SLASH, SLASH, SLASH, $this->htmlTemplate);
            
            $this->ezmail->subject = $subject;
            $this->ezmail->body = HtmlCompiler::run($templatePath, $this->htmlParameters);
            $this->ezmail->to = [$name => $to];

            #Send the email
            if(!$this->ezmail->send())
            {
                throw new exception(Constant::UNABLE_TO_SEND);
            }
        }


        /**
         * @param string name
         * @param string email
         * @param int otp
         * @return bool
         */
        public  function sendOTP(string $name, string $email, int $otp) : bool
        { 
            #Fill the preheader parameter
            $this->htmlParameters["preHeader"] = $this->lang->translate("below_is_your_code");

            #Fill the body parameter
            $this->htmlParameters["body"] = sprintf(
                "<h3>%s <strong>%s,</strong></h3><p>%s</p><br><h2><b>%s</b></h2><br><p>%s</p>", 
                $this->lang->translate("hello"), 
                $name,
                $this->lang->translate("below_is_your_code"), 
                $otp, 
                $this->lang->translate("if_you_are_having_trouble")
            );

            #Send email
            if($this->send(
                $this->lang->translate("verification_code"), 
                $email, 
                $name
            ))  return true;

            return false;
        }


        /**
         * @param string name
         * @param string email
         * @return bool
         */
        public function sendWelcomeEmail(string $name, string $email) : bool
        {
            #Fill the preheader parameter
            $this->htmlParameters["preHeader"] = $this->lang->translate("your_account_has_been_created");

            #Fill the body parameter
            $this->htmlParameters["body"] = sprintf(
                "<h3>%s <strong>%s,</strong></h3><p>%s</p><br><p>%s</p>", 
                $this->lang->translate("hello"),
                $name,
                $this->lang->translate("your_account_has_been_created"),
                $this->lang->translate("if_you_are_having_trouble")
            );
 
            #Send email
            if($this->send(
                sprintf("%s %s", $this->lang->translate("welcome_to"), EZENV["APP_NAME"]),
                $email, 
                $name
            )) return true;

            return false;
        }


        public function newDeviceNotification($name, $email, $userIp, $userAgent) : bool
        {

        }
    }