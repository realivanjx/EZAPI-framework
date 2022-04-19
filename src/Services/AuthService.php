<?php

namespace Services;

use Core\Exceptions\ApiError;
use Exception;
use Models\User;
use Repositories\IUserAuthRepository;


class AuthService implements IAuthService
{
    protected IUserAuthRepository $m_authRepository;
    protected User $m_user;

    public function __construct(IUserAuthRepository $authRepository)
    {
        $this->m_authRepository = $authRepository;
    }


    public function authenticate(string $usernameOrEmail, string $password, bool $rememberMe): User
    {
        return $this->m_authRepository->getUserByEmail($usernameOrEmail);
    }
}