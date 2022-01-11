<?php
    namespace Core;


    class Helper
    {
        /**
         * @return string
         * Attempt to retrieve the real IP address
         */
        public static function publicIP() : string
        {
            $realIP = "Invalid IP Address";

            $activeHeaders = [];

            $headers = [
                "HTTP_CLIENT_IP",
                "HTTP_PRAGMA",
                "HTTP_XONNECTION",
                "HTTP_CACHE_INFO",
                "HTTP_XPROXY",
                "HTTP_PROXY",
                "HTTP_PROXY_CONNECTION",
                "HTTP_VIA",
                "HTTP_X_COMING_FROM",
                "HTTP_COMING_FROM",
                "HTTP_X_FORWARDED_FOR",
                "HTTP_X_FORWARDED",
                "HTTP_X_CLUSTER_CLIENT_IP",
                "HTTP_FORWARDED_FOR",
                "HTTP_FORWARDED",
                "ZHTTP_CACHE_CONTROL",
                "REMOTE_ADDR" #this should be the last option
            ];

            #Find active headers
            foreach ($headers as $key)
            {
                if (array_key_exists($key, $_SERVER))
                {
                    $activeHeaders[$key] = $_SERVER[$key];
                }
            }

             #Reemove remote address since we got more options to choose from
            if(count($activeHeaders) > 1 && isset($_SERVER["REMOTE_ADDR"]))
            {
                unset($activeHeaders["REMOTE_ADDR"]);
            }

            #Pick a random item now that we have a secure way.
            $realIP = $activeHeaders[array_rand($activeHeaders)];

            #Validate the public IP
            if (filter_var($realIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
            {
                return $realIP;
            }

            return $realIP;
        }


        /**
         * @param object objectData
         * @return object
         */
        public static function sanitizeObject(object $objectData) : object
        {
            foreach ($objectData as $value) 
            {
                if (is_scalar($value)) 
                {
                    $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

                    continue;
                }
        
                sanitize($value);
            }
        
            return $objectData;
        }

         /**
         * @param array getData
         * @return array
         */
        public static function sanitizeGet(array $getData) : array
        {
            return filter_input_array(INPUT_GET, $getData, FILTER_SANITIZE_STRING);
        }


        /**
         * @param array postData
         * @return array
         */
        public static function sanitizePost(array $postData) : array
        {
            return filter_input_array(INPUT_POST, $postData, FILTER_SANITIZE_STRING);
        }

        
        /**
         * Check if a string is serialized or not
         * @method isSerialized
         * @param string $string
         * @return bool
         */
        public static function isSerialized(string $string) : bool
        {
            return (@unserialize($string) !== false);
        }

         /**
         * @method randomNumber
         * @param int length
         * @return int 
         */
        public static function randomNumber(int $length) : int
        {
            $result = null;

            for($i = 0; $i < (int) $length; $i++) 
            {
                $result .= mt_rand(0, 9);
            }

            return (int)$result;
        }


        /**
         * @method getUserAgent
         * @return string currrent user agent
         * 
         * note: this will get current agent info.
         */
        public static function getUserAgent() : string
        {
            return preg_replace('/\/[a-zA-Z0-9.]+/', '', $_SERVER['HTTP_USER_AGENT']);
        }
    }