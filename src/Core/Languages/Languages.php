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
            "username_or_email_is_required" => [
                "en_US" => "Username or email is required",
                "es_US" => "Nombre de usuario o correo es requerido"
            ],
            "password_is_required" => [
                "en_US" => "Password is required",
                "es_US" => "La contraseña es requerida"
            ],
            "invalid_username_or_email" => [
                "en_US" => "Invalid username or email",
                "es_US" => "Nombre de usuario o correo electrónico no válido"
            ],
            "invalid_password" => [
                "en_US" => "Invalid password",
                "es_US" => "Contraseña invalida"
            ],

            

            
        ];
    }
        


