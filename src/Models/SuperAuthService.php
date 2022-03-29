<?php
    namespace Models;
    use Core\Model;
    use Models\IAuthService;

    class SuperAuthService extends Model implements ISuperAuthService, IUserModel
    {
        private IAuthService $_authService;

        public function __construct(IAuthService $authService)
        {
            $this->_authService = $authService;
        }

        public function authenticate() : string
        {
            print("\r\n<pre>This is a test from the super auth service: " . $this->test . "\r\n<pre>");

            return $this->_authService->authenticate() + " super!";
        }

        public function login(object $input) : string
        {
            return "Hello from the user model login function";
        }

        public function register(object $input) : string
        {
            return "register";
        }
    }

?>