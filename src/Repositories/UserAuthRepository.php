<?php

namespace Repositories;

use Exception;
use Models\User;


class UserAuthRepository implements IUserAuthRepository
{
    public function getUserByEmail(string $email): User
    {
        throw new Exception("not implemented");
    }

    public function getUserByUsername(string $username): User
    {
        throw new Exception("not implemented");
    }
}



// UserRepository
// Irepository<BaseEntity>
// find(long id) : User
// insert(T entity) : User //generig interface
// update(T entity) : void
// Delete(T entity) : void
// save() : void
