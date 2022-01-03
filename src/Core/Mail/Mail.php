<?php
    namespace Core\Mail;
    use Core\Mail\Mailer;
    use Core\lang\Translator;
    use Core\HtmlCompiler;
    use Core\Constant;


    class Mail
    { 
        #Instance objects
        private  
            $smtp, 
            $lang;

        #The default html template name. This is case sensitive
        private string $htmlTemplate = "DefaultTemplate";

        #Holds the parameters taht will be assigned to the html template.
        private array $htmlParameters = [];
        
        #Constructor
        public function __construct()
        {
            #Create new instances
            $this->smtp = EZENV["USE_STMP"] ? new Smtp() : new Mailer();
            
            $this->lang = new Translator();

            #Get the current langauge config
            $currentLanguageInfo = $this->lang->info();

            #Assign default config.
            $this->htmlParameters = [
                "locale" => $currentLanguageInfo["locale"],
                "charset" => $currentLanguageInfo["charset"],
                "year"=> date("Y")
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
            #Add the subject to the email
            $this->smtp->subject = $subject;

            #add the name and to who it is going
            $this->smtp->to = [$name => $to];

            #Templates folder location.
            $templatePath = sprintf("%s%sMail%sTemplates%s%s.html", dirname(__DIR__), SLASH, SLASH, SLASH, $this->htmlTemplate);

            #Add the body to the email. In this case we are compiling html code
            $this->smtp->body =  HtmlCompiler::run($templatePath, $this->htmlParameters);

            #Send the email
            if(!$this->smtp->send())
            {
                throw new exception(Constant::UNABLE_TO_SEND);
            }
        }


        /**
         * @method sendOTP
         * @param string name
         * @param string email
         * @param int otp
         */
        public static function sendOTP(string $name, string $email, int $otp) : void
        { 
            #Fill the title parameter
            $this->htmlParameters["title"] = sprintf("<h3>%s <strong>%s,</strong></h3>",
                $this->lang->translate("hello"), 
                $name
            );

            #Fill the preheader parameter
            $this->htmlParameters["preHeader"] = $this->lang->translate("below_is_your_code");

            #Fill the body parameter
            $this->htmlParameters["body"] = sprintf(
                "<p>%s</p><br><h2><b>%s</b></h2><br><p>%s</p>", 
                $this->lang->translate("below_is_your_code"), 
                $otp, 
                $this->lang->translate("if_you_are_having_trouble")
            );

            #Send email
            $this->send(
                $this->lang->translate("verification_code"), 
                $email, 
                $name
            );
        }
    }