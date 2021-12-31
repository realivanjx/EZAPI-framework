<?php
    namespace Core\Exceptions;
    use Core\Helper;
    use Core\Constant;

    class ApiError
    {
        public function __construct($errorMessage, $httpCode = 400)
        {
            #Unserialize
            if(Helper::isSerialized($errorMessage))
            {
                $errorMessage = unserialize($errorMessage);
            }

            if(!is_array($errorMessage))
            {
                $errorMessage = [Constant::MESSAGE => $errorMessage];
            }

           #Add return type Json
           header("Content-Type: application/json");  
            
           #Add HTTP response code
           http_response_code($httpCode);
           
           #Convert to object
           $response = json_encode([Constant::ERROR => $errorMessage]);
           
           #Return values
           exit($response);
        }
    }