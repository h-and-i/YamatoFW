<?php

namespace Yamato\Core\Lib;

class Arr {
    public static function get($array, $key, $default = null) {
        if(array_key_exists($key, $array)) {
            return $array[$key];
        }
        return $default;
    }
}

?>
