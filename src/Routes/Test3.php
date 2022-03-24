<?php
    namespace Routes;
    use Core\Router;
    use Models\{IUserModel , MyAuthService, UserModel, IAuthService};

    //this is in progress. We must flip the key in this case
    class Test3 extends Router
    {
        protected IAuthService $_authService;
        protected IUserModel $_authTest;

        //Test in progress
        public function __construct(UserModel $usermodel, MyAuthService $valuewithConstructor)
        {
            $this->_authTest = $usermodel;
            $this->_authService =  $valuewithConstructor;
        }


        //Call localhost:8080/user/ or localhost:8080/ or localhost:8080/user/index   to execute
        public function index() : void
        {            
            echo $this->_authService->authenticate();

            die("\r\n<pre>This is a test from the parent class: " . $this->test);
        }
    }