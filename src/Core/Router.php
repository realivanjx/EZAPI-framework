<?php
  namespace Core;
  use \Exception;   
  use Core\Request;
  
  

  class Router 
  {
    public
        $request;

    public function __construct() 
    {
      #instantiate response request
      $this->request = new Request();

      #Add headers to the request method
      $this->request->headers();
    }

    public function __destruct()
    {
      
    }
  }