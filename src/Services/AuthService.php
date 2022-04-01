<?php
  namespace Services;
  use Core\Exceptions\ApiError;
  use Models\User;
  use Repositories\IUserAuthRepository;
  
    
  class AuthService implements IAuthService
  {
    protected IUserAuthRepository $m_authRepository;
    protected User $m_user;

    public function __construct(IUserAuthRepository $authRepository)
    {
      $this->m_authRepository = $authRepository;
    }

    
    public function authenticate(string $usernameOrEmail, string $password, bool $rememberMe) : object
    {
      #Check whether the user entered a valid email otherwise treat it as an username
      $identifier = filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL) ? "email" : "username";
      
      #Attempt to find the user in the database with the username or email provided
      $this->m_user = $this->m_authRepository->findFirst($identifier, $usernameOrEmail);     

      #User not found
      if(empty($this->m_user->id) || !password_verify($password, $this->m_user->password))
      {
        throw new ApiError("invalid_username_email_or_password");
      }

      // if($this->m_user->status == "banned")
      // {
      //   throw new ApiError ("account_banned");
      // }

      // if($this->m_user->status == "inactive")
      // {
      //   throw new ApiError ("account_inactive");
      // }

      //remove password field
      unset($this->m_user->password);


      return $this->m_user;
    }

   
  }