<?php
    namespace Models;

    interface IAuthService
    {
        public function authenticate(object $input) : string;
        public function register(object $input) : string;
    }
?>