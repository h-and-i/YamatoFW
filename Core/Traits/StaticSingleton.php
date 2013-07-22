<?php

namespace Yamato\Core\Traits;

/*
 * StaticSingletonトレイト
 * 
 */
trait StaticSingleton {
    
    protected static function getInstance() {
        static $obj = null;
        if($obj) {
            return $obj;
        }
        
        $obj = new static;
        $obj->initialize();
        return $obj;
    }
    
    protected function initialize() { }
    
    function __clone() {
        $this_obj = static::getInstance();
        throw new YamatoRuntimeException(get_class($this_obj) . ' can\'t use __clone().');
    }
}

?>
