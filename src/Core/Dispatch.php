<?php
    namespace Core;
    use ReflectionClass;
    use Exception;
    use ArgumentCountError;
    use Src\{Mapper, Config};
    

    class Dispatch
    {
        private array $map = [];

        /**
         * @method request
         * This is the entry point of our application.
         */
        public function request() : void
        {
            #Load app config
            Config::load();

            #Load EZENV config
            EZENV::load(PRODUCTION);


            #Get the path info from the browser
            $pathInfo = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['ORIG_PATH_INFO'];

            #Convert path into array and remove the last forward slash
            $request = preg_split("#/#", ltrim($pathInfo, "/"));

             /**
             * After a request is made we will get the index of the array if it is not empty
             * and try to find a class with this name. If the index is empty we will then use the 
             * default route defined in the config file. 
             * 
             * please note that Route is a folder in src and the backslash cannot be changed for any reason
             * this is case sensitive because we are autoloading the classes.
             */
            $route =  sprintf("Routes\%s", 
                empty($request[0]) && empty($request[1]) ?
                ucfirst(DEFAULT_ROUTE) : ucfirst($request[0])
            );

            
            /**
             * If the 2nd object in the array $request is not empty we will use that as default method.
             * If the 2nd object in the array is empty we will get the index object otherwise if both
             * index and 2nd objects are empty we will by default use index.
             */    
            $method = !empty($request[0]) && !empty($request[1]) ? $request[1] : 'index';

            #See if parameters are sent with a get request
            if (strpos($method, "?")) 
            {
                #Remove everything after the question marks since they are parameters.
                $method = strtok($method, "?");
            }
            

            /*
            * If the class is not found or if the method does not exist in that class
            * We will return error code 404.
            */
            if(!class_exists($route) || !method_exists($route, $method))
            {
                 die("error 404");
                //throw new ApiError(Dictionary::httpResponseCode[404]);
            }

             /**
             * If all validations are passed We will pass the params to the method requested
             * and trigger the method as a new instance.
             */

            $ref  = new ReflectionClass($route);

            $this->recursiveParams($route);

            if(count($this->map)) 
            {
                $routeInstance = $ref->newInstanceArgs($this->map);
            }
            else
            {
                $routeInstance = $ref->newInstance();
            }

            #Execute
            $routeInstance->$method();
        }
        

        private function recursiveParams(string $routeClass) 
        {
            $ref  = new ReflectionClass($routeClass);
            $constructor = $ref->getConstructor();

            if(is_null($constructor)) return;
               
            $parameters = $constructor->getParameters();

            foreach ($parameters as $paramer) 
            {
                $classToInject = $paramer->getType()->getName();

                if(empty($classToInject) || $paramer->isOptional()) continue;  

                if(interface_exists($classToInject))
                {
                    if(!array_key_exists($classToInject, Mapper::$map))
                    {
                        throw new Exception("Interface class not mapped: " . $classToInject);
                    }

                    $classToInject = Mapper::$map[$classToInject];
                }

                try
                {
                    $this->map[] =  new $classToInject;
                }
                catch(ArgumentCountError)
                {
                    $args = $this->getParamList($classToInject);

                    $ref  = new ReflectionClass($classToInject);

                    $this->map[] =  $ref->newInstanceArgs($args);
                }
            }
        }

        private function getParamList(string $className) : array
        {
            $params = [];

            $ref  = new ReflectionClass($className);
            $constructor = $ref->getConstructor();

            $parameters = $constructor->getParameters();

            foreach ($parameters as $paramer) 
            {
                $classToInject = $paramer->getType()->getName();

                if(empty($classToInject) || $paramer->isOptional()) continue;  

                if(interface_exists($classToInject))
                {
                    if(!array_key_exists($classToInject, Mapper::$map))
                    {
                        throw new Exception("Interface class not mapped: " . $classToInject);
                    }

                    $classToInject = Mapper::$map[$classToInject];
                }

                try
                {
                    $params[] =  new $classToInject;
                }
                catch(ArgumentCountError)
                {
                    throw new Exception("Only one level nested supported");
                }
            }

            return $params;

        }
    }
  