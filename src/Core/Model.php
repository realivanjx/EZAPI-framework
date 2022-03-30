<?php
    namespace Core;
    use Core\Database\Mysql\IMysqlQuery;

    

    //this class contains all shared instances and functions across all models
    class Model
    {
        public $db; //instance

        public function __construct(IMysqlQuery $mysqlQuery) 
        {
            $this->db = $mysqlQuery;
        }

        public function __destruct()
        {
          $this->db = null;
        }
    }