<?php
    namespace Src;

    class Config
    {
        public function load() : void
        {
            define("SLASH", DIRECTORY_SEPARATOR);
            define("ROOT_DIR", dirname(__DIR__, 1));
            define("SRC_DIR", dirname(__DIR__));

            define("DEFAULT_ROUTE_DIRECTORY", "Routes");
            define("DEFAULT_ROUTE", "User");

            define("PRODUCTION", false);
        }
    }