<?php
    namespace Routes;
    use Exception;
    use Core\Router;
    use Core\Constant;
    use Models\UserModel;
    use Core\Exceptions\ApiError;

    use Core\Lang\Translator;

    Class User extends Router
    {

        private UserModel $m_userModel;


        public function __construct(UserModel $userModel)
        {
            $this->m_userModel = $userModel;

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
            $this->request->jsonResponse(Constant::SUCCESS, [
                "greeting" => $this->lang->translate("hello_world"),
                "version" => EZENV["APP_VERSION"],
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
            $this->request->jsonResponse(Constant::SUCCESS, [
                "defaultLocale" =>  EZENV["DEFAULT_LOCALE"],
                "currentLocale" => $this->lang->currentLocale(),
                "supportedLocales" => $this->lang->list()
            ]);
        }


        /**
         * @method POST object request
         * @param object ex: {locale: value}
         * @return object
         * @throws ApiError exceptions
         * @example URL: http://localhost/user/setLocale
         * @note Call this route to change the current locale. If the user is logged 
         * it will also update the user's preference in the database.
         * @todo Update the databse
         */
        public function setLocale() : void
        {
            #Receive params and sanitize them.
            $input = $this->request->jsonInput();

            #Change language locale
            if(!$this->lang->setLocale($input->locale))
            {
                throw new ApiError (Constant::UNABLE_TO_SET_LOCALE);
            }

            //Update the databse 

            #Success response
            $this->request->jsonResponse();
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
            $inputObject = $this->request->jsonInput();

            #Attempt to register
            $response = $this->m_userModel->register($inputObject);

            #OTP sent
            if($response === Constant::OTP_SENT)
            {
                $this->request->jsonResponse(Constant::SUCCESS, ["OTP" => $this->lang->translate("OTP_SENT")]);
            }

            #Success response
            $this->request->jsonResponse(200, [Constant::MESSAGE => $response]);
        }


        /**
         * @method POST object request
         * @param object 
         * @return object
         * @example URL: http://localhost/user/login
         * @see the postman collection for a post object request example.
         */
        public function login() : void
        {
            #Receive params and sanitize them.
            $inputObject = $this->request->jsonInput();

            #Attempt to login
            $response = $this->m_userModel->login($inputObject);

            if($response === Constant::OTP_SENT)
            {
                $this->request->jsonResponse(Constant::SUCCESS, ["OTP" => $this->lang->translate("OTP_SENT")]);
            }

            #Success response
            $this->request->jsonResponse();
        }


        /**
         * @method GET request
         * @return object
         * @throws ApiError
         * @example URL: http://localhost/user/info
         * @see the postman collection for a post object request example.
         */
        public function info() : void
        {
            #Should always be true
            if(!$this->m_userModel->isLogged())
            {
                throw new ApiError (Constant::ERROR_MESSAGE);
            }

            $response = $this->m_userModel->info();

            $this->request->jsonResponse(Constant::SUCCESS, $response);
        }

        /**
         * @method GET
         * @return object 
         * @throws ApiError
         * @example URL: http://localhost/user/logout
         * @see the postman collection for a post object request example.
         */
        public function logout() : void
        {
            if($this->m_userModel->logout())
            {
                #response
                $this->request->jsonResponse();
            }

            #return a fancy error
            throw new ApiError (Constant::ERROR_MESSAGE);
        }


        /**
         * @method GET
         * @return object 
         * @throws ApiError
         * @example URL: http://localhost/user/extend
         * @see the postman collection for a post object request example.
         */
        public function extend() : void
        {
            if($this->m_userModel->extendAuth())
            {
                #response
                $this->request->jsonResponse();
            }

            #return a fancy error
            throw new ApiError (Constant::ERROR_MESSAGE);
        } 


        /**
         * @method POST
         * @return object
         * @throws ApiError
         * @example URL: http://localhost/user/extend
         * @see the postman collection for a post object request example.
         */
        public function update() : void
        {
            if(!$this->m_userModel->isLogged())
            {
                throw new ApiError (Constant::ERROR_MESSAGE);
            }

            #Receive params and sanitize them before next step
            $inputObject = $this->request->jsonInput();

            #Validate required params
            if(!$this->m_userModel->updateUser($inputObject))
            {
                throw new ApiError (Constant::ERROR_MESSAGE);
            }

            #response
            $this->request->jsonResponse();
        }


         /**
         * @method resetPsw
         * @param object 
         * @return object
         * @throws exception
         * comment: this route works for both guest and logged users.
         * for guest users a token will be sent to their email. call this route with {username_or_email: value} to get a token
         * once you get a token you can call this route with all the fields to reset the password ex: 
         * {username_or_email: string value, token: string value, password: string value, confirmPassword: string value}
         * 
         * if the user is logged you can call this route without the token but instead a current password is requried ex
         * {currentPassword: string value, password: string value, confirmPassword: string value}
         */
        public function resetPsw() : void
        {
            #Receive params and sanitize them
            $inputObject = $this->request->jsonInput();
        
            #Call reset psw model
            $resetPSWResp = $this->m_userModel->resetPsw($inputObject);

            #OTP SENT
            if($resetPSWResp == "OTP")
            {
                $this->request->jsonInput(
                    Constant::SUCCESS, 
                    ["otp" =>  $this->lang->translate("OTP_SENT")]
                );
            }

            #Password reset successful
            $this->request->jsonInput();
        }
    }
?>