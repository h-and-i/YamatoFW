<?php

require_once '../initialize_test.php';

use Yamato\Core\Config as Config;

$time_start = microtime(true);

$config = Config::get('yamato.yml');

$time_end = microtime(true);
$time = $time_end - $time_start;
var_dump($time);
var_dump($config);

?>
