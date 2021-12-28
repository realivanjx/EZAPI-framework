<?php
    namespace Core\Exceptions;
    use Core\Exceptions\ExceptionHandler;
    use Core\Request;
    use Core\Constant;
    use Core\Helper;

    class ApiError extends ExceptionHandler
    {
        public function __construct($errorMessage, $httpCode = 400)
        {
            #Construct ExceptionHandler constructor
            parent::__construct();

             #Unserialize
            if(Helper::isSerialized($errorMessage))
            {
                $errorMessage = unserialize($errorMessage);
            }
            
            #Send Api response
            $this->request->jsonResponse($httpCode, [Constant::ERROR => $errorMessage]);
        }
    }