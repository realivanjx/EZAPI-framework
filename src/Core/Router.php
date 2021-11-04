<?php
  namespace Core;
  use \Exception;   
  use Core\Request;
  use Core\Constant;
  use Core\DI;
  use Core\Languages\Translator;
  
  

  class Router 
  {
    public
        $request, 
        $lang,
        $di;

    public function __construct() 
    {
      #Inject Dependencies
      $dependencyInjection = new DI();
      $this->di = $dependencyInjection->load(get_called_class());

      #instantiate response request
      $this->request = new Request();

      #Add headers to the request method
      $this->request->headers();

      $this->lang = new Translator();
    }

    public function __destruct()
    {
      $this->di = null;
    }
  }