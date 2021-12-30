<?php
  namespace Core\Exceptions;
  use Exception;


  class ExceptionHandler 
  {

    /**
     * @param Exception ex is the exception error given
     * By default errors will be shown only in development mode.
     */
    public function __construct(Exception $ex)
    {

      if(!EZENV["PRODUCTION"])
      {
        $errorBuilder = [
          "error" => "Exception Occured",
          "time" => TIMESTAMP,
          "type" => get_class($ex),
          "message" => $ex->getMessage(),
          "path" => $ex->getFile(),
          "line" => $ex->getLine(),
          "trace" => $ex->getTrace()
        ];
      }
      else
      {
        error_reporting(E_ALL);
        ini_set('ignore_repeated_errors', TRUE);
        ini_set('log_errors', TRUE);
        ini_set('error_log', sprintf("%s%sCore%sErrors%sException_log.txt", SRC_DIR, SLASH, SLASH, SLASH));
      
        $log = sprintf("\rType: %s\r", get_class($ex));
        $log .= sprintf("Message: %s\r", $ex->getMessage());
        $log .= sprintf("Path: %s\r", $ex->getFile());
        $log .= sprintf("Line: %s\r", $ex->getLine());
        $log .= sprintf("Trace: %s\n", $ex->getTraceAsString());
       
        error_log($log);

        $errorBuilder = ["message" => "Internal Server Error server error"];
      }

      #Internal server error code
      http_response_code(500);

      #Response
      exit(json_encode($errorBuilder));
    }
  }
?>