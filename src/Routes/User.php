<?php
    namespace Routes;
    use Core\Router;
    use Models\UserModel;

    Class User extends Router
    {

        private UserModel $m_userModel;


        public function __construct(UserModel $userModel)
        {
            $this->m_userModel = $userModel;


            parent::__construct();
        }
        

        public function index()
        {
            //Testing
            $callModelTest = $this->m_userModel->login();

            $this->request->jsonResponse(200, ["sucess" => $callModelTest]);
        }
    }


?>