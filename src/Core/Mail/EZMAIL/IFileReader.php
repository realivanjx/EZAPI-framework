<?php

namespace Core\Mail\EZMAIL;

    interface IFileReader
    {
        public function read(string $path) : string;
    }