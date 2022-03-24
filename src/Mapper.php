<?php
    //No namespace needed in the root dir

    class Mapper
    {
        public static $map = [
            "Models\IUserModel" => "Models\UserModel",
            "Models\IAuthService" => "Models\MyAuthService"
        ];

        //This will hold the instantiated instances within the constructor
        public static $instances = [];
    }