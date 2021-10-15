<?php
    namespace Routes;
    use \Exception;
    use Core\Router;

    class User extends Router
    {
        public function index()
        {
            // $this->request->response(200, [
            //     "greeting" => $this->lang->translate("hello_world"),
            //     "appVersion" => EZENV["APP_VERSION"],
            //     "defaultLanguage" => EZENV["DEFAULT_LOCALE"],
            //     "supportedLanguages" => []
            // ]);

            #this is just a test
            print_r($this->lang->list());
        }
    }