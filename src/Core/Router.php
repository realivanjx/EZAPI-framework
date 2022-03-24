<?php
  namespace Core; 

  //This class contains all shared instances and functions across routes
  class Router 
  {
    public $request; //instance

    public string $test = "Router"; 

    public function __construct() 
    {
     
    }

    public function __destruct()
    {

    }
  }