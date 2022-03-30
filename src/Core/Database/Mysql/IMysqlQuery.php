<?php
    namespace Core\Database\Mysql;

    interface IMysqlQuery
    {
        function insert(array $rows, string $table = null) : bool;
        function select(array $conditions, string $table = null) : object;
        function delete(array $conditions, string $table = null) : bool;
        function update(array $conditions, string $table = null) : array;
        function query(array $conditions, string $table = null) : array;
        function lastInsertedId() : int;
        function tableColumns(string $table = null) : array;
        function tableType(string $fieldName, string $table = null, bool $parse = true) : array;

        function find(array $array, string $table = null) : object;
        function findFirst(array $array, string $table = null) : object;
        function findLast(array $array, string $table = null) : object;
        function search(array $array, int $limit = 30, int $offset = 0, string $order = "id DESC", string $table = null) : object;
        function deleteId(int $id, string $table = null) : array;
        function deleteBy(array $condition, string $table = null) : array;
        function saveValues(array $array, string $table = null) : bool;
        function updateById(int $id, array $array, string $table = null) : string;
        function updateBy(array $updateBy, array $array, string $table = null) : string;
        function tableFields(string $table = null) : array;
        function save() : bool;

    }
?>