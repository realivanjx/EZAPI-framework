<?php

namespace Tests;
use PHPUnit\Framework\TestCase;
use Services;
use Models\User;
use Services\AuthService;

class AuthServiceTest extends TestCase
{
    private FakeUserAuthRepository $authRepository;
    private AuthService $service;

    public function setUp() : void
    {
        $this->authRepository = new FakeUserAuthRepository();
        $this->service = new AuthService(
            $this->authRepository
        );
    }

    public function testAuthenticate() : void
    {
        // Auth repository.
        $getUserCount = 0;
        $this->authRepository->getUserByEmailCallback = function($email, &$getUserCount)
        {
            assert("john@mail.com", $email);
            $getUserCount += 1;
            $user = new User();
            $user->username = "test";
            return $user;
        };

        // Test.
        $result = $this->service->authenticate("john@mail.com", "12345", true);

        // Assert.
        assert(1, $getUserCount);
        assert("test", $result->username);
    }
}

?>
