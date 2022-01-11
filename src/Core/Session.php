<?php
    namespace Core;
    use Core\Database\Mysql\Mysql;
    use Core\Helper;
    use Core\Constant;
    use Core\Crypto;
    use Core\Mail\Mail;
    use Core\Exceptions\ApiError;

    /**
     * As you may already know there are no sessions in rest apis therefore we created our own 
     * session method which will keep the current user logged in and hangle auth extensions and more.
     */
    class Session 
    {
        /**
         * @method _db
         * @return instance 
         * @comment: Create a new db instance 
         * using the default table session.
         */
        private static function _db() 
        {
            return new Mysql("session");
        }


        /**
         * @method set
         * @param int userId
         * @return string 
         * @comment: 
         */
        public static function set(int $userId) : string
        {
            #Session random hash
            $sessionHash = Crypto::randomHash();

            #agent identifier random hash
            $agentHash = Crypto::randomHash();
        
            #Agent name
            $userAgent = Helper::getUserAgent();

            #User IP
            $userIP = Helper::publicIP();

            #db 
            $db = self::_db();

            $ezmail = new Mail();
        
            #Session timeout
            $sessionEnd = CURRENT_TIME + (USER_SESSION_EXPIRY * 60);

            /**
             * Stop debugging tools from making request to our server
             * while in EZENV["PRODUCTION"] for better security performance.
             */
            if(EZENV["PRODUCTION"] && in_array($userAgent, DEBUGING_TOOLS))
            {
                throw new ApiError (Constant::INVALID_AGENT_TOOL);
            }

            #Blocked ip addresses known as IP blacklist            
            if(EZENV["PRODUCTION"] && in_array($userIP, IP_BLACKLIST))
            {
                throw new ApiError (Constant::BANNED_IP_ADDRESS);
            }

            #Database query
            $session = $db->select([
                "where" => "userId = ?",
                "bind" => [$userId]
            ]);

            if(!empty($session->userId))
            {
                $validateSession = [];

                # one result
                if(!isset($session->{0}))
                {
                     #Determine whether the session is active or inactive
                     if(strtotime($session->expire) >=  CURRENT_TIME)
                     {
                        $validateSession["active"][] = (array) $session;
                     }
                     else
                     {
                        $validateSession["inactive"][] = (array) $session;
                     }
                }

                #More than one result
                if(isset($session->{0}))
                {
                    foreach($session as $userSession)
                    {
                        if(empty($userSession->expire)) continue;
                       
                        #Determine whether the session is active or inactive
                        if(strtotime($userSession->expire) >=  CURRENT_TIME)
                        {
                            $validateSession["active"][] = (array) $userSession;
                        }
                        else
                        {
                            $validateSession["inactive"][] = (array) $userSession;
                        }
                    }
                }
               

                #Validate current sessions
                if(!empty($validateSession["active"]))
                {
                    #Check whether the user can login in multiple devices or not
                    if(count($validateSession["active"]) >= 1 && !ALLOW_MULTI_LOGIN)
                    {
                        throw new ApiError (Constant::MULTI_LOGIN_NOT_ALLOWED);
                    }

                    #Count the number of devices currently logged
                    if(count($validateSession["active"]) >= MULTI_LOGIN_COUNT)
                    {
                        throw new ApiError (Constant::MAXIMUN_LOGIN_COUNT_REACHED);
                    }
                }

                #Alert the user with of new device
                if(!Cookie::exists(USER_AGENT_NAME))
                {
                    #Userid is id here since we are calling the users table.ss
                    $user = $db->select([
                        "where" => "id = ?",
                        "bind" => [$userId],
                    ], "user");

                    $ezmail->newDeviceNotification(
                        $user->username, 
                        $user->email, 
                        $userIP, 
                        $userAgent
                    );
                }
            }
            


            /**
             * If the Agent identifier cookie is present 
             * we will use the same token identifier to extend
             * this cookie. 
             */
            if(Cookie::exists(USER_AGENT_NAME))
            {
                #Use the same agentHash for the new cookie
                $agentHash = Cookie::get(USER_AGENT_NAME);
            }

            /**
             * If the session identifier cookie is present 
             * we will use the same token identifier to extend
             * this cookie. 
             */
            if(Cookie::exists(USER_SESSION_NAME))
            {
                $sessionHash = Cookie::get(USER_SESSION_NAME);
            }

            #Set Agent identifier to 1 year
            $agent_cookie_time = CURRENT_TIME + (31556926); #time in seconds for one year
        
            //Set Cookie 
            if(Cookie::set(USER_SESSION_NAME, $sessionHash, $sessionEnd))
            {
                if(Cookie::set(USER_AGENT_NAME, $agentHash, $agent_cookie_time))
                {
                    $sessionUpdate = $db->select([
                        "where" => "userId = ? AND agentName = ? AND agentIdentifier = ?",
                        "bind" => [$userId, $userAgent, $agentHash]
                    ]);

                    if(!empty($sessionUpdate->userId))
                    {
                        #Update DB
                        $confirmation = $db->update([
                            "set" => "userId = ?, session = ?, agentName = ?, agentIdentifier = ?, ip = ?, time = ?, expire = ?",
                            "where" => "userId = ? AND agentName = ? AND agentIdentifier = ?",
                            "bind" => [
                                $userId,
                                $sessionHash,
                                $userAgent,
                                $agentHash,
                                $userIP, 
                                TIMESTAMP,
                                date($db->dateTimeFormat, $sessionEnd),
                                $userId,
                                $userAgent,
                                $agentHash
                            ]
                        ]);

                        if(isset($confirmation["message"]) && $confirmation["message"] == Constant::SUCCESS)
                        {
                            return Constant::SUCCESS;
                        }

                        return Constant::ERROR;
                    }

                    #Insert a new record
                    $confirmInsert = $db->insert([
                        "userId" => $userId, 
                        "session" => $sessionHash, 
                        "agentName" => $userAgent,
                        "agentIdentifier" => $agentHash,
                        "ip" => $userIP, 
                        "time" => TIMESTAMP,
                        "expire" => date($db->dateTimeFormat, $sessionEnd)
                    ]);

                    if($confirmInsert)
                    {
                        return Constant::SUCCESS;
                    }
                }

                #Delete Cookie on agent Identifier error
                Cookie::delete(USER_SESSION_NAME);
            }
           
            return Constant::UNABLE_TO_SET_SESSION;
        }


      

        /**
         * @method get
         * @return int userId
         */
        public static function get()
        {
            #Attempt an auto authentication
            if(Cookie::exists(USER_SESSION_NAME) && Cookie::exists(USER_AGENT_NAME))
            {
                $session = self::_db()->select([
                    "select" => "session, userId, expire",
                    "where" => "session = ? AND agentIdentifier = ?",
                    "bind" => [Cookie::get(USER_SESSION_NAME), Cookie::get(USER_AGENT_NAME)],
                    "limit" => 1
                ]);
                
                #If not empty validate session and return userId                
                if(!empty($session) && strtotime($session->expire) >=  CURRENT_TIME)
                {
                    return $session->userId;
                }
            }
        }
    

        /**
         * @method extend
         * @return bool
         */
        public static function extend() : bool
        {
            #If both cookies are not present authentication is required.
            if(!Cookie::exists(USER_SESSION_NAME) || !Cookie::exists(USER_AGENT_NAME))
            {
                return false;
            }

            #get current session hash
            $currentSession = Cookie::get(USER_SESSION_NAME);

            #Get Agent identifier
            $agentIdentifier = Cookie::get(USER_AGENT_NAME);

            #get userAgent
            $userAgent = Helper::getUserAgent();

            #get IP
            $userIP = Helper::getClientIP();

            #find session
            $session = self::_db()->select([
                "where" => "session = ? AND agentIdentifier = ?",
                "bind" => [$currentSession, $agentIdentifier],
                "limit" => 1
            ]);

            #Authentication required
            if(empty($session->expire)) return false;

            #Calculate expiration time
            $sessionEnd = CURRENT_TIME + (USER_SESSION_EXPIRY * 60);

            #Validate session time to see if auth is required
            if(strtotime($session->expire) <  CURRENT_TIME) return  false;

            #Extend session
            self::_db()->update([
                "set" => "expire = ?",
                "where" => "session = ? AND agentIdentifier = ?",
                "bind" => [date(self::_db()->dateTimeFormat, $sessionEnd), $currentSession, $agentIdentifier]
            ]);
        
            #Set Cookie 
            if(!Cookie::set(USER_SESSION_NAME, $session->session, $sessionEnd)) return false;

            #Assign Global variables
            $userRole = self::_db()->select([
                "select" => "role, locale",
                "where" => "id = ?",
                "bind" => [$session->userId],
                "limit" => 1
            ], "user");

            #Validate values
            if(empty($userRole)) return false;

            #set globals
            Globals::$userId = $session->userId;
            Globals::$userRole = $userRole->role;
            Globals::$userLanguage = $userRole->locale;

            #all done
            return  true;
        }

        

        /**
         * We do not delete records from the DB we just update the session. Because we will keep track of devices
         */
        public static function delete()
        {
            $db = self::_db();

            #Find user from cookie
            if(Cookie::exists(USER_SESSION_NAME) && Cookie::exists(USER_AGENT_NAME))
            {
                $sessionToDistroy = Cookie::get(USER_SESSION_NAME);

                $sessionExpired = CURRENT_TIME - (USER_SESSION_EXPIRY * 60);

                if(Cookie::delete(USER_SESSION_NAME))
                {
                   $confirmUpdate = $db->update([
                        "set" => "session = ?, expire = ?",
                        "where" => "session = ? AND agentIdentifier = ?",
                        "bind" => [
                            null,
                            date($db->dateTimeFormat, $sessionExpired),
                            $sessionToDistroy,
                            Cookie::get(USER_AGENT_NAME)
                        ]
                    ]);

                    if(isset($confirmUpdate["message"]) && $confirmUpdate["message"] == Constant::SUCCESS)
                    {
                        //set globals to null
                        Globals::$userId = null;

                        return Constant::SUCCESS;
                    }
                }

                return Constant::ERROR;
            }
        }
    }