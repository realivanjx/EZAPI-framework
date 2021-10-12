<?php
  namespace Core;
  use \Exception;   
  use Core\Request;

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

        #To prevent cors issues we load all our headers before dispatching any request.
        $this->request->headers();
    }

    public function __destruct()
    {
        #Remove all headers
        header_remove(); 
    }
  }