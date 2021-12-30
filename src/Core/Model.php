<?php
    namespace Core;


    class Model
    {
        public 
            $db, 
            $lang;

        public function __construct() 
        {
            //initialize db and language
        }

         /**
         * @method assign
         * @param object object
         * @comment: Used to assign values to all of the variables in a model.
         */
        public function assign(object $object) : void 
        {
            $vars = get_class_vars(get_class($this));
            
            foreach($object as $key => $value) 
            {
                if(array_key_exists($key, $vars))
                {
                    #Assign values to its parent class
                    $vars[$key] = $value;

                    #Assign value to the root variable
                    $this->$key = $value;
                }
            }

            #Assign variables to the database class
            $this->db->modelVariables = $vars;
        }

        public function __destruct()
        {
        }
    }