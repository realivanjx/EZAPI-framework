<?php
    namespace Routes;
    use Core\Router;
    use Models\{ISuperAuthService};
    use Models\Service\IServiceTest;

use function Core\EZENV;

    class User extends Router
    {
        protected ISuperAuthService $_authService;
        protected IServiceTest $_authTest;

        public function __construct(ISuperAuthService $authService, IServiceTest $authService2)
        {
            $this->_authService = $authService;

            $this->_authTest = $authService2;

        }

        //Framework URL PROTOCOL terminology is domain/Class/Function or domain:port/Class name/Function name 
        //example localhost:8080/User/index


        //Call localhost:8080/user/ or localhost:8080/ or localhost:8080/user/index   to execute
        public function index() : void
        {            
            print("Welcome to " . EZENV["APP_NAME"] . " Version " . EZENV("APP_VERSION")); //ezenv can be used in both ways
            echo $this->_authService->authenticate();

            die("\r\n<pre>This is a test from the parent class: " . $this->test);
        }

        //Call localhost:8080/user/auth  to execute
        public function auth(object $post) : void
        {
            
            echo $this->_authTest->authenticate2();

            die("\r\n<pre>This is a test from the parent class: " . $this->test);
        }

       
    }