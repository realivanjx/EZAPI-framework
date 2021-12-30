<?php
    namespace EZAPIFRAMEWORK;
    use Exception;
    use Core\Dispatch;
    use Core\Exceptions\ExceptionHandler;

    /**
    *################################
    *#> Welcome to EZAPI framework <#
    *################################
    * 
    * @copyright (c) Nerdtrix LLC 2021 - Current
    * @author Name: Jerry Urena | @jerryurenaa
    */


    try
    {
        #Composer path
        $composerPath = sprintf(
            "%s%svendor%sautoload.php", 
            dirname(__DIR__, 1), 
            DIRECTORY_SEPARATOR, 
            DIRECTORY_SEPARATOR);

        #Verify composer autoload
        if(file_exists($composerPath))
        {
            require_once($composerPath);
        }
        else
        {
            #Native Autoload
            spl_autoload_register(function ($className)
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

        #Dispatch request
        Dispatch::request();
    }
    catch(Exception $ex)
    {
        new ExceptionHandler($ex);
    }
?>