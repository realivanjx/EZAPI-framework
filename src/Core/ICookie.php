<?php
    namespace Core;

    interface ICookie
    {
        static function set(
            string $name,
            string $value,
            int $cookieExpiration, 
            string $path = "/", 
            string $domain = "", 
            bool $secure = false, 
            bool $httpOnly = true ) : bool;

        static function get(string $name) : string;

        static function exists(string $name) : bool;

        static function delete(string $name) : bool;

        static function deleteAll(string $skip = null) : void;
    }
?>