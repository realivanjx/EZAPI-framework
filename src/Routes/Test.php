<?php
    namespace Routes;
    use Core\Router;
    use Models\{IUserModel , MyAuthService, UserModel, IAuthService};

    //NO DI test
    class Test extends Router
    {
        protected IAuthService $_authService;
        protected IUserModel $_authTest;

        public function __construct(IUserModel $userModel, IAuthService $authService)
        {
            $this->_authTest = $userModel;
           // $this->_authService =  new MyAuthService($this->_authTest);
           $this->_authService =  $authService;
        }

        //Framework URL PROTOCOL terminology is domain/Class/Function or domain:port/Class name/Function name 
        //example localhost:8080/User/index


        //Call localhost:8080/user/ or localhost:8080/ or localhost:8080/user/index   to execute
        public function index() : void
        {            
            echo $this->_authService->authenticate();

            die("\r\n<pre>This is a test from the parent class: " . $this->test);
        }

        

       
    }