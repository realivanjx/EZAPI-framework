<?php
    namespace Models;

    class User
    {
        public ?int $id = 1;
        public ?int $roleId;
        public ?int $statusId;
        public string $fName;
        public string $lName;
        public string $email = "test";
        public string $username = "test";
        public string $password = "$2y$10$.aUQiLdhT10EOEji/Nqf/OlNq6EfbrEgox84s1/y.beQexZKJ4OfK"; //test
        public string $locale;
        public string $createdAt;
        public string $updatedAt;
        public string $deletedAt;
    }
?>