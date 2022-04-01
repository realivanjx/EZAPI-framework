<?php
    namespace Controllers;

    use Core\Router;
    use Services\{IAuthService};
    use Core\Exceptions\ApiError;

    class User extends Router
    {
        protected IAuthService $m_authService;

        public function __construct(IAuthService $authService)
        {
            $this->m_authService = $authService;

            parent::__construct();
        }

        //Framework URL PROTOCOL terminology is domain/Class/Function or domain:port/Class name/Function name 
        //example localhost:8080/User/index


        //Call localhost:8080/user/ or localhost:8080/ or localhost:8080/user/index   to execute
        public function index() : void
        {  
            print("Welcome to " . EZENV["APP_NAME"] . " Version " . EZENV["APP_VERSION"]);
        }

        //Call localhost:8080/user/auth  to execute

        /**
         * 
         * @param object $input {usernameOrEmail: string, password: string, rememeberMe: bool}
         * @return object
         * @throws ApiError
         */
        public function Login(object $input) : void
        { 
            if(empty($input))
            {
                throw new ApiError("Invalid request body");
            }
            
            if(empty($input->usernameOrEmail))
            {
                throw new ApiError("Username or email is required");
            }

            if(empty($input->password))
            {
                throw new ApiError("password or email is required");
            }

            $response = $this->m_authService->Authenticate(
                $input->usernameOrEmail,
                $input->password,
                $input->rememberMe ?? false
            );

            $this->request->response($response);
        }

       
    }