<?php
    namespace Repositories;
    use Models\User;


    class UserRepository extends BaseEntity implements IUserRepository
    {
        function list(int $offset, int $limit) : User
        {
            return new User;
        }

        function add(User $user) : User
        {
            return $this->insert($user);
        }

        function assign(object $user)
        {
            //Assign the values to the model
        }

        function edit(User $user) : void
        {
            $this->update($user);
        }

        function remove(int $userId) : void
        {
            $this->delete($userId);
        }

        function getById(?int $id) : User
        {
            return new User;
        }

        function getByUsername(string $username) : User
        {
            return new User;
        }

        function getByEmail(string $email) : User
        {
            return new User;
        }
        
    }


// genomic secuencing

// core => Controller
// routes => Service

// crud operations = 

// create
// read
// update
// delete


// UserRepository
// Irepository<BaseEntity>
// find(long id) : User
// insert(T entity) : User //generig interface
// update(T entity) : void
// Delete(T entity) : void
// save() : void


// operation -> sql -> HTTP
// create -> insert -> post
// read -> select -> get
// update -> update -> put
// delete -> delete -> delete

// ====================================

// one repository per business object
// because of single responsibility principle and
// a class should have one and only one reason to change

// example
// User -> IUserRepository -> UserRepository = talks to db

// =================================

// provide a contract or interface

// use generic implementations