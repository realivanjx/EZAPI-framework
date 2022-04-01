<?php
    namespace Core\Database\Mysql;
    use \PDO;
    use \PDOException;
    use Core\Exceptions\Error;

    class Mysql
    {
        protected PDO $m_connection;

        public function __construct() 
        {
            $this->connect();
           
        }



         /**
         * @method connect
         * @throws PDOException
         * Database connection.
         */
        private function connect() : void
        {
            try 
            { 
                $config = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_CASE => PDO::CASE_NATURAL,
                    PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
                    PDO::MYSQL_ATTR_INIT_COMMAND => sprintf(
                        "SET NAMES %s;SET time_zone ='%s'", 
                        "utf8", 
                        CURRENT_TIMEZONE
                    )#set timezone to match the default framework timezone
                ];


                $this->m_connection = new PDO(sprintf(
                    "mysql:host=%s;dbname=%s", 
                    EZENV['DB_HOST'], 
                    EZENV['DB_NAME']),  
                    EZENV['DB_USER'], 
                    EZENV['DB_PASSWORD'],
                    $config
                );
            }
            catch(PDOException $ex)
            {
                Error::handler($ex);
            }
        }	
        
        
         /**
         * @method insert
         * @param array rows
         * @return boolean
         * @throws excaption
         */
        public function insert() : int
        {
            return 0;
        }

        public function select()
        {}

        public function delete(){}

        // crud operations = 

        // create
        // read
        // update
        // delete

    }
?>