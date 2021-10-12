<?php
    namespace EZAPIFRAMEWORK;
    use Core\Dispatch;
    use Core\Error;
    use \Exception;
    

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
        #Autoload
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
                throw new Exception("Class not found: {$fileName}");
            }
        });


        #Dispatch request
        Dispatch::request();
    }
    catch(Exception $ex)
    {
        Error::handler($ex);
    }