<?php

namespace Core\Mail\EZMAIL;

interface ILogger
{
    public function log(string $format, ...$values) : void;
}