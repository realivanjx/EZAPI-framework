<?php
    namespace Routes;
    use Core\Router;
    use Models\{IAuthService};

    class User extends Router
    {
        protected IAuthService $_authService;

        public function __construct(IAuthService $authService)
        {
            $this->_authService = $authService;

        }

        //Framework URL PROTOCOL terminology is domain/Class/Function or domain:port/Class name/Function name 
        //example localhost:8080/User/index


        //Call localhost:8080/user/ or localhost:8080/ or localhost:8080/user/index   to execute
        public function index() : void
        {            
            print("Welcome to " . EZENV["APP_NAME"] . " Version " . EZENV["APP_VERSION"]);
        }

        //Call localhost:8080/user/auth  to execute
        public function auth(object $post) : void
        {
            $response = $this->_authService->authenticate($post);

            print($response);
        }

       
    }