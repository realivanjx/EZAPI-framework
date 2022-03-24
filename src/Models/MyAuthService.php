<?php

namespace Models;
use Models\IUserModel;

class MyAuthService implements IAuthService
{
    private IUserModel $_userModel;

    public function __construct(IUserModel $userModel)
    {
        $this->_userModel = $userModel;
    }

    public function authenticate() : string
    {
        return $this->_userModel->login((object)[]);
    }
}

?>