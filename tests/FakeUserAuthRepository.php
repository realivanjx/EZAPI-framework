<?php

namespace Tests;
use Models\User;
use Repositories\IUserAuthRepository;

class FakeUserAuthRepository implements IUserAuthRepository
{
    public $getUserByEmailCallback;
    public $getUserByUsernameCallback;

    public function getUserByEmail(string $email): User
    {
        return $this->getUserByEmailCallback($email);
    }

    public function getUserByUsername(string $username): User
    {
        return $this->getUserByUsernameCallback($username);
    }
}

?>