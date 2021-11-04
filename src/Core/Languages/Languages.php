<?php
    namespace Core\Languages;

    Class Languages
    {
        public const config = [
            "en_US" => [
                "name" => "English (United States)", 
                "locale" => "en_US",
                "charset" => "UTF-8"
            ],
            "es_US" => [
                "name" => "Spanish (United States)", 
                "locale" => "es_US",
                "charset" => "UTF-8"
            ]
            #Add any other supported locale here
        ];

        public const list = [
            "hello_world" => [
                "en_US" => "Hello world",
                "es_US" => "Hola mundo"
                #Add any other language here
            ],
        ];
    }
        


