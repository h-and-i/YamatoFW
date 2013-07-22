<?php

require_once '../initialize_test.php';

function one() {
    $where = new \Yamato\Core\Query\WhereBuilder();
    $where->addAnd('hoge = :hoge', array(':hoge' => '1'))
            ->addAnd('hoge2 < :hoge2', array(':hoge2' => '2'))
            ->addOr('or3 = :or3');

    $in_where = new \Yamato\Core\Query\WhereBuilder();
    $in_where->addAnd('in_1 = :in_1')
                ->addAnd('static != 1')
                ->addOr('in_2 = :in_2');

    $where->addAnd($in_where);
    $where->bind(array(':or3' => 'or3',
                       ':in_1' => 'in1',
                       ':in_2' => 'in2',
                       ':hoge' => 'changehoge'));

    var_dump($where->build());
    var_dump($where->getBindValues());
}

function two() {
    $where = new \Yamato\Core\Query\WhereBuilder();
//    $where->addAnd('hoge = :hoge AND hoge2 = :hoge2 OR hoge = :hoge3', [':hoge1' => 'hoge']);
    $where->addAnd('hoge = :hoge1 AND hoge2 = hoge2 OR hoge = hoge3', [':hoge1' => 'hoge'])
            ->addAnd('hoge3 = :hoge3', 5)
            ->addOr('hoge4 = :hoge4 AND hoge5 = :hoge5', ['value4', 'value5'])
//            ->addOr('hoge6 = :hoge6 AND hoge7 = :hoge7', ['value6']) // error teset
            ;
    var_dump($where->getBindValues());
//    var_dump($where->build());
}

two();



?>
