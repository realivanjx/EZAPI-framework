<?php
    namespace Models\Service;
    use Core\Model;
    use Models\IUserModel;

    class ServiceTest extends Model implements IServiceTest
    {
        private IUserModel $_userModel;

        public function __construct(IUserModel $userModel)
        {
            $this->_userModel = $userModel;
        }

        public function authenticate2() : string
        {
            print("\r\n<pre>This is a test from the parent class: " . $this->test . "\r\n<pre>");

            return $this->_userModel->login((object)[]);
        }
    }

?>