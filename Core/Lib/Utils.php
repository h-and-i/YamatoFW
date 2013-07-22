<?php

namespace Yamato\Core\Lib;

/*
 * ユーティリティ関数群
 */
class Utils {
    public static function requireOnce($file_path) {
        if(!file_exists($file_path)) {
            throw new \Yamato\Core\Exception\File404Exception($file_path . ' is not exist.');
        }
        require_once $file_path;
        return true;
    }
}

?>