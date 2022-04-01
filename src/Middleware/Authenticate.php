<?php
    namespace Middleware;
    use Services\IAuthService;

class Authenticate
{
    protected $auth;
    
    public function __construct(IAuthService $auth)
    {
        $this->auth = $auth;
    }

    //In construction
}
