<?php
    namespace Repositories;
    use Core\Database\Mysql\IDatabase;
    use Models\User;


    class UserAuthRepository implements IUserAuthRepository
    {
        protected IDatabase $db;
        private const COLUMN_NAME = "user";

        public function __construct(IDatabase $db)
        {
            $this->db = $db;
        }


        function findFirst(string $identifier, string $key) : User
        {
            return new User;
        }

        /**
         * @method findFIrst
         * @param array  ex: ["id" => "value"]
         * @return object
         */
        public function findFirsts(array $identifier, string $key) : User
        {
           return new user;
        }
    }



// UserRepository
// Irepository<BaseEntity>
// find(long id) : User
// insert(T entity) : User //generig interface
// update(T entity) : void
// Delete(T entity) : void
// save() : void
