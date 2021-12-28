<?php
    namespace Core;
    use Core\DI;
    use Core\Languages\Translator;
    use Core\Database\Mysql;
    use Core\Database\MysqlQuery;


    class Model
    {
        public 
            $di, 
            $db, 
            $lang;

        public function __construct() 
        {
            (array)$modelVariables = get_class_vars(get_class($this));

            if(!array_key_exists("table", $modelVariables) && get_class($this) !== "Core\Model")
            {
                if(!EZENV["PRODUCTION"])
                {
                    die(sprintf("You must add a 'table' variable to the model: %s", get_class($this)));
                }

                throw new Exception("You must add a 'table' variable to the model: {get_class($this)}");
            }

            if(get_class($this) !== "Core\Model")
            {
                $this->db = new MysqlQuery($modelVariables["table"]);
            }
         
            

            #instantiate language 
            $this->lang = new Translator();  

            #Inject Dependencies
            // $dependencyInjection = new DI();
            // $this->di =  $dependencyInjection->load(get_called_class());
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
            $this->di = null;
            $this->db = null;
            $this->lang = null;
        }
    }