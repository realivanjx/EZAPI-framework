<?php
    namespace Core;


    class Crypto
    {        
        /**
         * @method randomHash
         * @return string 
         */
        public static function randomHash() : string
        {
            return md5(bin2hex(random_bytes(16)));
        }


        /**
         * @method randomToken
         * @return string 
         */
        public static function randomToken() : string
        {
            return base64_encode(openssl_random_pseudo_bytes(32));
        }



        public function AESencrypt($psw, $rawdata)
        {
            // CBC has an IV and thus needs randomness every time a message is encrypted
            $method = 'aes-256-cbc';
        
            // Must be exact 32 chars (256 bit)
            $key = substr(hash('sha256', $psw, true), 0, 32);
        
            // IV must be exact 16 chars (128 bit)
            $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        
            //Return encrypted data
            return base64_encode(openssl_encrypt($rawdata, $method, $key, OPENSSL_RAW_DATA, $iv));
        }

        public function AESdecrypt($psw, $dataencrypted)
        {
            // CBC has an IV and thus needs randomness every time a message is encrypted
            $method = 'aes-256-cbc';
        
            // Must be exact 32 chars (256 bit)
            // You must store this secret random key in a safe place of your system.
            $key = substr(hash('sha256', $psw, true), 0, 32);
        
            // IV must be exact 16 chars (128 bit)
            $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        
            //return decrypted data
            return openssl_decrypt(base64_decode($dataencrypted), $method, $key, OPENSSL_RAW_DATA, $iv);
        }
    }