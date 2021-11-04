<?php
  namespace Core;
  use \Exception;   
  use Core\Request;
  use Core\Constant;
  use Core\Languages\Translator;
  use Core\Exceptions\ApiError;
  

  class Router 
  {
    public
        $request, 
        $lang,
        $di;

    public function __construct() 
    {
      #instantiate response request
      $this->request = new Request();

      $this->request->headers();

      $this->lang = new Translator();
    }

    public function __destruct()
    {
       
    }
  }