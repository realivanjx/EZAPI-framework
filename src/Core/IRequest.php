<?php
    namespace Core;

    interface IRequest
    {
        function data() : mixed;
        
        function response(int $code, mixed $response) : void;
    }
?>