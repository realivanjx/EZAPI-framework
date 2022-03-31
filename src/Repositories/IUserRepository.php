<?php
    namespace Repositories;
    use Models\User;

    interface IUserRepository
    {
        function getById(int $id) : User;

        function getByUsername(string $username) : User;

        function getByEmail(string $email) : User;

        function list(int $offset, int $limit) : User;
        
        function add(User $user) : User;

        function edit(User $user) : void;

        function remove(int $userId) : void;

    }