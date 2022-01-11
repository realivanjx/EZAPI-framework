<?php
    namespace Core;

    class Config
    {
        public static function load() : void
        {
            $configSet = [

                #Essentials
                "SLASH" => DIRECTORY_SEPARATOR, #Define a dynamic directory separator to prevent issues in different OS systems.
                "ROOT_DIR" => dirname(__DIR__, 2), #Go back to the root level from the current directory
                "SRC_DIR" => dirname(__DIR__, 1), #Current source directory
                

                #Header configurations
                "ALLOW_ANY_API_ORIGIN" => true,
                "ALLOWED_ORIGINS" => [], #This is only required if the ALLOW_ANY_API_ORIGIN is false.

                #Routes
                "DEFAULT_ROUTE" => "User",

                #date
                "CURRENT_TIMEZONE" => "America/New_York",
                "TIMESTAMP" => date("Y-m-d H:i:s"),
                "CURRENT_TIME" => time(),

                "USER_AGENT_NAME" => "EZAPI_FRAMEWORK", //Agent name
                "USER_SESSION_NAME" => "EZAPI_DNRJZJXQGAEPA0BMYN5Q", //Random
                "USER_SESSION_EXPIRY" => 15, //time in minutes. is the timout time for the cooke unless the session is extended

                "DEBUGING_TOOLS" => ["PostmanRuntime"],//these tools will be blocked in production //PostmanRuntime
                "IP_BLACKLIST" => [],//blocked ips in production mode

                "ALLOW_MULTI_LOGIN" => true,
                "MULTI_LOGIN_COUNT" => 5 //number of devices allowed to be logged at the same time

            ];


            foreach($configSet as $key => $value)
            {
                define($key, $value);
            }

            #Define timezone
            date_default_timezone_set(CURRENT_TIMEZONE);
        }
    }