<?php
    namespace Models;

    interface IAuthService
    {
        public function authenticate() : string;
    }

?>