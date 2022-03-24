<?php
    namespace Src;
    
    //No namespace needed in the root dir
    use Models\{
        IUserModel, 
        UserModel, 
        IAuthService, 
        MyAuthService
    };

    use Models\Service\{
        IServiceTest, 
        ServiceTest
    };

    class Mapper
    {
        public static $map = [
            IUserModel::class =>  UserModel::class,
            IAuthService::class =>  MyAuthService::class,
            IServiceTest::class => ServiceTest::class
        ];


        //This will hold the instantiated instances within the constructor
        public static $instances = [];
    }