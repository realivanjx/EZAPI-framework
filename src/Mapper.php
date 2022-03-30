<?php
    namespace Src;
    
    //No namespace needed in the root dir

use Core\Database\Mysql\{IMysqlQuery, MysqlQuery};
use Models\{
        IAuthService,
        AuthService
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
            IAuthService::class =>  AuthService::class,
            IMysqlQuery::class => MysqlQuery::class
        ];


        //Test
        public static $mapTest = [
            IAuthService::class =>  [
                AuthService::class,
                InstanceType::transient
            ]
        ];


        //This will hold the instantiated instances within the constructor
        public static $instances = [];
    }