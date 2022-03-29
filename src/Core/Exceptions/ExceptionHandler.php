<?php
    namespace Core\Exceptions;
    use \Exception;
    use Core\IRequest;

    class ExceptionHandler extends Exception
    {
        public $request;

        public function __construct(IRequest $request)
        {
            $this->request = $request;
        }
        
    }