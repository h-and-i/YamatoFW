<?php

namespace Yamato\Core;

/**
 * Description of Cache
 *
 * @author hiraishi
 */
interface Cache {
    public function store($store_key, $data, $options = array());
    public function load($store_key, $options = array());
    public function reset($store_key, $options = array());
}

?>
