<?php
    namespace Core\Exceptions;
    use Core\Exceptions\ExceptionHandler;
    use Core\Request;
    use Core\Constant;

    class ApiError extends ExceptionHandler
    {
        public function __construct($errorMessage, $httpCode = 400)
        {
            #Construct ExceptionHandler constructor
            parent::__construct();
            
            #Send Api response
            $this->request->response($httpCode, [Constant::ERROR => $errorMessage]);
        }
    }