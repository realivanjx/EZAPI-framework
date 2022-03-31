<?php
    namespace Repositories;

    //This class talks to mySQL
    class BaseEntity
    {
        public function find(object $id) : object
        {
            return (object)[];
        }

        public function findFirst(object $id) : object
        {
            return (object)[];
        }


        public function insert(object $entity) : object
        {
            return (object)[];
        }
        public function update(object $entity) : void 
        {

        }
        public function Delete(int $userId) : void 
        {

        }
        
        public function save() : void 
        {

        }
    }