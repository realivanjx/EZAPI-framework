<?php
    namespace Models;

    interface ISuperAuthService
    {
        public function authenticate() : string;
    }

?>