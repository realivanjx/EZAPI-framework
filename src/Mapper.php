<?php
    namespace Src;
    
    //No namespace needed in the root dir
    use Models\{
        IUserModel, 
        UserModel, 
        IAuthService, 
        MyAuthService,
        ISuperAuthService, 
        SuperAuthService
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
            ISuperAuthService::class =>  SuperAuthService::class,
            IServiceTest::class => ServiceTest::class
        ];


        //This will hold the instantiated instances within the constructor
        public static $instances = [];
    }