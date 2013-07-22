<?php

namespace Yamato\Core\Database;

class Db {
    use \Yamato\Core\Lib\Singleton;
    
    // DBドライバー（基本はPDO)
    protected $driver = null;
    
    protected function initialize() {
        $this->connect();
    }
    
    /**
     * DB接続
     */
    public function connect() {
        if(is_object($this->driver)) {
            return $this->driver;
        }
        // @todo@ コンフィグ値で切り替えられるように
        $this->driver = new PdoDriver();
    }
    
    public function query($query) {
        return $this->driver->query($query);
    }
    
    public function prepare($query) {
        return $this->driver->prepare($query);
    }
    
    public function execute($bind_values = null, $statement = null) {
        return $this->driver->execute($bind_values, $statement);
    }
    
    public function fetch($statement = null, $options = null) {
        return $this->driver->fetch($statement, $options);
    }
    
    public function fetchAll($statement = null, $options = null) {
        return $this->driver->fetchAll($statement, $options);
    }
}

?>
