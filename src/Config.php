<?php

    class Config
    {
        

        public static function load() : void
        {
            $configSet = [

                #Essentials
                "SLASH" => DIRECTORY_SEPARATOR, #Define a dynamic directory separator to prevent issues in different OS systems.
                "ROOT_DIR" => dirname(__DIR__, 1), #Go back to the root level from the current directory
                "SRC_DIR" => dirname(__DIR__), #Current source directory

                #Header configurations
                "ALLOW_ANY_API_ORIGIN" => true,
                "ALLOWED_ORIGINS" => [], #This is only required if the ALLOW_ANY_API_ORIGIN is false.

                #Routes
                "DEFAULT_ROUTE" => "User",

                "CURRENT_TIMEZONE" => "America/New_York",
            ];


            foreach($configSet as $key => $value)
            {
                define($key, $value);
            }

            #Define timezone
            date_default_timezone_set(CURRENT_TIMEZONE);
        }
    }
