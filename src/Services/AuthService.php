<?php
  namespace Services;
  use Core\Exceptions\ApiError;
  use Core\Service;
use Models\User;
use Repositories\IUserRepository;
  
    
  class AuthService extends Service implements IAuthService
  {
    protected $_user;

    public function __construct(IUserRepository $userRepository)
    {
      $this->_user = $userRepository;
    }

    public function authenticate(object $input) : string
    {
      if(empty($input->usernameOrEmail))
      {
        throw new ApiError("Username or email is required");
      }

      if(empty($input->password))
      {
        throw new ApiError("password or email is required");
      }


      $user = new User();
      $user->email = "test";
      $user->password = "test";

      $this->_user->add($user);

      return "Hello from the user model login function";
    }

   
  }