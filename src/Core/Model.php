<?php
    namespace Core;

    

    //this class contains all shared instances and functions across all models
    class Model
    {
        public $db; //instance

        public string $test = "Model"; 

        public function __construct() 
        {
            
        }

        public function __destruct()
        {
          
        }
    }