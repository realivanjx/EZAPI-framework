<?php
    namespace EZAPIFRAMEWORK;
    use \Exception;
    use Core\Dispatch;
  

    /**
    *################################
    *#> Welcome to EZAPI framework <#
    *################################
    * 
    * @copyright (c) Nerdtrix LLC 2021 - Current
    * @author Name: Jerry Urena
    * @author Social links:  @jerryurenaa
    * @author email: jerryurenaa@gmail.com
    * @author website: jerryurenaa.com
    * @license MIT (included within this project)
    * 
    */


    try
    {
        //Check server requirements before instantiation.

        #Composer path
        $composerPath = sprintf(
            "%s%svendor%sautoload.php", 
            dirname(__DIR__, 1), 
            DIRECTORY_SEPARATOR, 
            DIRECTORY_SEPARATOR
        );

        #Verify composer autoload
        if(file_exists($composerPath))
        {
            require_once($composerPath);
        }
        else
        {
            #Native Autoload
            spl_autoload_register(function (string $className)
            {
                $fileName = sprintf(
                    "%s%ssrc%s%s.php", 
                    dirname(__DIR__),  
                    DIRECTORY_SEPARATOR, 
                    DIRECTORY_SEPARATOR, 
                    str_replace("\\", DIRECTORY_SEPARATOR, $className)
                );

                if (file_exists($fileName))
                {
                    require ($fileName);
                }
                else
                {
                    throw new Exception(sprintf("Class not found: %s", $fileName));
                }
            });
        }

        //run middleware before dispatching

        #Dispatch request
        $initialize = new Dispatch();
        $initialize->request();
    }
    catch(Exception $ex)
    {
        echo $ex->getMessage();
    }