<?php
    namespace Core\Exceptions;

    class ApiError extends ExceptionHandler
    {
        public function __construct($errorMessage, $httpCode = 400)
        {
            
        }
    }