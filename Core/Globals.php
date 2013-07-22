<?php

namespace Yamato\Core;

/**
 * Description of Globals
 *
 * @author hiraishi
 */
class Globals {
    public static function server($value = null) {
        if(is_null($value)) {
            return $_SERVER;
        }
        return \Arr::get($_SERVER, $value);
    }
}

?>
