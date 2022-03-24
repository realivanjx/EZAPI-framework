<?php
    namespace Models;
    use Core\Model;
    use Models\IUserModel;

    class MyAuthService extends Model implements IAuthService
    {
        private IUserModel $_userModel;

        public function __construct(IUserModel $userModel)
        {
            $this->_userModel = $userModel;
        }

        public function authenticate() : string
        {
            print("\r\n<pre>This is a test from the parent class: " . $this->test . "\r\n<pre>");

            return $this->_userModel->login((object)[]);
        }
    }

?>