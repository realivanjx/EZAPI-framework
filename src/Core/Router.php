<?php
  namespace Core; 

  //This class contains all shared instances and functions across routes
  class Router 
  {
    public $request; //instance

    public string $test = "Router"; 

    public function __construct(IRequest $request) 
    {
      $this->request = $request;
    }

    public function __destruct()
    {

    }
  }