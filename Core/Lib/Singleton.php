<?php

namespace Yamato\Core\Lib;

/*
 * Singletonトレイト
 * 
 */
trait Singleton {
    protected function __construct() { }
    
    static function getInstance() {
        static $obj = null;
        
        if(is_null($obj)) {
            $obj = new static;
            $obj->initialize();
        }
        return $obj;
    }
    
    protected function initialize() { }
    
    function __clone() {
        $this_obj = static::getInstance();
        throw new YamatoRuntimeException(get_class($this_obj) . ' can\'t use __clone().');
    }
}

?>
