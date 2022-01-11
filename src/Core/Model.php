<?php
    namespace Core;
    use Core\Database\Mysql\Mysql;
    use Core\Lang\Translator;


    class Model
    {
        public 
            $db, 
            $lang;

        public function __construct() 
        {
            //initialize db and language
            (array)$modelVariables = get_class_vars(get_class($this));

            if(!array_key_exists("table", $modelVariables) && get_class($this) !== "Core\Model")
            {
                if(!EZENV["PRODUCTION"])
                {
                    die(sprintf("You must add a 'table' variable to the model: %s", get_class($this)));
                }

                throw new Exception(sprintf("You must add a 'table' variable to the model: %s", get_class($this)));
            }

            if(get_class($this) !== "Core\Model")
            {
                $this->db = new Mysql($modelVariables["table"]);
            }

            #add multilingual support.
            $this->lang = new Translator();
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
            $this->db = null;
        }
    }