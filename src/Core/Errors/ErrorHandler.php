<?php
  namespace Core\Errors;
  use \Exception;


  class ErrorHandler 
  {
    //in progress
    public static function handler(Exception $ex) : void
    {
        print_r($ex);
    }
  }