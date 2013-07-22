<?php

namespace Yamato\Core\Database;

interface DbDriver {
    public function connect();
    public function beginTransaction();
    public function commit();
    public function rollback();
    public function query($query);
    public function execute($bind_values = null, $statement = null);
    public function prepare($query);
    public function lastInsertId($name);
    public function fetch($statement = null, $options = null);
    public function fetchAll($statement = null, $options = null);
}

?>
