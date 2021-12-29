<?php
    namespace Core;
    use \Exception;

    class Request
    {

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
            // if(!isset($_SERVER["HTTP_ORIGIN"]) && EZENV["PRODUCTION"])
            // {
            //     $this->jsonResponse(400, ["error"=> "bad request"]);
            // }

            // #Bypass HTTP_ORIGIN while in development mode by assigning the local IP address to the Origin.
            // if(!isset($_SERVER["HTTP_ORIGIN"]) && !EZENV["PRODUCTION"])
            // {
            //     $_SERVER["HTTP_ORIGIN"] = $_SERVER["REMOTE_ADDR"];
            // }
            $_SERVER["HTTP_ORIGIN"] = $_SERVER["REMOTE_ADDR"];


            #Validate origins even while in development mode.
            if (!in_array($_SERVER["HTTP_ORIGIN"], ALLOWED_ORIGINS) && !ALLOW_ANY_API_ORIGIN)
            {
                $this->jsonResponse(400, ["error"=> "bad request"]);
            }

            #Validate request method
            if(isset($_SERVER["REQUEST_METHOD"]) && !in_array($_SERVER["REQUEST_METHOD"], $this->_allowedMethods))
            { 
                #method not allowed
                $this->jsonResponse(405, ["error"=> "Method not allowed"]);
            }

            #Make sure content type is present while in production mode.
            // if (!isset($_SERVER["CONTENT_TYPE"]) && EZENV["PRODUCTION"])
            // {
            //     $this->jsonResponse(415, ["error"=> "Content type not allowed"]);
            // }

            // #Overwrite Content type in development mode to JSON by default.
            // if (!isset($_SERVER["CONTENT_TYPE"]) && !EZENV["PRODUCTION"])
            // {
            //     $_SERVER["CONTENT_TYPE"] =  $this->_allowedContentType[0];
            // }
            $_SERVER["CONTENT_TYPE"] =  $this->_allowedContentType[0];

            #Validate content type
            if(isset($_SERVER["CONTENT_TYPE"]) && !in_array($_SERVER["CONTENT_TYPE"], $this->_allowedContentType))
            {
                $this->jsonResponse(415, ["error"=> "Content type not allowed"]);
            }

            #Build header array
            $headerValues = [
                "Access-Control-Allow-Origin" => $_SERVER["HTTP_ORIGIN"],
                "Access-Control-Allow-Credentials" => true,
                "ccess-Control-Allow-Methods" => implode(",", $this->_allowedMethods),
                "Access-Control-Allow-Headers" => implode(",", $this->_allowedHeaders),
                "Content-Type" => $_SERVER["CONTENT_TYPE"],
                // "X-Powered-By" => EZENV["APP_NAME"],
                // "app-version" => EZENV["APP_VERSION"]
            ];

            #Secure the connection with a valid SSL certificate on production.
            // if(EZENV["PRODUCTION"] && EZENV["ENFORCE_SSL"] && $_SERVER["SERVER_PORT"] !== "443")
            // {
            //     $this->jsonResponse(495, ["error"=> "SSL must be enabled"]);
            // }

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
         * @method response
         * @param int code 
         * @param array response
         * @param string contentType 
         * @return object
         * @throws Exceptions
         * @see Core\Dictionary for the list of content types  or http response codes
         */
        public function jsonResponse(int $code, array $response) : void
        {

            #Validate response code
            // if(!array_key_exists($code, Dictionary::httpResponseCode)) throw new Exception (Constant::INVALID_HTTP_RESPONSE_CODE);

            // #Validate content type
            // if(!array_key_exists($contentType, Dictionary::contentType)) throw new Exception (Constant::INVALID_CONTENT_TYPE);

            #Add return type Json
            header(sprintf("Content-Type: %s", "application/json"));  
            
            #Add HTTP response code
            http_response_code($code);

            
            #Encode values
            $response = json_encode($response);
           
            
            #Return values
            exit($response);
        }



        /**
         * @method inputJson
         * @param bool sanitize (obtional) false by default
         * @return object
         * @throws Exception
         */
        public function jsonInput(bool $sanitize = false) : object
        {
            #Get params 
            $input = file_get_contents("php://input");

            #Decode params
            $results = json_decode($input);

            #Validate Json
            if (json_last_error() !== JSON_ERROR_NONE) 
            {
               // throw new ApiError(Constant::INVALID_JSON_FORMAT);
            }

            #Sanitize values
            if($sanitize)
            {
               // $results = Helper::sanitizeObject($results);
            }

            #Return object
            return $results;
        }
    }