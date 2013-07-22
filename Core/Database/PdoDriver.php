<?php

namespace Yamato\Core\Database;

use Yamato\Core\Config as Config;

class PdoDriver implements DbDriver {
    protected $connection = null;
    protected $latest_statement = null;
    
    public function __construct() {
        $this->connect();
    }
    
    public function connect() {
        $config = Config::get('database.yml');
        $user = $config['database_user'];
        $password = $config['database_password'];
        $dsn = $this->createDsn($config);
        
        $connection = new \PDO($dsn, $user, $password);
        
        // 属性設定
        $connection->query("SET NAMES utf8;");
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        
        $this->connection = $connection;
    }
    
    protected function createDsn($config) {
        $database_name = $config['database_name'];
        $host = $config['database_host'];
        $dsn = "mysql:dbname={$database_name};host={$host}";
        return $dsn;
    }
    
    public function beginTransaction() {
        $this->connection->beginTransaction();
    }
    
    public function commit() {
        $this->connection->commit();
    }
    
    public function rollback() {
        $this->connection->rollback();
    }
    
    public function execute($bind_values = null, $statement = null) {
        (is_null($statement)) and ($statement = $this->latest_statement);
        
        $result = null;
        if($statement) {
            $result = $statement->execute($bind_values);
        }
        return $result;
    }
    
    public function prepare($query) {
        $this->latest_statement = $this->connection->prepare($query);
        return $this->latest_statement;
    }
    
    public function query($query) {
        return $this->connection->query($query);
    }
    
    public function lastInsertId($name = null) {
        return $this->connection->lastInsertId ($name);
    }
    
    public function fetch($statement = null, $options = null) {
        (is_null($statement)) and ($statement = $this->latest_statement);
        if(empty($statement)) {
            return array();
        }
        
        $result = $statement->fetch();
        return empty($result) ? array() : $result;
    }
    
    public function fetchAll($statement = null, $options = null) {
        (is_null($statement)) and ($statement = $this->latest_statement);
        if(empty($statement)) {
            return array();
        }
        
        $result = $statement->fetchAll();
        return empty($result) ? array() : $result;
    }
}

?>
