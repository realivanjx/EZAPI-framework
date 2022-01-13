<?php
    namespace Core;
    use Core\Database\Mysql\Mysql;

    /**################################
    * Role Base Access Control
    * ###############################*/
    
    class RBAC
    {
        #role list
        private static  $roles = [
            "USER",
            "GUEST",
            "ADMIN",
        ];

        /**
         * @method roles
         * @return array 
         */
        public static function roles()
        {
            return self::$roles;
        }

        public static function isViewAllow(string $role,  string $route, string $action) : bool
        {
            if(!$role) return false;

            $_db = new Mysql("rbac");

            #Remove backslash from the class
            if(strpos($route, "\\"))
            {
                $route = explode("\\", $route);
                $route = end($route);
            }

            #DB query
            $hasAccess = $_db->select([
                "where" => "role = ? AND route = ?",
                "bind" => [$role, $route],
                "limit" => 1
            ]);

            if(empty($hasAccess->action)) return false;

            if($hasAccess->action == "*" || strpos($hasAccess->action, $action) !== false)
            {
                if(strpos($hasAccess->permission, "VIEW") !== false)
                {
                    return true;
                }
            }

            return false;
        }




        // public function isCreateAllow($role, $route, $action)
        // {
        //     //permission needed
        //     $hasAccess = $this->_db->select([
        //         "where" => "role = ? AND route = ?",
        //         "bind" => [$role, $route],
        //         "limit" => 1
        //     ]);

        //     if(!empty($hasAccess) && $hasAccess->action == "*" || strpos($hasAccess->action, $action) && $hasAccess->mermission == "CREATE")
        //     {
        //         return true;
        //     }

        //     return false;

        // }


        // public function isEditAllow($route, $action, $userId)
        // {

        // }

        // public function isDeleteAllow($route, $action, $userId)
        // {

        // }

        
        

       
        // public function addRole($array)
        // {

        //     if();

        //     $this->_db->insert([
        //         'role' => $array["role"], 
        //         'route' => $array["route"], 
        //         'action' => $array["action"], 
        //         'permission' => $array["permission"], 
        //         'description' => $array["description"]
        //     ]);
            
        // }

        // public function editRole()
        // {
        //     $_db->update([
        //         "set" => "expire = ?",
        //         "where" => "session = ? AND agent = ? AND ip = ?",
        //         "bind" => [date('c', $sessionEnd), $currentSession, $userAgent, $userIP]
        //     ]);
        // }

        // public function deleteRole()
        // {
        //     $_db->delete([
        //         "where" => "agent = ? AND ip = ?",
        //         "bind" => [Helper::getUserAgent(), Helper::getClientIP()]
        //     ]);
        // }


        // public function listRoles()
        // {
        //     $hasAccess = $this->_db->select([
        //         "where" => "userId = ?",
        //         "bind" => [$userId]
        //     ]);
        // }

    }