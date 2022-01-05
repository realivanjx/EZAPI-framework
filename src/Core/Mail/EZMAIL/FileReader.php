<?php
    namespace Core\Mail\EZMAIL;

    class FileReader implements IFileReader
    {
        public function read(string $path) : string
        {
            return file_get_contents($path);
        }
    }