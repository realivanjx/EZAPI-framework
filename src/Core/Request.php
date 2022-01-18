<?php
    namespace Core;
    use Exception;
    use Core\Constant;
    use Core\Core\Exceptions\ApiError;

    class Request
    {
         /**
         * List of HTTP error codes
         */
        public const httpResponseCode =  [

            // INFORMATIONAL CODES
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',

            // SUCCESS CODES
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-status',
            208 => 'Already Reported',

            // REDIRECTION CODES
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy', // Deprecated
            307 => 'Temporary Redirect',

            // CLIENT ERROR
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            495 => 'SSL Certificate Error',

            // SERVER ERROR
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out',
            505 => 'HTTP Version not supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            511 => 'Network Authentication Required',
        ];


        #Allowed HTTP methods
        private $_allowedMethods = [
            "POST",
            "GET",
            "PUT",
            "PATCH",
            "DELETE",
            "OPTIONS"
        ];

        #Allowed HTTP headers
        private $_allowedHeaders = [
            "Content-Type"
        ];

        #Allowed HTTP content types
        private $_allowedContentType = [
            "application/json",
            "application/json; charset=UTF-8",
            "application/x-www-form-urlencoded"
        ];


        /**
         * @method headers
         * This method will validate all of the headers sent by the browser and assign our desired headers.
         */
        public function headers() : void
        {
            #Validate against empty origin values while in production mode
            if(!isset($_SERVER["HTTP_ORIGIN"]) && EZENV["PRODUCTION"])
            {
                throw new ApiError (self::httpResponseCode[400]);
            }

            #Bypass HTTP_ORIGIN while in development mode by assigning the local IP address to the Origin.
            if(!isset($_SERVER["HTTP_ORIGIN"]) && !EZENV["PRODUCTION"])
            {
                $_SERVER["HTTP_ORIGIN"] = $_SERVER["REMOTE_ADDR"];
            }
            
            #Validate origins even while in development mode.
            if (!in_array($_SERVER["HTTP_ORIGIN"], ALLOWED_ORIGINS) && !ALLOW_ANY_API_ORIGIN)
            {
                throw new ApiError (self::httpResponseCode[400]);
            }

            #Validate request method
            if(isset($_SERVER["REQUEST_METHOD"]) && !in_array($_SERVER["REQUEST_METHOD"], $this->_allowedMethods))
            { 
                #method not allowed
                throw new ApiError (self::httpResponseCode[405], 405);
            }

            #Make sure content type is present while in production mode.
            if (!isset($_SERVER["CONTENT_TYPE"]) && EZENV["PRODUCTION"])
            {
                throw new ApiError (self::httpResponseCode[415], 415);
            }

            #Overwrite Content type in development mode to JSON by default.
            if (!isset($_SERVER["CONTENT_TYPE"]) && !EZENV["PRODUCTION"])
            {
                $_SERVER["CONTENT_TYPE"] =  $this->_allowedContentType[0];
            }

            #Validate content type
            if(isset($_SERVER["CONTENT_TYPE"]) && !in_array($_SERVER["CONTENT_TYPE"], $this->_allowedContentType))
            {
                throw new ApiError (self::httpResponseCode[415], 415);
            }

            #Build header array
            $headerValues = [
                "Access-Control-Allow-Origin" => $_SERVER["HTTP_ORIGIN"],
                "Access-Control-Allow-Credentials" => true,
                "ccess-Control-Allow-Methods" => implode(",", $this->_allowedMethods),
                "Access-Control-Allow-Headers" => implode(",", $this->_allowedHeaders),
                "Content-Type" => $_SERVER["CONTENT_TYPE"],
                "X-Powered-By" => EZENV["APP_NAME"],
                "app-version" => EZENV["APP_VERSION"]
            ];

            #Secure the connection with a valid SSL certificate on production.
            if(EZENV["PRODUCTION"] && EZENV["ENFORCE_SSL"] && $_SERVER["SERVER_PORT"] !== "443")
            {
                throw new ApiError (self::httpResponseCode[495], 495);
            }

            #Ensure that headers are not already sent before assigning new headers.
            if (!headers_sent()) 
            {
                #Assign headers
                foreach($headerValues as $key => $value)
                {
                    header(sprintf("%s: %s", $key, $value));
                }
            }
        }


         /**
         * @param int code 
         * @param array response
         * @return object
         * @throws ApiError
         * @see Core\Dictionary for the list of content types  or http response codes
         */
        public function jsonResponse(string $message = Constant::SUCCESS, array $response = [], int $code = 200) : void
        {
            #Validate response code
            if(!array_key_exists($code, self::httpResponseCode)) throw new ApiError (Constant::INVALID_HTTP_RESPONSE_CODE);

            #Add return type Json
            header("Content-Type: application/json");  
            
            #Add HTTP response code
            http_response_code($code);

            $apiResponse = [Constant::MESSAGE => $message];

            if(!empty($response))
            {
                $apiResponse[Constant::RESPONSE] = $response;
            }
            
            #Convert to object
            $response = json_encode($apiResponse);
            
            #Return values
            exit($response);
        }



        /**
         * @method inputJson
         * @param bool sanitize (obtional) true by default
         * @return object
         * @throws ApiError
         */
        public function jsonInput(bool $sanitize = true) : object
        {
            #Get params 
            $input = file_get_contents("php://input");

            #Decode params
            $results = json_decode($input);

            #Validate Json
            if (json_last_error() !== JSON_ERROR_NONE) 
            {
              throw new ApiError(Constant::INVALID_JSON_FORMAT);
            }

            #Sanitize values
            if($sanitize)
            {
               $results = Helper::sanitizeObject($results);
            }

            #Return object
            return $results;
        }


        /**
         * @param bool sanitize
         * @return object
         * @throws ApiError
         */
        public function getInput(bool $sanitize = false) : object
        {
            if(!isset($_GET) || empty($_GET))
            {
                throw new ApiError(Constant::INVALID_GET_PARAMETERS);
            }

            $inputGet = $_GET;

            if($sanitize)
            {
                $inputGet = Helper::sanitizeGet($inputGet);
            }

            return (object)$inputGet;
        }


        /**
         * @param bool sanitize
         * @return object
         * @throws ApiError
         */
        public function postInput(bool $sanitize = false) : object
        {
            if(!isset($_POST) || empty($_POST))
            {
                throw new ApiError(Constant::INVALID_POST_PARAMETERS);
            }

            $inputPost = $_POST;

            if($sanitize)
            {
                $inputPost = Helper::sanitizePost($inputPost);
            }

            return (object)$inputPost;
        }
    }