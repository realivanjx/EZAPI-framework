<?php
    namespace Repositories;
    use Models\User;

    interface IUserAuthRepository
    {
        function findFirst(string $identifier, string $username) : User;

    }