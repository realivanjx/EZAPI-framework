<?php
    namespace Models;

    class User
    {
        public ?int $id;
        public ?int $roleId;
        public ?int $statusId;
        public string $fName;
        public string $lName;
        public string $email;
        public string $password;
        public string $locale;
        public string $createdAt;
        public string $updatedAt;
        public string $deletedAt;
    }
?>