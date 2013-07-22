<?php

require_once '../initialize_test.php';

use Yamato\Core\Query\InsertBuilder as InsertBuilder;

$insert = new InsertBuilder();

$sql = $insert->into('m_user')
            ->field('id')
            ->field('name2')
            ->value(['1', 'Mark'])
            ->value(['2', 'Nancy'])
            ->build();

var_dump($sql);

var_dump($insert->getBindValues());


?>
