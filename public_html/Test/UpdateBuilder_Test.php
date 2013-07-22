<?php

require_once '../initialize_test.php';

use Yamato\Core\Query\UpdateBuilder as UpdateBuilder;

Yamato\Core\Database\Db::getInstance()->connect();

$update = new UpdateBuilder();

$update->update('m_user')
            ->set('create_date', '2013-03-10 00:00:00')
            ->setArray(['name' => 'Kevin'])
            ->set('update_date', '2013-03-11 00:00:00')
            ->set('del_flg', 1)
            ->where('id = :id', '1')
            ;

var_dump($update->build());
var_dump($update->getBindValues());

Yamato\Core\Database\Db::getInstance()->prepare($update->build());
Yamato\Core\Database\Db::getInstance()->execute($update->getBindValues());


?>
