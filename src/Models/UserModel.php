<?php
  namespace Models;
  use \Exception;
  use Core\Model;
  

  //polymorphism
 

    
  class UserModel extends Model
  {
    public 
        $table = "user",
        $id, //int 11
        $fName, // varchar(50)
        $lName, // varchar(50)
        $username, // varchar(100)
        $email, // varchar(150)
        $password, // varchar(150)
        $status = 2, #inactive by default,
        $role = "USER", #carchar(100)
        //$locale = EZENV["DEFAULT_LOCALE"], //varchar(10)
        $twoFactorAuth, //tinyint(1)
        $createdAt, //timestamp
        $updatedAt, //timestamp
        $deletedAt; //timestamp

  public function login() : string
  {
      return "routing test done";
  }
}
?>