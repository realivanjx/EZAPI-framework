<?php
  namespace Core; 

  
  class Router 
  {
    public $request;

    public function __construct(IRequest $request) 
    {
      $this->request = $request;
    }

    public function __destruct()
    {
      $this->request = null;
    }
  }