<?php
    namespace Routes;
    use Core\Router;
    use Models\IUserModel;

    class User extends Router
    {
        protected $m_userModel;

        public function __construct(IUserModel $userModel)
        {
            $this->m_userModel = $userModel;
        }

        public function index() : void
        {
            die("wtfffffffffffff");
            echo $this->m_userModel->login((object)[]);
        }

       
    }