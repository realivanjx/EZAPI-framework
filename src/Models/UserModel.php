<?php
  namespace Models;
  use \Exception;
  use Core\Model;
  use Core\Exceptions\ApiError;
    

    
  class UserModel extends Model 
  {
    public 
      $table = "user",
      $username,
      $email,
      $password;

    public function login(object $input) : string
    {
      #Check if username or email field is empty
      if(empty($input->username_or_email))
      {
        throw new ApiError (serialize(["username_or_email" => $this->lang->translate("username_or_email_is_required")]));
      }

      #Check if password field is empty
      if(empty($input->password))
      {
        throw new ApiError (serialize(["password" => $this->lang->translate("password_is_required")]));
      }

      #Check whether the user entered a valid email otherwise treat it as an username
      $identifier = filter_var($input->username_or_email, FILTER_VALIDATE_EMAIL) ? "email" : "username";

      #Attempt to find the user in the database with the username or email provided
      $user =  $this->query->findFirst([$identifier => $input->username_or_email]);

      #User not found
      if(empty($user->$identifier))
      {
        throw new ApiError (serialize(["username_or_email" => $this->lang->translate("invalid_username_or_email")]));
      }

      #Invalid password
      if(!password_verify($input->password, $user->password))
      {
        throw new Exception (serialize(["password" => $this->lang->translate("invalid_password")]));
      }


      print_r($user); die("thats all for now");
    }

  }