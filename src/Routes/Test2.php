<?php
    namespace Routes;
    use Core\Router;

    //NO constructor test
    class Test2 extends Router
    {
        //Call localhost:8080/user/ or localhost:8080/ or localhost:8080/user/index   to execute
        public function index() : void
        {   
            die("\r\n<pre>This is a test from the parent class: " . $this->test);
        }
    }