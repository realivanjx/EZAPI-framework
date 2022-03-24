<?php
    namespace Routes;
    use Core\Router;
    use Models\IAuthService;

    class User extends Router
    {
        protected $_authService;

        public function __construct(IAuthService $authService)
        {
            $this->_authService = $authService;
        }

        public function index() : void
        {
            echo $this->_authService->login();
        }

       
    }