<?php
    namespace Src;

    use Core\Database\Mysql\{IDatabase, Database};
    use Repositories\{IUserAuthRepository, UserAuthRepository};

    use Services\{
        IAuthService,
        AuthService
    };


    class Mapper
    {
        public static $map = [
            IAuthService::class =>  AuthService::class,
            IUserAuthRepository::class => UserAuthRepository::class,
            IDatabase::class => Database::class
        ];

    }