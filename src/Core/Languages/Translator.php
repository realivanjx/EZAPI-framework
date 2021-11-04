<?php
    namespace Core\Languages;
    use Core\Constant;
    use Core\Cookie;
    use Core\Languages\Languages;
    use Core\Exceptions\apiError;

    class Translator
    {
        private 
            $_dictionary,
            $_locale,
            $_config;

        public function __construct()
        {
            $this->_dictionary = Languages::list;

            $this->_config = Languages::config;

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
                if(array_key_exists($this->_locale, $this->_dictionary[$key]))
                {
                    return $this->_dictionary[$key][$this->_locale];
                }
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
            return $this->_config[$this->_locale];
        }

        /**
         * @method list
         * @return array
         * @comment: Returns the list of available languages
         */
        public function list() : array 
        {
            return $this->_config;
        }

        public function currentLocale() : string
        {
            if(Cookie::exists("locale"))
            {
                return Cookie::get("locale");
            }

            return $this->_locale;
        }

        /**
         * 
         */
        public function setLocale($locale) : bool
        {
            if(!array_key_exists($locale, $this->_config))
            {
               throw new apiError (Constant::INVALID_LANGUAGE_LOCALE);
            }

            #Store preference in a cookie for one year
            if(Cookie::set("locale", $locale, time() + 31556926))
            {
                #set locale value
                $this->_locale = $locale; 

                return true;
            }

            return false;
        }
    }