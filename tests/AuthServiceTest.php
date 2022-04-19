<?php

namespace Tests;
use PHPUnit\Framework\TestCase;
use Services;
use Models\User;
use Services\AuthService;

use function PHPUnit\Framework\assertEquals;

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
        $this->authRepository->getUserByEmailCallback = function($email) use (&$getUserCount)
        {
            assertEquals("john@mail.com", $email);
            $getUserCount += 1;
            $user = new User();
            $user->username = "test";
            return $user;
        };

        // Test.
        $result = $this->service->authenticate("john@mail.com", "12345", true);

        // Assert.
        assertEquals(1, $getUserCount);
        assertEquals("test", $result->username);
    }
}

?>
