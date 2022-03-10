<?php
    namespace Core;

    class Config
    {
        public static function load() : void
        {
            #Essentials
            define("SLASH", DIRECTORY_SEPARATOR); #Define a dynamic directory separator to prevent issues in different OS systems.
            define("ROOT_DIR", dirname(__DIR__, 2)); #Go back to the root level from the current directory
            define("SRC_DIR", dirname(__DIR__, 1)); #Current source directory
            

            #Header configurations
            define("ALLOW_ANY_API_ORIGIN", true);
            define("ALLOWED_ORIGINS", []); #This is only required if the ALLOW_ANY_API_ORIGIN is false.

            #Routes
            define("DEFAULT_ROUTE", "User");

            #date
            define("CURRENT_TIMEZONE", "America/New_York");
            define("TIMESTAMP", date("Y-m-d H:i:s"));
            define("CURRENT_TIME", time());

            define("USER_AGENT_NAME", "EZAPI_FRAMEWORK"); //Agent name
            define("USER_SESSION_NAME", "EZAPI_DNRJZJXQGAEPA0BMYN5Q"); //Random
            define("USER_SESSION_EXPIRY", 15); //time in minutes. is the timout time for the cooke unless the session is extended

            define("DEBUGING_TOOLS", ["PostmanRuntime"]);//these tools will be blocked in production //PostmanRuntime
            define("IP_BLACKLIST", []);//blocked ips in production mode

            define("ALLOW_MULTI_LOGIN", true);
            define("MULTI_LOGIN_COUNT", 5); //number of devices allowed to be logged at the same time

            #Define timezone
            date_default_timezone_set(CURRENT_TIMEZONE);
        }
    }