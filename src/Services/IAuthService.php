<?php
    namespace Services;
    use Models\User;

    interface IAuthService
    {
        public function authenticate(string $usernameOrEmail, string $password, bool $rememberMe) : User;
    }
?>