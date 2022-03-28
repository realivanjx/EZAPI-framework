 <?

           $ref  = new ReflectionClass($route);
            
            $instance = (object)[];
            $constructor = $ref->getConstructor();

            if(!is_null($constructor))
            {
                $instances = [];

                foreach ($constructor->getParameters() as $param) 
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