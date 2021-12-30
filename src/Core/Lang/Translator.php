<?php
    namespace Core\Lang;
    use Core\Constant;
    use Core\Cookie;
    use Core\Exceptions\ApiError;

    class Translator
    {
        private 
            $_dictionary,
            $_locale,
            $_config;

        public const SUPPORTED_LANGUAGES = [
            "en_US" => [
                "name" => "English (United States)", 
                "locale" => "en_US",
                "charset" => "UTF-8",
                "path" => "en/en_US.php"
            ],
            "es_US" => [
                "name" => "Spanish (United States)", 
                "locale" => "es_US",
                "charset" => "UTF-8",
                "path" => "es/es_US.php"
            ]
            #Add any other supported locale here following the same pattern
        ];

        public function __construct()
        {
            $this->_config = self::SUPPORTED_LANGUAGES;

            #Set locale
            if(Cookie::exists("locale"))
            {
                $this->_locale = Cookie::get("locale");
            }
            else
            {
                #Assign value
                $this->_locale = EZENV["DEFAULT_LOCALE"];

                #set locale
                $this->setLocale(EZENV["DEFAULT_LOCALE"]);
            }

            $this->loadDictionary($this->_locale);
        }

        /**
         * @method translate
         * @param string key
         * @return string
        */
        public function translate(string $key) : string
        {
            if(array_key_exists($key, $this->_dictionary))
            {
                return $this->_dictionary[$key];
            }

            return Constant::INVALID_KEY_OR_LOCALE;
        }


        /**
         * @method info
         * @return array
         * @comment: Returns the current locale, name and charset
         */
        public function info() : array 
        {
            #Remove the path before displaying the language
            unset($this->_config[$this->_locale]["path"]);

            return $this->_config[$this->_locale];
        }

        /**
         * @method list
         * @return array
         * @comment: Returns the list of available languages
         */
        public function list() : array 
        {
            #Remove the path before displaying the language
            foreach($this->_config as $key => $value)
            {
                unset($this->_config[$key]["path"]);
            }

            return $this->_config;
        }


        /**
         * @return array
         */
        public function currentLocale() : string
        {
            if(Cookie::exists("locale"))
            {
                return Cookie::get("locale");
            }

            return $this->_locale;
        }

        /**
         * @param string locale
         * @return bool
         * @throws ApiError
         */
        public function setLocale(string $locale) : bool
        {
            if(!array_key_exists($locale, $this->_config))
            {
               throw new ApiError (Constant::INVALID_LANGUAGE_LOCALE);
            }

            #Store preference in a cookie for one year
            if(Cookie::set("locale", $locale, time() + 31556926))
            {
                #set locale value
                $this->_locale = $locale; 

                if($this->loadDictionary($locale))
                {
                    return true;
                }
            }

            return false;
        }


        /**
         * @param string locale
         * @return bool
         * @throws ApiError
         */
        private function loadDictionary(string $locale) : bool
        {
            $dictionaryPath = sprintf(
                "%s%sCore%sLang%s%s", 
                SRC_DIR, 
                SLASH, 
                SLASH, 
                SLASH, 
                self::SUPPORTED_LANGUAGES[$locale]["path"]
            );

            if(!file_exists($dictionaryPath))
            {
                throw new ApiError (Constant::INVALID_LANGUAGE_PATH);
            }

            #Load the new language
            $this->_dictionary = require($dictionaryPath);

            if(!empty($this->_dictionary))
            {
                return true;
            }

            return false;
        }
    }