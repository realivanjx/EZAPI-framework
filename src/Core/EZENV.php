<?php
    namespace Core;
    use \SplFileObject;

    class EZENV
    {
        /**
         * @method load
         * EZENV is one of many unique features that EZAPI offers. 
         * With this feature you are able to use enviroment variables natively.
         */
        public static function load() : void
        {
            #use the production .env by default.
            $fileName = ".env.production";
            $production = true; #it is always a good idea to know in which mode we are working.

            #Validate if the server is in production or development.
            if($_SERVER["REMOTE_ADDR"] === "127.0.0.1"  || $_SERVER["REMOTE_ADDR"] === "::1" && $_SERVER["SERVER_NAME"] === "localhost")
            {
                $fileName = ".env.development";

                $production = false;
            }

            #Open the .env file and read it
            $dotEnvFile = new SplFileObject(
                sprintf("%s%s%s",
                    ROOT_DIR,
                    SLASH,
                    $fileName
                )
            );

            #contains the parse values
            $env = [];

            #read .env line by line
            while (!$dotEnvFile->eof()) 
            {
                #read line
                $line = $dotEnvFile->fgets();

                #Skip line with the # symbol
                if(substr($line, 0, 1) === "#") continue;

                #Skip line with two forward slash
                if(substr($line, 0, 1) === "//") continue;

                #Skip empty lines
                if(empty($line)) continue;

                #Skip lines without the equal sign
                if(!strpos($line, "=")) continue;

                #Convert values to array
                $splitEnv = explode("=", $line);

                #Remove white spaces
                $splitEnv[1] = trim($splitEnv[1]);

                #If the value contains string quotes remove them.
                if(substr($splitEnv[1], 0, 1) === '"')
                {
                    $splitEnv[1] = substr($splitEnv[1], 1, -1);
                }

                #add key and value to the env array
                $env[trim($splitEnv[0])] = $splitEnv[1];
            }

            #Merge arrays
            $env = array_merge($env, ["PRODUCTION" => $production]);
          
            #Add env variables
            foreach ($env as $key => $value) 
            {
                putenv(sprintf("%s=%s", $key, $value));
            }

            #Create EZENV function and return its value when called.
            if(!function_exists("EZENV")) 
            {
                function EZENV($key)
                {
                    $value = getenv($key);
          
                    if ($value === false) 
                    {
                        return "Invalid env key";
                    }
          
                    return $value;
                }
            }

            #Define the global EZENV global variable to be used as alternative way.
            define("EZENV", $env);
        }
    }