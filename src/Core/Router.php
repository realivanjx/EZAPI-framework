<?php
  namespace Core;
  use Exception;   
  use Core\Request;
  use Core\Lang\Translator;
  
  

  class Router 
  {
    public
        $request,
        $lang;

    public function __construct() 
    {
      #instantiate response request
      $this->request = new Request();

      #Add headers to the request method
      $this->request->headers();

      #add multilingual support.
      $this->lang = new Translator();
    }

    public function __destruct()
    {
      
    }
  }