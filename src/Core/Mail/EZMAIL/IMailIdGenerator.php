<?php

namespace Core\Mail\EZMAIL;

interface IMailIdGenerator
{
    public function generate() : string;
}