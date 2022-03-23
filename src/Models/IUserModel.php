<?php
    namespace Models;
    
    interface IUserModel
    {
    public function login(object $input) : string;
    public function register(object $input) : string;
    }