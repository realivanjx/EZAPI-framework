<?php
  namespace Models;
  use Exception;
  use Core\Model;
  use Core\OTP;
  use Core\Exceptions\ApiError;
  

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
        $locale = EZENV["DEFAULT_LOCALE"], //varchar(10)
        $twoFactorAuth, //tinyint(1)
        $createdAt, //timestamp
        $updatedAt, //timestamp
        $deletedAt; //timestamp



    /**
     * @param object $input
     * @return string
     * @throws ApiError exceptions
     */
    public function register(object $input) : string
    {
      $validation = [];

      #STEP1 VALIDATE EMPTY FIELDS
      if(empty($input->fName)) $validation["fName"] = $this->lang->translate("first_name_is_required");
      if(empty($input->lName)) $validation["lName"] =  $this->lang->translate("last_name_is_required");
      if(empty($input->username)) $validation["username"] =  $this->lang->translate("username_is_required");
      if(empty($input->email)) $validation["email"] = $this->lang->translate("email_is_required");
      if(empty($input->password)) $validation["password"] =  $this->lang->translate("password_is_required");
      if(empty($input->confirmPassword)) $validation["confirmPassword"] = $this->lang->translate("password_confirmation_is_required");
      
      if(!empty($validation)) throw new ApiError (serialize($validation));

      #STEP2 EMAIL VALIDATION
      if(!filter_var($input->email, FILTER_VALIDATE_EMAIL)) $validation["email"] = $this->lang->translate("invalid_email_address");
      
      if(!empty($validation)) throw new ApiError (serialize($validation));

      #Verify if the email address already exits
      $user = $this->db->findFirst(["email" => $input->email]);
      if(!empty($user->email)) $validation["email"] = $this->lang->translate("this_email_already_exists");
      
      if(!empty($validation)) throw new ApiError (serialize($validation));

      #STEP3 USERNAME VALIDATION
      if(strlen($input->username) < 8) $validation["username"] = $this->lang->translate("your_username_is_too_short");
      if(strlen($input->username) > 150) $validation["username"] = $this->lang->translate("your_username_is_too_long");
      
      if(!empty($validation)) throw new ApiError (serialize($validation));

      #Verify if the username exits in the database
      $user = $this->db->findFirst(["username" => $input->username]);
      if(!empty($user->username)) $validation["username"] = $this->lang->translate("this_username_already_exists");
      
      if(!empty($validation)) throw new ApiError (serialize($validation));

      #STEP4 PASSWORD VALIDATION
      if(strlen($input->password) < 8) $validation["password"] =  $this->lang->translate("your_password_is_too_short");
      if(strlen($input->password) > 150) $validation["password"] = $this->lang->translate("your_password_is_too_long");
      if($input->password !== $input->confirmPassword) $validation["confirmPassword"] = $this->lang->translate("your_password_does_not_match");

      if(!empty($validation)) throw new ApiError (serialize($validation));

      #assign values
      $this->assign($input);

      #check if the locale cookie is set and assign its value
      if(Cookie::exists("locale"))
      {
        $this->locale = Cookie::get("locale");
      }

      #save record
      if(!$this->db->save())
      {
        throw new ApiError (Constant::ERROR_MESSAGE);
      }
          
      #By default email validation is enable
      if(EZENV["ENFORCE_EMAIL_VALIDATION"])
      {
        #Get userId as last interted ID
        $userId = $this->db->lastInsertedId();

        #Get an OTP to validate email
        $otp = OTP::get($userId);

        #Send OTP email
        try
        {
        //   $this->di->Mail->sendOTP(
        //     $this->fName,
        //     $this->email,
        //     $otp
        //   );
        }
        catch(Exception $ex)
        {
          #delete inserted record since we are unable to validate.
          $this->db->deleteId($userId);

          #Unable to send OTP! Record deleted.
          throw new ApiError (Constant::UNABLE_TO_SEND_OTP);
        }
        
        #Registration susccessful, now Validate the OTP token
        return Constant::OTP_SENT;
      }

      #Registration susccessful
      return Constant::SUCCESS;
    }
}
?>