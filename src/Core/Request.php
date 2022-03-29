<?php
    namespace Core;

    class Request implements IRequest
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
            // #Validate against empty origin values while in production mode
            // if(!isset($_SERVER["HTTP_ORIGIN"]) && EZENV["PRODUCTION"])
            // {
            //     print_r($_SERVER["REMOTE_ADDR"]); die;
            //     $this->jsonResponse(400, [Constant::ERROR => Dictionary::httpResponseCode[400]]);
            // }

            // #Bypass HTTP_ORIGIN while in development mode by assigning the local IP address to the Origin.
            // if(!isset($_SERVER["HTTP_ORIGIN"]) && !EZENV["PRODUCTION"])
            // {
            //     $_SERVER["HTTP_ORIGIN"] = $_SERVER["REMOTE_ADDR"];
            // }
            
            // #Validate origins even while in development mode.
            // if (!in_array($_SERVER["HTTP_ORIGIN"], ALLOWED_ORIGINS) && !ALLOW_ANY_API_ORIGIN)
            // {
            //     $this->jsonResponse(400, [Constant::ERROR => Dictionary::httpResponseCode[400]]);
            // }

            // #Validate request method
            // if(isset($_SERVER["REQUEST_METHOD"]) && !in_array($_SERVER["REQUEST_METHOD"], $this->_allowedMethods))
            // { 
            //     #method not allowed
            //     $this->jsonResponse(405, [Constant::ERROR => Dictionary::httpResponseCode[405]]);
            // }

            // #Make sure content type is present while in production mode.
            // if (!isset($_SERVER["CONTENT_TYPE"]) && EZENV["PRODUCTION"])
            // {
            //     $this->jsonResponse(415, [Constant::ERROR => Dictionary::httpResponseCode[415]]);
            // }

            // #Overwrite Content type in development mode to JSON by default.
            // if (!isset($_SERVER["CONTENT_TYPE"]) && !EZENV["PRODUCTION"])
            // {
            //     $_SERVER["CONTENT_TYPE"] =  $this->_allowedContentType[0];
            // }

            // #Validate content type
            // if(isset($_SERVER["CONTENT_TYPE"]) && !in_array($_SERVER["CONTENT_TYPE"], $this->_allowedContentType))
            // {
            //     die($_SERVER["CONTENT_TYPE"]);
            //     $this->jsonResponse(415, [Constant::ERROR => Dictionary::httpResponseCode[415]]);
            // }

            // #Build header array
            // $headerValues = [
            //     "Access-Control-Allow-Origin" => $_SERVER["HTTP_ORIGIN"],
            //     "Access-Control-Allow-Credentials" => true,
            //     "ccess-Control-Allow-Methods" => implode(",", $this->_allowedMethods),
            //     "Access-Control-Allow-Headers" => implode(",", $this->_allowedHeaders),
            //     "Content-Type" => $_SERVER["CONTENT_TYPE"],
            //     "X-Powered-By" => EZENV["APP_NAME"],
            //     "app-version" => EZENV["APP_VERSION"]
            // ];

            // #Secure the connection with a valid SSL certificate on production.
            // if(EZENV["PRODUCTION"] && EZENV["ENFORCE_SSL"] && $_SERVER["SERVER_PORT"] !== "443")
            // {
            //     $this->response(495,  [Constant::ERROR => Dictionary::httpResponseCode[495]]);
            // }

            // #Ensure that headers are not already sent before assigning new headers.
            // if (!headers_sent()) 
            // {
            //     #Assign headers
            //     foreach($headerValues as $key => $value)
            //     {
            //         header(sprintf("%s: %s", $key, $value));
            //     }
            // }
        }


        public function data() : mixed
        {
            $this->headers();

            switch ($_SERVER["REQUEST_METHOD"])
            {
                case "GET" : 
                    return (object)$_GET;

                case "POST" or "PUT" or "DELETE" or "PATCH": 
                    return $this->body();

                default:
                    return null;

            }
        }

        private function body() : mixed
        {
            if(!empty($_POST)) return (object)$_POST;

            #Get params 
            $input = file_get_contents("php://input");

            #Decode params
            $results = json_decode($input);

            #Validate Json
            if (json_last_error() === JSON_ERROR_NONE)
            {
                return $results;
            }
            
            #Anything other than json
            return $input;     
        }
    }