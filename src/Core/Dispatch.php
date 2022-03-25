<?php
    namespace Core;
    use \ReflectionClass;
    use \Exception;
    use Src\Mapper;
    use Src\Config;
    use \ArgumentCountError;

    class Dispatch
    {
        /**
         * @method request
         * This is the entry point of our application.
         */
        public function request() : void
        {
            #Load app config
            Config::load();


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
            
            $instance = (object)[];
            $constructor = $ref->getConstructor();

            if(!is_null($constructor))
            {
                $instances = [];

                foreach ($ref->getConstructor()->getParameters() as $param) 
                { 
                    $currentInstance = (object)[];
                
                   $classToInject = $param->getType()->getName();

   
                    if(!empty($classToInject) && !$param->isOptional())
                    {
                        if(interface_exists($classToInject))
                        {
                            if(!array_key_exists($classToInject, Mapper::$map))
                            {
                                throw new Exception("Interface not mapped " . $classToInject);
                            }

                            try
                            {
                                $currentInstance = new Mapper::$map[$classToInject];
                            }
                            catch(ArgumentCountError)
                            {
                                //Nested
                                $currentInstance = $this->loadNested($classToInject);
                            }
                        }
                        else if(class_exists($classToInject))
                        { 
                            try
                            {
                                $currentInstance = new $classToInject;
                            }
                            catch(ArgumentCountError)
                            {
                                //Nested
                                $currentInstance = $this->loadNested($classToInject);
                            }
                        }  

                        //Save instance
                        array_push(Mapper::$instances,  $currentInstance);

                        //Assign param
                        array_push($instances,  $currentInstance);
                    }
                }

                $instance = $ref->newInstanceArgs($instances);
            }
            else
            {
                $instance = $ref->newInstance();
            }
            
            $instance->$method();
        }

        private function loadNested(string $classToInject) : object
        {
            $currentInstance = (object)[];

            $dispatchClass = $this->getMapClass($classToInject);

            $ref  = new ReflectionClass($dispatchClass);
            $constructor = $ref->getConstructor();

            if(!is_null($constructor))
            {
                try
                {
                    foreach ($constructor->getParameters() as $param) 
                    {
                        $nestedClass = $param->getType()->getName();

                        if(interface_exists($nestedClass))
                        {
                            $injectInstance = $this->getMapClass($classToInject);
                            $injectParamInstance = $this->getMapClass($nestedClass);
                            
                            $currentInstance =  new $injectInstance(new $injectParamInstance);
                        }
                        else if(class_exists($classToInject))
                        {                             
                            $currentInstance =  new $classToInject(new $nestedClass);
                        }  
                    }
                }
                catch(ArgumentCountError)
                {
                    //Nested
                    throw new Exception("Only one level nested is supported!");
                }
            }

            return $currentInstance;
        }

        private function getMapClass(string $name) : string
        {
            if(array_key_exists($name, Mapper::$map))
            {
                return Mapper::$map[$name];
            }

            return $name;
        }
    }
  