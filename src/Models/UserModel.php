<?php
  namespace Models;
  use Exception;
  use Core\Model;
  use Core\OTP;
  use Core\Cookie;
  use Core\Constant;
  use Core\Exceptions\ApiError;
  use Core\Mail\Mail;
  use Core\Session;
  use Core\Globals;
  

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
        $status, #active by default,
        $role = "USER", #carchar(100)
        $locale = EZENV["DEFAULT_LOCALE"], //varchar(10)
        $twoFactorAuth, //tinyint(1)
        $createdAt, //timestamp
        $updatedAt, //timestamp
        $deletedAt; //timestamp

    public const STATUS_ACTIVE = "active";
    public const STATUS_INACTIVE = "inactive";
    public const STATUS_BANNED = "banned";

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
        #set account status to inactive
        $this->status = self::STATUS_INACTIVE;

        #Get userId as last interted ID
        $userId = $this->db->lastInsertedId();

        #Get an OTP to validate email
        $otp = OTP::get($userId);

        #Send OTP email
        try
        {
          $test = new Mail();
          $test->sendOTP(
            $this->fName,
            $this->email,
            $otp
          );
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


    /**
     * @param object $input
     * @return string
     * @throws ApiError exceptions
     */
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
      $user =  $this->db->findFirst([$identifier => $input->username_or_email]);
 
      #User not found
      if(empty($user->$identifier))
      {
        throw new ApiError (serialize(["username_or_email" => $this->lang->translate("invalid_username_or_email")]));
      }
      
      #Invalid password
      if(!password_verify($input->password, $user->password))
      {
        throw new ApiError (serialize(["password" => $this->lang->translate("invalid_password")]));
      }

      #Check if the user is banned
      if($user->status == self::STATUS_BANNED)
      {
        throw new ApiError (serialize(["banned" => $this->lang->translate("account_banned")]));
      }

      $test = new Mail();
      
      /**
       * If enforce email validation is enable and the account status is inactive return OTP required or
       * if the account has 2 factor auth enable send an OPT. 
       */
      if($user->status == self::STATUS_INACTIVE && EZENV["ENFORCE_EMAIL_VALIDATION"] || $user->twoFactorAuth)
      {
        #OTP required
        if(empty($input->token))
        {
          #Get an OTP
          $otp = OTP::get($user->id);

          #Send OTP email
          try
          {            

            #Send OTP
            $test->sendOTP(
              $user->fName,
              $user->email,
              $otp
            );
          }
          catch(Exception $ex)
          {
            #Unable to send OTP! Record deleted.
            throw new ApiError (serialize(["OTP" =>  $this->lang->translate("unable_to_send_otp")]));
          }
          
          return Constant::OTP_SENT;
        }

        #validate token auth
        $tokenResp = OTP::validate($user->id, $input->token);

        if($tokenResp !== constant::SUCCESS)
        {
          throw new ApiError (serialize(["OTP" => $this->lang->translate("invalid_otp")]));
        }

        #activate account
        if($user->status == self::STATUS_INACTIVE)
        {
          #update account status
          $update = $this->db->updateById($user->id, ["status" => self::STATUS_ACTIVE]);

          #Send welcome email First
          $test->sendWelcomeEmail($user->fName, $user->email);
        }
      }

      #asign cookie session
      $handleSession = Session::set($user->id);

      if($handleSession == Constant::SUCCESS)
      {
        #Assign user
        $this->assign($user);
        
        #unset password
        $this->password = null;

        #Set global
        Globals::$userId = $this->id;
        Globals::$userRole = $this->role;
        Globals::$userLanguage = $this->locale;
        
        #change language preference
        if($this->lang->currentLocale() != $this->locale)
        {
          $this->lang->setLocale($this->locale);
        }

        return Constant::SUCCESS;
      }

      #Unable to set cookie session
      throw new Exception (Constant::ERROR_MESSAGE);
    }
  }
?>