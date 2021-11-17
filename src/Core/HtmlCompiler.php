<?php
    namespace Core;
    use \SplFileObject;
    use \Exception;

    class HtmlCompiler
    {

        /**
         * @method run
         * @param string filePath
         * @param array parameters
         * @return string html content
         * @throws exceptions 
         * @comment: Use this function to compile html pages with dynamic variables.
         */
        public static function run(string $filePath, array $parameters) : string
        {
            #Validate the file location
            if(!file_exists($filePath)) throw new Exception("The file location does not exists");

            $compiledHtml = null;

            #Open html file
            $file = new SplFileObject($filePath);

            while (!$file->eof())
            {
                #read line
                $line = $file->fgets();

                #Search for double symbools
                if (preg_match('#@@#', $line)) 
                {
                    #Get everything before the two @@ symbols
                    preg_match("/(@@)(.*?)(@@)/", $line, $parameter);

                    #Get the 3rd key since its the one without the @@
                    $parameter = $parameter[2];

                    #Validate key existance
                    if(!array_key_exists($parameter, $parameters)) throw new Exception(sprintf("Parameter %s not found in the array.", $parameter));

                    #Replace everything between the two @@ symbols
                    $line = preg_replace("/(@@)(.*?)(@@)/", $parameters[$parameter], $line);
                }

                #Append to the string
                $compiledHtml .=  $line;
            }

            #Return value
            return $compiledHtml;
        }
    }