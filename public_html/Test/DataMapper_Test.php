<?php

require_once '../initialize_test.php';

use Yamato\Model\Foo as Foo;
use Yamato\Model\FooDataMapper as FooDataMapper;

function test1() {
    $data_maper = new FooDataMapper();

    $conditions = [
                    'filter' => ['name' => 'heyheyhey',
                                 'del_flg' => '0'],
                    'order_by' => 'id desc',
                  ];
    $result = $data_maper->findRawOne($conditions);
    var_dump($result);
}

function test2() {
    $data_maper = new FooDataMapper();
    
    $foo = new Foo();
    $foo->id = 1;
    $foo->name = 'update_name';

    $data_maper->update($foo);
}

function test3() {
    $data_mapper = new FooDataMapper();
    
    $foo = new Foo();
    $foo->name = 'heyheyhey';
    $foo->create_date = '2013/03/15 00:00:00';
    $data_mapper->insert($foo);
}

function test4() {
    $data_mapper = new FooDataMapper();
    
    $conditions = ['filter' => ['del_flg' => 0],
                   'order_by' => 'id desc',
                   'limit' => '0, 2'];
    $foo_list = $data_mapper->findRawAll($conditions);
    var_dump($foo_list);
}

function test5() {
    $data_mapper = new FooDataMapper();
    
    $conditions = ['filter' => ['del_flg' => 0],
                   'order_by' => 'name, id desc, create_date asc',
                   'limit' => '0, 2'];
    $foo_list = $data_mapper->findOne($conditions);
    var_dump($foo_list);
}

function test6() {
    $data_mapper = new FooDataMapper();
    
    $conditions = [
                    'where' => [
                                'del_flg != :del_flg OR name = :name',
                                [':del_flg' => 1, ':name' => 'heyheyhey']
                               ],
                   'order_by' => 'id desc',
                  ];
    
    $foo_list = $data_mapper->findRawOne($conditions);
    var_dump($foo_list);
}

function test7() {
    $data_mapper = new FooDataMapper();
    
    $conditions = [
                    'where' => [
                                'del_flg != :del_flg AND name = :name',
                                [':del_flg' => 1, ':name' => 'heyheyhey']
                               ],
                   'order_by' => 'id desc',
                  ];
    
    $foo_list = $data_mapper->findRawAll($conditions);
    var_dump($foo_list);
}

function test8() {
    $data_mapper = new FooDataMapper();
    
    $conditions = [
                    'where' => [
                                'del_flg != :del_flg AND name = :name',
                                [':del_flg' => 1, ':name' => 'heyheyhey']
                               ],
                   'order_by' => 'id desc',
                  ];
    
    $foo_list = $data_mapper->findOne($conditions);
    var_dump($foo_list);
}

function test9() {
    $data_mapper = new FooDataMapper();
    
    $conditions = [
                    'where' => [
                                'del_flg != :del_flg AND name = :name',
                                [':del_flg' => 1, ':name' => 'heyheyhey']
                               ],
                   'order_by' => 'id desc',
                  ];
    
    $foo_list = $data_mapper->findAll($conditions);
    var_dump($foo_list);
}

test9();

?>
