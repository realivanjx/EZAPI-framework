<?php
    namespace Routes;
    use Core\Router;
    use Models\{IUserModel , MyAuthService, UserModel, IAuthService};

    //this is in progress. We must flip the key in this case
    class Test3 extends Router
    {
        protected IUserModel $_authTest;

        //Test in progress
        public function __construct(IUserModel $usermodel)
        {
            $this->_authTest = $usermodel;
        }


        //Call localhost:8080/user/ or localhost:8080/ or localhost:8080/user/index   to execute
        public function index() : void
        {            
            $this->_authTest->login((object)[]);

            die("\r\n<pre>This is a test from the parent class: " . $this->test);
        }
    }