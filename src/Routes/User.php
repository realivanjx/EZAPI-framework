<?php
    namespace Routes;
    use Core\Router;
    use Core\Constant;
    use Core\Exceptions\ApiError;
    use Models\UserModel;


    use Core\Mail\Mail;
  

    class User extends Router 
    {
        private UserModel $m_userModel;

        #Constructor
        public function __construct(UserModel $usermodel)
        {
            $this->m_userModel = $usermodel;

            parent::__construct();
        }


        /**
         * @method GET request
         * @return object
         * @example URL: http://localhost or http://localhost/user/ 
         * @note This is the default route.
         */
        public function index() : void
        {
            $this->request->jsonResponse(200, [
                "greeting" => $this->lang->translate("hello_world"),
                "version" => EZENV["APP_VERSION"],
                "RunningOn" => sprintf("PHP Version: %s", phpversion()),
                "poweredBy" => EZENV["APP_NAME"]
            ]);
        }


        /**
         * @method GET request
         * @return object
         * @example URL: http://localhost/user/locale
         * This routes returns the list of supported locales, default locale and current locale.
         */
        public function locale() : void
        {
            $this->request->jsonResponse(200, [
                "defaultLocale" =>  EZENV["DEFAULT_LOCALE"],
                "currentLocale" => $this->lang->currentLocale(),
                "supportedLocales" => $this->lang->list()
            ]);
        }


        /**
         * @method POST object request
         * @param object ex: {locale: value}
         * @return object
         * @throws apiError exceptions
         * @example URL: http://localhost/user/setLocale
         * @note Call this route to change the current locale. If the user is logged 
         * it will also update the user's preference in the database.
         * @todo Update the databse
         */
        public function setLocale() : void
        {
            #Receive params and sanitize them.
            $input = $this->request->jsonInput(true);

            #Change language locale
            if(!$this->lang->setLocale($input->locale))
            {
                throw new apiError (Constant::UNABLE_TO_SET_LOCALE);
            }

            //Update the databse 

            #Success response
            $this->request->jsonResponse(200, [Constant::MESSAGE => Constant::SUCCESS]);
        }









        /**
         * @method POST object request
         * @param object 
         * @return object
         * @example URL: http://localhost/user/register
         * @see the postman collection for a post object request example.
         */
        public function register() : void 
        {
            #Receive params and sanitize them.
            $inputObject = $this->request->jsonInput(true);

            #Attempt to register
            $response = $this->di->UserModel->register($inputObject);

            #OTP sent
            if($response === Constant::OTP_SENT)
            {
                $this->request->jsonResponse(200, ["OTP" => $response]);
            }

            #Success response
            $this->request->jsonResponse(200, [Constant::MESSAGE => $response]);
        }


        #in progress
        public function activateAccount() : void 
        {
            // #Receive params and sanitize them.
           // $inputObject = $this->request->inputJson(true);
            $this->di->Mail->sendOTP(
                "hose",
                "mastersoft15@gmail.com",
                "10"
            );
        }
        

        


        public function login() : void 
        {
            #Receive params and sanitize them.
            $inputObject = $this->request->jsonInput(true);

            $response = $this->di->UserModel->login($inputObject);

           print_r($input);


      
        // #If the user is logged there is no need to continue
        // if($this->di->UserModel->isLogged())
        // {
        //   $this->request->response(200, $this->di->UserModel->info());
        // }

        // #Receive params and sanitize them.
        // $post = $this->request->inputJson(true);

        // #Attempt to login
        // $response = $this->di->UserModel->login($post);
        
        // #logged in successfully
        // if($response == Constant::SUCCESS)
        // {
        //   $this->request->response(200, $this->di->UserModel->info());
        // }

        // #Invalid auth
        // throw new Exception (serialize($response));
      




        }


        public function logout() : void {}
        public function info() : void {}
        
        public function resetPsw() : void {}
        public function update() : void {}
        public function extend() : void {}
        public function activate() : void {}


    }