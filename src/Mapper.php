<?php
    //No namespace needed in the root dir

    class Mapper
    {
        public static $map = [
            "Models\IUserModel" => "Models\UserModel"
        ];

        //This will hold the instantiated instances within the constructor
        public static $instances = [];
    }