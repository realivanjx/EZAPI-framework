<?php
  namespace Models;
  use Core\Model;
  
    
  class UserModel extends Model implements IUserModel
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


    public function login(object $input) : string
    {
        return "Hello from the user model login function";
    }

    public function register(object $input) : string
    {
        return "register";
    }
  }