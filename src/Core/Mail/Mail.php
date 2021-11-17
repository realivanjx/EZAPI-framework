<?php
    namespace Core\Mail;
    use Core\Mail\Mailer;
    use Core\Languages\Translator;
    use Core\HtmlCompiler;


    class Mail
    { 
        private object $smtp, $lang;

        private string $htmlTemplate = "";

        private array $htmlParameters = [];
        

        public function __contruct()
        {
            $this->smtp = new Mailer();
            $this->lang = new Translator();

            $currentLanguageInfo = $this->lang->info();

            $this->htmlParameters = [
                "locale" => $currentLanguageInfo["locale"],
                "charset" => $currentLanguageInfo["charset"],
                "year"=> date("Y")
            ];
        }  


        private function send(string $subject, string $to, string $name)
        {
            $this->smtp->subject = $subject;
            $this->smtp->to = [$name => $to];
            $this->smtp->body =  HtmlCompiler::run($this->htmlTemplate, $this->htmlParameters);

            $confirm = $this->smtp->send();
        }

        public  function sendOTP(string $name, string $email, int $otp) : void
        { 
            #Fill the title parameter
            $this->htmlParameters["title"] = sprintf("<h3>%s <strong>%s,</strong></h3>", $this->lang->translate("hello"), $name);

            #Fill the preheader parameter
            $this->htmlParameters["preHeader"] = $this->lang->translate("below_is_your_code");

            #Fill the body parameter
            $this->htmlParameters["body"] = sprintf("<p>%s</p><br><h2><b>%s</b></h2><br><p>%s</p>", 
                $this->lang->translate("below_is_your_code"), 
                $otp, 
                $this->lang->translate("if_you_are_having_trouble")
            );

            #Send email
            $this->send($this->lang->translate("verification_code"), $email, $name);
        }
    }