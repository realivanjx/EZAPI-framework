<?php
    namespace Core;


    

    class Cookie
    {

        /**
         * @method set
         * @param string name, value, cookieExpiration, path, domain. secure. 
         * @param boolean httpOnly
         * @return boolean
         */
        public static function set(
            string $name,
            string $value,
            string $cookieExpiration, 
            string $path = "/", 
            string $domain = "", 
            bool $secure = false, //set to true in deployment
            bool $httpOnly = true ) : bool
        {
            if(!empty($name) && !empty($value) && !empty($cookieExpiration)) 
            {
                #name, value, expire, path, domain, secure, httponly

                if(setcookie($name, $value, [
                    'expires' => $cookieExpiration,
                    'path' => $path,
                    'domain' => $domain,
                    'secure' => $secure,
                    'httponly' => $httpOnly,
                    'samesite' => 'None' //None || Lax  || Strict
                ])) return true;
            }

            return false;
        }


        /**
         * @method get
         * @param string name
         * @return string
         */
        public static function get(string $name) : ?string 
        {
            if(!empty($name) && self::exists($name))
            {
                return $_COOKIE[$name];
            }

            return null;
        }


        /**
         * @method exists
         * @param string name
         * @return string
         */
        public static function exists(string $name) : bool 
        {
            if(!empty($name) && isset($_COOKIE[$name]))
            {
                return true;
            }

            return false;
        }
        

        /**
         * @method delete
         * @param string name
         * @param int expire (by default 1 year ago)
         * @return boolean
         */
        public static function delete(string $name, int $expire =  365 * 24 * 60 * 60) : bool
        {
            if(!empty($name) && self::exists($name))
            {
                //Set cookie in pass time
                if(setcookie($name, "", [
                    'expires' => time() + $expire,
                    'path' => "/",
                    'domain' => "",
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'None',
                ])) return true;
            }

            return false;
        }

        
        /**
         * @method deleteAll
         * @param string skip
         * @comment this method will delete all the cookies. 
         * IF the param skip is not null it will skip the specified cookie if it exists.
         */
        public static function deleteAll(string $skip = null) : void
        {
            #A year ago
            $expiredTime = time() - 365 * 24 * 60 * 60;

            #delete all except skip
            foreach ($_COOKIE as $key => $value )
            {
                if($key != $skip)
                {
                    setcookie($key, $value, $expiredTime, '/' );
                }
            }
        }

    }