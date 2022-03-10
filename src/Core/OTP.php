<?php
    namespace Core;
    use Core\Database\Mysql\Mysql;
    use Core\Helper;
    use Core\Constant;
    use Exception;

    class OTP 
    {
        #Database fields and table name
        private static 
            $table = "otp",
            $id, //int 11
            $userId, //int 11
            $otp, //int 10
            $expirationDate; //datetime

        #Database object
        private static ?Mysql $db = null;


        #Constructor
        public static function initializeDB()
        {
            if(self::$db == null)
            {
                self::$db = new Mysql(self::$table);
            }
        }

        

        /**
         * @method get
         * @param int userId
         * @param int expiry set to 15 mins by default
         * @return int
         * @throws exceptions
         */
        public static function get(int $userId, int $expiry = 15) : int
        {
            self::initializeDB();

            #Generate a new token
            $otp = Helper::randomNumber(6);
            
            #Calculate expiry time
            $expiration = time() + ($expiry * 60);

            #Delete previous tokens if there are any
            self::delete($userId);
            
            #Save OTP
            if(self::$db->insert([
                "userId" => $userId,
                "otp" => $otp,
                "expirationDate" => date("c", $expiration)
            ])) return $otp;

            #Something went wrong
            throw new Exception (Constant::ERROR_MESSAGE);
        }


        /**
         * @method delete
         * @param int userId
         * @return bool
         */
        public static function delete(int $userId) : bool
        {
            self::initializeDB();

             if(self::$db->delete([
                "where" => "userId = ?",
                "bind" => [$userId]
            ])) return true;

            return false;
        }


        /**
         * @method validate
         * @param int userId
         * @param int otp
         */
        public static function validate(int $userId, int $otp) : string
        {
            self::initializeDB();
            
            #Select values
            $validate = self::$db->select([
                "where" => "userId = ? AND otp = ?",
                "bind" => [$userId, $otp]
            ]);    

            #Validate record
            if(empty($validate->expirationDate)) return Constant::INVALID_OTP;

            #Validate expiration date
            if(strtotime($validate->expirationDate) >=  time())
            { 
                #Delete token when validated
                self::delete($userId);

                return Constant::SUCCESS;
            }

            #Token Expired
            self::delete($userId);

            return Constant::OTP_EXPIRED;
        }
    }