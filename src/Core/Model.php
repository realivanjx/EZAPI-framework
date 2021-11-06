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
            $modelVariables = get_class_vars(get_class($this));

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

        public function __destruct()
        {
            $this->di = null;
            $this->db = null;
            $this->lang = null;
        }
    }