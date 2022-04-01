<?php
    namespace Src;

    class Config
    {
        public function load() : void
        {
            define("SLASH", DIRECTORY_SEPARATOR);
            define("ROOT_DIR", dirname(__DIR__, 1));
            define("SRC_DIR", dirname(__DIR__));

            define("DEFAULT_ROUTE_DIRECTORY", "Controllers");
            define("DEFAULT_ROUTE", "User");

            #Header configurations
            define("ALLOW_ANY_API_ORIGIN", true);
            define("ALLOWED_ORIGINS", []); #This is only required if the ALLOW_ANY_API_ORIGIN is false.

            
            #Define timezone
            define("TIMESTAMP", "Y-m-d H:i:s");
            define("CURRENT_TIMEZONE", "America/New_York");
            date_default_timezone_set(CURRENT_TIMEZONE);

            define("PRODUCTION", false);
        }
    }