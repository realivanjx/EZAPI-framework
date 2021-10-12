<?php
    namespace Routes;
    use \Exception;
    use Core\Router;

    class User extends Router
    {
        public function index()
        {
            $this->request->response(200, ["testing technologies" => "trust me it is fun"]);
        }
    }