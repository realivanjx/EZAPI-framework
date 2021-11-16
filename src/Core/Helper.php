<?php
    namespace Core;


    class Helper
    {

        /**
         * Check if a string is serialized or not
         * @method isSerialized
         * @param string $string
         * @return bool
         */
        public static function isSerialized(string $string) : bool
        {
            return (@unserialize($string) !== false);
        }
       

        /**
         * @method sanitizeObject
         * @param object objectData
         * @return object
         */
        public static function sanitizeObject(object $objectData) : object
        {
            foreach ($objectData as $value) 
            {
                if (is_scalar($value)) 
                {
                    $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

                    continue;
                }
        
                sanitize($value);
            }
        
            return $objectData;
        }


        /**
         * @method sanitizeGet
         * @param array getData
         * @return array
         */
        public static function sanitizeGet(array $getData) : array
        {
            return filter_input_array(INPUT_GET, $getData, FILTER_SANITIZE_STRING);
        }


        /**
         * @method sanitizePost
         * @param array postData
         * @return array
         */
        public static function sanitizePost(array $postData) : array
        {
            return filter_input_array(INPUT_POST, $postData, FILTER_SANITIZE_STRING);
        }


        /**
         * @method randomNumber
         * @param int length
         * @return int 
         */
        public static function randomNumber(int $length) : int
        {
            $result = null;

            for($i = 0; $i < (int) $length; $i++) 
            {
                $result .= mt_rand(0, 9);
            }

            return (int)$result;
        }
    }