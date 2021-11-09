<?php
    namespace Routes;
    use Core\Router;
    use Core\Constant;
    use Core\Exceptions\ApiError;
    use Models\UserModel;
  

    class User extends Router
    {
        public function __construct()
        {
            parent::__construct();
        }


        /**
         * 
         */
        public function index() : void
        {
            $this->request->response(200, [
                "greeting" => $this->lang->translate("hello_world"),
                "appVersion" => EZENV["APP_VERSION"],
                "poweredBy" => EZENV["APP_NAME"]
            ]);
        }


        /**
         * 
         */
        public function locale() : void
        {
            $this->request->response(200, [
                "defaultLocale" =>  EZENV["DEFAULT_LOCALE"],
                "currentLocale" => $this->lang->currentLocale(),
                "supportedLocales" => $this->lang->list()
            ]);
        }


        /**
         * 
         */
        public function setLocale() : void
        {
            #Receive params and sanitize them.
            $input = $this->request->inputJson(true);

            #Change language locale
            if(!$this->lang->setLocale($input->locale))
            {
                throw new apiError (Constant::UNABLE_TO_SET_LOCALE);
            }

            #Success response
            $this->request->response(200, [Constant::MESSAGE => Constant::SUCCESS]);
        }


        /**
         * @method register
         * @param object ex:  {fname: value, lname: value, username: value, email: value, password: value, confirmPassword: value}
         * @return object
         * @throws exceptions
         */
        public function register() : void 
        {
            #Receive params and sanitize them.
            $inputObject = $this->request->inputJson(true);

            #Attempt to register
            $response = $this->di->UserModel->register($inputObject);

            #OTP sent
            if($response === Constant::OTP_SENT)
            {
                $this->request->response(200, ["OTP" => $response]);
            }

            #Success response
            $this->request->response(200, [Constant::MESSAGE => $response]);
        }


        #in progress
        public function activateAccount() : void {}


        public function login() : void 
        {
            #Receive params and sanitize them.
            $inputObject = $this->request->inputJson(true);

            $response = $this->di->UserModel->login($inputObject);

           print_r($input);
        }
        public function logout() : void {}
        public function info() : void {}
        
        public function resetPsw() : void {}
        public function update() : void {}
        public function extend() : void {}
        public function activate() : void {}


    }