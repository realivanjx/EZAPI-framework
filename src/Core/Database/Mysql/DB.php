<?php
    namespace Core\Database\Mysql;
    use Core\Constant;
    use PDO;
    use PDOException;
    use Exception;


     /**
    * @comment Please report any errors so that our team can fix them as soon as possible.
    */

    class DB 
    {
        public 
            $_conn, 
            $table, 
            $_lastInsertedId;

        public 
            $dateFormat = "Y-m-d",
            $dateTimeFormat = "Y-m-d H:i:s";

        public function __construct(string $table) 
        {
            $this->table = $table;
            
            #Connect to the db only if needed
            if(empty($this->_conn))
            {
                $this->connect();
            }
        }


        /**
         * @method connect
         * @throws PDOException
         * Database connection.
         */
        protected function connect() : void
        {return;
            try 
            { 
                $config = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_CASE => PDO::CASE_NATURAL,
                    PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
                    PDO::MYSQL_ATTR_INIT_COMMAND => sprintf(
                        "SET NAMES %s;SET time_zone ='%s'", 
                        "utf8", 
                        CURRENT_TIMEZONE
                    )#set timezone to match the default framework timezone
                ];


                $this->_conn = new PDO(sprintf(
                    "mysql:host=%s;dbname=%s", 
                    EZENV['DB_HOST'], 
                    EZENV['DB_NAME']),  
                    EZENV['DB_USER'], 
                    EZENV['DB_PASSWORD'],
                    $config
                );
            }
            catch(PDOException $e)
            {
                if(!EZENV["PRODUCTION"])
                {
                   die(print_r($e->getMessage()));
                }

                throw new Exception($e->getMessage());
            }
        }	


        /**
         * @method insert
         * @param array rows
         * @return boolean
         * @throws excaption
         */
        public function insert(array $rows, string $table = null) : bool
        {
            $table = !empty($table) ? $table : $this->table;

            $setValues = null;
        
            foreach($rows as $column => $value) 
            {
              $setValues .= "{$column}=?,";
            }

            $setValues = rtrim($setValues, ',');

            $query = "INSERT INTO {$table} SET {$setValues}";

            $stmt = $this->_conn->prepare($query);

            $bindCount = 1;
            foreach($rows as $col => $val) 
            {
                $stmt->bindValue($bindCount, trim($val));

                $bindCount++;
            }

            if($stmt->execute())
            {
                $this->_lastInsertedId = $this->_conn->lastInsertId();

                return true;
            }

            if(!EZENV["PRODUCTION"] && !empty($stmt->error))
            {
                throw new Exception($stmt->error);
            }

            return false;
        }


        /**
         * @method select
         * @param array conditions [select, where, order, limit, offset, bind[]]  
         * @param string table
         * @return object
         * @throws excaption
         */
        public function select(array $conditions, string $table = null) : object
        {
            $table = !empty($table) ? $table : $this->table;
            $errors = [];

            //VALIDATIONS
            if(array_key_exists('where', $conditions))
            {
                if(!array_key_exists('bind', $conditions))
                {
                    $errors['bind'] = "You must bind your values";
                }

                if(!strpos($conditions['where'], "?"))
                {
                    $errors['where'] = "Your condition must end with a ?";
                }
            }

            if(!empty($errors))
            {
                return (object) $errors;
            }

            $select = !empty($conditions['select']) ? $conditions['select'] : "*";
            $where = !empty($conditions['where']) ? "WHERE {$conditions['where']}" : null;
            $orderBy = !empty($conditions['order']) ? "ORDER BY {$conditions['order']}" : "ORDER BY id DESC";
            $limit = !empty($conditions['limit']) ? "LIMIT {$conditions['limit']}"  : null;
            $offset = !empty($conditions['offset']) ? "OFFSET {$conditions['offset']}" : null;
            $between = !empty($conditions['between']) ? "BETWEEN {$conditions['between']}" : null;

            $query = "SELECT {$select} FROM {$table} {$where} {$between} {$orderBy} {$limit} {$offset}";

            $stmt = $this->_conn->prepare($query);
            
            if(!empty($conditions['bind']))
            {
                $bindCount = 1;
                foreach($conditions['bind'] as $value) 
                {
                    $stmt->bindValue($bindCount, trim($value));

                    $bindCount++;
                }
            }

            if($stmt->execute())
            {
                if($stmt->rowCount() > 1)
                {
                    return (object) $stmt->fetchAll(PDO::FETCH_OBJ);
                }

                return (object) $stmt->fetch(PDO::FETCH_OBJ);
            }

            if(!EZENV["PRODUCTION"] && $stmt->error)
            {
                throw new Exception($stmt->error);
            }

            return [];
        }


        /**
         * @method delete
         * @param array conditions [where, bind[]]
         * @param string table
         * @return array
         * @throws exceptions
         */
        public function delete(array $conditions, string $table = null) : bool
        {
            $table = !empty($table) ? $table : $this->table;

            //VALIDATIONS
            if(array_key_exists('where', $conditions))
            {
                if(!array_key_exists('bind', $conditions))
                {
                    throw new Exception ("You must bind your values");
                }

                if(!strpos($conditions['where'], "?"))
                {
                    throw new Exception ("Your condition must end with a ?");
                }
            }
           
            $query = "DELETE FROM {$table} WHERE {$conditions['where']}";

            $stmt = $this->_conn->prepare($query);
           
            $bindCount = 1;
            foreach($conditions['bind'] as $value) 
            {
                $stmt->bindValue($bindCount, trim($value));

                $bindCount++;
            }

            if($stmt->execute())
            {
               return true;
            }

            if(!EZENV["PRODUCTION"] && $stmt->error)
            {
                throw new Exception($stmt->error);
            }

            return false;
        }


        /**
         * @method update
         * @param array conditions [where, bind[]]
         * @param string table
         * @return array
         * @throws exceptions
         */
        public function update(array $conditions, string $table = null) : array
        {
            $table = !empty($table) ? $table : $this->table;
            $errors = [];

            //VALIDATIONS
            if(!array_key_exists('set', $conditions))
            {
                $errors['set'] = "You must set your values";
            }

            if(!array_key_exists('bind', $conditions))
            {
                $errors['bind'] = "You must bind your values";
            }

            if(array_key_exists('where', $conditions))
            {
                if(!strpos($conditions['where'], "?"))
                {
                    $errors['where'] = "Your condition must end with a ?";
                }
            }

            if(!empty($errors))
            {
                return $errors;
            }
           
            $query = "UPDATE {$table}  SET {$conditions['set']} WHERE {$conditions['where']}";

            $stmt = $this->_conn->prepare($query);

            $bindCount = 1;
            foreach($conditions['bind'] as $value) 
            {
                $stmt->bindValue($bindCount, trim($value));

                $bindCount++;
            }

            if($stmt->execute())
            {
                return ["message" => Constant::SUCCESS];
            }

            if(!EZENV["PRODUCTION"] && !empty($stmt->error))
            {
                throw new Exception($stmt->error);
            }

            return [];
        }


        /**
         * @method query
         * @param array conditions [query, bind[]]
         * @param string table
         * @return array
         * @throws exceptions
         */
        public function query(array $conditions, string $table = null) : array
        {
            $table = !empty($table) ? $table : $this->table;
            $errors = [];

            //VALIDATIONS
            if(!array_key_exists('query', $conditions))
            {
                $errors['query'] = "You must build a proper MYSQLI query: EX: SELECT * FROM EXAMPLE";
            }            

            if(!empty($errors))
            {
                return $errors;
            }

            $stmt = $this->_conn->prepare($conditions['query']);

            if(array_key_exists('bind', $conditions))
            {
                $bindCount = 1;
                foreach($conditions['bind'] as $value) 
                {
                    $stmt->bindValue($bindCount, trim($value));
    
                    $bindCount++;
                }
            }

            if($stmt->execute())
            {
                $queryStmt = explode(" ", $conditions['query']);

                if($queryStmt[0] == "SELECT" || $queryStmt[0] == "select")
                {
                    if($stmt->rowCount() > 1)
                    {
                        return $stmt->fetchAll(PDO::FETCH_OBJ);
                    }

                    return $stmt->fetch(PDO::FETCH_OBJ);
                }

                return ["message" => "success"];
            }

            if(!EZENV["PRODUCTION"] && $stmt->error)
            {
                throw new Exception($stmt->error);
            }

            return [];
        }

        
        /**
         * @method lastInsertedId
         * @return integer
         */
        public function lastInsertedId() : int
        {
            return (int)$this->_lastInsertedId;
        }


         /**
         * @method showColumns
         * @param string table
         * @return array
         * @throws excaption
         */
        public function tableColumns(string $table = null) : array
        {
            $table = !empty($table) ? $table : $this->table;

            $query =  "SHOW COLUMNS FROM {$table}";

            $stmt = $this->_conn->prepare($query);

            if($stmt->execute())
            {
                return $stmt->fetchAll(PDO::FETCH_OBJ);
            }

            if(!EZENV["PRODUCTION"] && !empty($stmt->error))
            {
                throw new Exception($stmt->error);
            }

            return  [];
        }



        /**
         * @method tableType
         * @param string fieldName
         * @param string table
         * @param bool parse
         * @return array
         * @throws exception
         */
        public function tableType(string $fieldName, string $table = null, bool $parse = true) : array
        {
            (array) $tableType = [];

            (string) $table = !empty($table) ? $table : $this->table;

            (string) $query =  "SHOW COLUMNS FROM {$table}";

            $stmt = $this->_conn->prepare($query);

            if($stmt->execute())
            {
                (object) $cols = $stmt->fetchAll(PDO::FETCH_OBJ);

                foreach($cols as $key => $value)
                {
                    if($value->Field == $fieldName)
                    {
                        $tableType[$value->Field] = $value->Type;

                        continue;
                    }
                }
            }

            if($parse)
            {
                #enum
                if (preg_match("/enum/", $tableType[$fieldName]))
                {
                    preg_match('#^enum\((.*?)\)$#ism', $tableType[$fieldName], $matches);

                    $tableType = str_getcsv($matches[1], ",", "'");

                    /**
                     * because 0 is considered null or false enums start from 1. 
                     * We have to modify our array to meet this requirement.
                     */

                    (int) $enumNumber = 1;
                    (array) $tempArray = [];

                    foreach($tableType as $enumKey => $enumValue)
                    {
                        $tempArray[$enumNumber] = $enumValue;

                        $enumNumber ++;
                    }

                    $tableType = $tempArray;
                }
            }

            if(!EZENV["PRODUCTION"] && !empty($stmt->error))
            {
                throw new Exception($stmt->error);
            }

            return  $tableType;
        }

        /**
         * TODO
         * 1-table joints
         * 2- DB backup
         * 3-Create table
         * 4- alter table
         * 5- delete table
         */
    }