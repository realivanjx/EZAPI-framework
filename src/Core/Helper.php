<?php
    namespace Core;


    class Helper
    {
       
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

        public static function sanitizeGet(array $getData) : array
        {
            return filter_input_array(INPUT_GET, $getData, FILTER_SANITIZE_STRING);
        }

        public static function sanitizePost(array $postData) : array
        {
            return filter_input_array(INPUT_POST, $postData, FILTER_SANITIZE_STRING);
        }
    }