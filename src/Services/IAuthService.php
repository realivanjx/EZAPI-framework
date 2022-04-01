<?php
    namespace Services;

    interface IAuthService
    {
        public function authenticate(string $usernameOrEmail, string $password, bool $rememberMe) : object;
    }
?>