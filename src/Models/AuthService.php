<?php
  namespace Models;
  use Core\Model;
  
    
  class AuthService extends Model implements IAuthService
  {
    protected 
      $table = "user",
      $id,
      $fName,
      $lName,
      $email,
      $password,
      $createdAt,
      $updatedAt,
      $deletedAt;


    public function authenticate(object $input) : string
    {
        return "Hello from the user model login function";
    }

    public function register(object $input) : string
    {
        return "register";
    }
  }