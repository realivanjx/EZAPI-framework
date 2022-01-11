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
            $this->request->jsonResponse(200, [
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
            $inputObject = $this->request->jsonInput();

            #Attempt to register
            $response = $this->m_userModel->register($inputObject);

            #OTP sent
            if($response === Constant::OTP_SENT)
            {
                $this->request->jsonResponse(200, ["OTP" => $this->lang->translate("OTP_SENT")]);
            }

            #Success response
            $this->request->jsonResponse(200, [Constant::MESSAGE => $response]);
        }


        public function login() : void
        {
            #Receive params and sanitize them.
            $inputObject = $this->request->jsonInput();

            #Attempt to login
            $response = $this->m_userModel->login($inputObject);

            if($response === Constant::OTP_SENT)
            {
                $this->request->jsonResponse(200, ["OTP" => $this->lang->translate("OTP_SENT")]);
            }

            #Success response
            $this->request->jsonResponse(200, [Constant::MESSAGE => $response]);
        }
    }
?>