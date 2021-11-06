<?php
    namespace Core\Database;
    use Core\Database\Mysql;
    use \PDO;

    class MysqlQuery extends Mysql
    {

        public function __construct(string $table)
        {
            parent::__construct($table);
        }


        
        /**
             * @method findFIrst
             * @param array  ex: ["id" => "value"]
             * @return array
             */
            public function find(array $array, $table = null) : object
            {
                return $this->select([
                    "where" => key($array) . "= ?",
                    "bind" => [current($array)]
                ], $table);
            }



            /**
             * @method findFIrst
             * @param array  ex: ["id" => "value"]
             * @return object
             */
            public function findFirst(array $array, $table = null) : object
            {
                return $this->select([
                    "where" => key($array) . "= ?",
                    "bind" => [current($array)],
                    "limit" => 1
                ], $table);
            }


            /**
             * @method findLast
             * @param array  ex: ["id" => "value"]
             * @return object
             */
            public function findLast(array $array, $table = null) : object
            {
                return $this->select([
                    "where" => key($array) . "= ?",
                    "bind" => [current($array)],
                    "limit" => 1,
                    "order" => "id ASC"
                ], $table);
            }


            /**
             * @method search
             * @param array Ex: ["username" => "yourName"]
             * @param int limit, offset
             * @param string order
             * @return array
             */
            public function search(array $array, int $limit = 30, int $offset = 0, string $order = "id DESC", $table) : array
            {
                return $this->select([
                    "where" => key($array) . " like %?%",
                    "bind" => [current($array)],
                    "limit" => $limit,
                    "offset" => $offset,
                    "order" => $order
                ], $table);
            }


            /**
             * @method deleteId
             * @param int id
             * @return string
             */
            public function deleteId(int $id, $table = null) : array
            {
                return $this->delete([
                    "where" => "id = ?", 
                    "bind" => [$id]
                ], $table);
            }

            /**
             * @method deleteId
             * @param int id
             * @return string
             */
            public function deleteBy(array $condition, $table = null) : array
            {
                return $this->delete([
                    "where" => key($condition) . " = ?", 
                    "bind" => [end($condition)]
                ], $table);
            }


            /**
             * @method assign
             * 
             */
            public function assign(object $object) : void 
            {
                foreach($object as $key => $value) 
                {
                    if(property_exists($this, $key))
                    {
                        $this->$key = $value;
                    }
                }
            }



            /**
             * @method saveValues
             * @param array []
             * @return boolean
             */
            public function saveValues(array $array, string $table = null) : bool 
            {
                #Build array
                $values = [];
                foreach($this->db->tableColumns($table) as $tableColumns)
                {
                    #Hash password by default
                    if($tableColumns->Field == "password")
                    {
                        $values[$tableColumns->Field] = password_hash($array["password"], PASSWORD_DEFAULT);
                    }
                    else if($tableColumns->Field == "date" || $tableColumns->Field == "created")
                    {
                        $values[$tableColumns->Field] = TIMESTAMP;
                    }
                    else
                    {
                        #Insert only Filled Values
                        if(!empty($array[$tableColumns->Field]))
                        {
                            $values[$tableColumns->Field] = $array[$tableColumns->Field];
                        }
                    }
                }

                #Insert values
                if($this->insert($values, $table)) return true;

                return false;
            }


            /**
             * @method saveValues
             * @param array []
             * @return string
             */
            public function updateById(int $userId, array $array, string $table = null) : string
            {
                if(empty($userId) || empty($array)) return "Empty values are not allowed";

                $tableFields = $this->tableFields($table);

                $set = null;
                $values = [];
                foreach($array as $key => $value)
                {
                    if(!in_array($key, $tableFields))
                    {
                    return "Invalid value provided: {$key}";
                    }

                    if($key == "password")
                    {
                        $values[] = password_hash($value, PASSWORD_DEFAULT);
                    }
                    else
                    {
                        $values[] = $value;
                    }

                    $set .= "{$key} = ?,";
                }

                $set = rtrim($set, ',');

                array_push($values, $userId);

                if($this->update([
                    "set" => $set,
                    "where" => $this->modelVariables['table'] == "user" || $table == "user" ? "id = ?" : "userId = ?",
                    "bind" => $values
                    ], $table)
                )

                return Constant::SUCCESS;
            }

            /**
             * @method saveValues
             * @param array []
             * @return string
             */
            public function updateBy(array $updateBy, array $array, string $table = null) : string
            {
                if(empty($array)) return "Empty values are not allowed";

                $tableFields = $this->tableFields($table);

                $set = null;
                $values = [];
                foreach($array as $key => $value)
                {
                    if(!in_array($key, $tableFields))
                    {
                    return "Invalid value provided: {$key}";
                    }

                    if($key == "password")
                    {
                        $values[] = password_hash($value, PASSWORD_DEFAULT);
                    }
                    else
                    {
                        $values[] = $value;
                    }

                    $set .= "{$key} = ?,";
                }

                $set = rtrim($set, ',');

                array_push($values, end($updateBy));

                if($this->update([
                    "set" => $set,
                    "where" => sprintf("%s = ?", key($updateBy)),
                    "bind" => $values
                    ], $table)
                )

                return Constant::SUCCESS;
            }


            /**
             * @method tableFields
             * @return array
             */
            public function tableFields(string $table = null) : array
            {
                $fields = [];
                foreach($this->db->tableColumns($table) as $tableColumns)
                {
                $fields[] = $tableColumns->Field;
                }

                return $fields;
            }


            public function save() : bool
            {
                #Build array
                $values = [];
                foreach($this->db->tableColumns() as $tableColumns)
                {
                    #Hash password by default
                    if($tableColumns->Field == "password")
                    {
                        $values[$tableColumns->Field] = password_hash($this->{$tableColumns->Field}, PASSWORD_DEFAULT);
                    }
                    else if($tableColumns->Field == "date" || $tableColumns->Field == "created")
                    {
                        $values[$tableColumns->Field] = TIMESTAMP;
                    }
                    else
                    {
                        #Insert only Filled Values
                        if(!empty($this->{$tableColumns->Field}))
                        {
                            $values[$tableColumns->Field] = $this->{$tableColumns->Field};
                        }
                    }
                }
    
                #Insert values
                if($this->insert($values)) return true;

                return false;
            }


            /**
             * @method vars
             * @param boolean hideSensitive (obtional)
             * @return array results
             * @comment: This method will return all variables within the model called.
             * This method is useful when you need to return all the columns from a database
             */
            public function vars(bool $hideSensitive = true) : array
            {
                $vars = [];

                #Find all public variables
                foreach($this->modelVariables as $key => $value)
                {
                    $vars[$key] = $this->{$key};
                }

                /**
                 * you must blacklist public variables within this class manually
                 * Currently we are using 3 public db, lang, modelVariables so we unset
                 * them fromt he results. 
                 */
                if(array_key_exists("db", $vars)) unset($vars["db"]);
                if(array_key_exists("lang", $vars)) unset($vars["lang"]);
                if(array_key_exists("modelVariables", $vars)) unset($vars["modelVariables"]);

                #Remove blacklisted
                if(array_key_exists("table", $vars) && $hideSensitive)
                {
                    #unset id and password from the table users for extra security
                    if($vars["table"] == "user")
                    {
                        unset($vars["password"]);
                        unset($vars["twoFactorAuth"]);
                    }

                    unset($vars["table"]);
                    unset($vars["di"]);
                }

                return $vars;
            }
    }