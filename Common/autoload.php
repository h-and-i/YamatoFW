<?php

require_once CORE_PATH . 'AutoLoader.php';
use Yamato\Core\AutoLoader as AutoLoader;

AutoLoader::initialize();
spl_autoload_register('Yamato\Core\AutoLoader::entity');
