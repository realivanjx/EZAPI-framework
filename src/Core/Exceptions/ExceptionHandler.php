<?php
    namespace Core\Exceptions;
    use \Exception;
    use Core\Request;

    class ExceptionHandler extends Exception
    {
        public $request;

        public function __construct()
        {
            #Construct original exception class
            parent::__construct();

            $this->request = new Request();
        }
        
    }