<?php
    namespace Core;
    use Core\Database\Mysql\Mysql;
    use Core\Lang\Translator;
    use \Exception;

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


        /**
         * @method vars
         * @param boolean hideSensitive (obtional)
         * @return array results
         * @comment: This method will return all variables within the model called.
         * This method is useful when you need to return all the columns from a database
         */
        public function vars(bool $hideSensitive = true) : array
        {
            $vars = [];

            #Find all public variables
            foreach($this->db->modelVariables as $key => $value)
            {
                $vars[$key] = $this->{$key};
            }

            /**
             * you must blacklist public variables within this class manually
             * Currently we are using 3 public db, lang, modelVariables so we unset
             * them fromt he results. 
             */
            if(array_key_exists("db", $vars)) unset($vars["db"]);
            if(array_key_exists("lang", $vars)) unset($vars["lang"]);
            if(array_key_exists("modelVariables", $vars)) unset($vars["modelVariables"]);

            #Remove blacklisted
            if(array_key_exists("table", $vars) && $hideSensitive)
            {
                #unset id and password from the table users for extra security
                if($vars["table"] == "user")
                {
                    unset($vars["password"]);
                    unset($vars["twoFactorAuth"]);
                }

                unset($vars["table"]);
            }

            return $vars;
        }

        public function __destruct()
        {
            $this->db = null;
        }
    }