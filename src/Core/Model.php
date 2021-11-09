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

            if(!array_key_exists("table", $modelVariables))
            {
                if(!EZENV["PRODUCTION"])
                {
                    die(sprintf("You must add a 'table' variable to the model: %s", get_class($this)));
                }

                throw new Exception("You must add a 'table' variable to the model: {get_class($this)}");
            }
         
            $this->db = new MysqlQuery($modelVariables["table"]);

            #instantiate language 
            $this->lang = new Translator();  

            #Inject Dependencies
            $dependencyInjection = new DI();
            $this->di =  $dependencyInjection->load(get_called_class());
        }

         /**
         * @method assign
         * @param object object
         * @comment: Used to assign values to all of the variables in a model.
         */
        public function assign(object $object) : void 
        {
            $variables = [];

            foreach($object as $key => $value) 
            {
                if(property_exists($this, $key))
                {
                    #Assign values to its parent class
                    $this->$key = $value;
                    
                    #save values
                    $variables[$key] = $value;
                }
            }

            #Assign variables to the database class
            $this->db->modelVariables = $variables;
        }

        public function __destruct()
        {
            $this->di = null;
            $this->db = null;
            $this->lang = null;
        }
    }