<?php
  namespace Models;
  use Core\Model;
  use Models\IUserModel;
  

  
 

    
  class UserModel extends Model implements IUserModel
  {
    protected 
        $table = "user",
        $id,
        $fName,
        $lName,
        $email,
        $password,
        $createdAt;


    public function login(object $input) : string
    {
        return "klk wawawa ";
    }

    public function register(object $input) : string
    {
        return "";
    }
  }