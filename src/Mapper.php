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

    enum InstanceType
    {
        case transient;
        case request;
        case singleton;
    }

    class Mapper
    {
        public static $map = [
            IUserModel::class =>  UserModel::class,
            IAuthService::class =>  MyAuthService::class,
            ISuperAuthService::class =>  SuperAuthService::class,
            IServiceTest::class => ServiceTest::class
        ];


        //Test
        public static $mapTest = [
            IUserModel::class =>  [
                UserModel::class,
                InstanceType::transient
            ],
            IAuthService::class =>  [
                MyAuthService::class,
                InstanceType::request
            ],
            ISuperAuthService::class =>  [
                SuperAuthService::class,
                InstanceType::singleton
            ]
        ];


        //This will hold the instantiated instances within the constructor
        public static $instances = [];
    }