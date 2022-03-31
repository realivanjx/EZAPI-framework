<?php
    namespace Services;

    interface IAuthService
    {
        public function authenticate(object $input) : string;
    }
?>