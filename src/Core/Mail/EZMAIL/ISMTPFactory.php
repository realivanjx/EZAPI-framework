<?php

namespace Core\Mail\EZMAIL;

interface ISMTPFactory
{
    public function create(
        string $hostName,
        int $portNumber,
        float $timeout = 30
    ) : ISMTP;
}