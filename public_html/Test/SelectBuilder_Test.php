<?php

require_once '../initialize_test.php';

use Yamato\Core\Query\SelectBuilder as SelectBuilder;


function normal_test() {
    $select = new SelectBuilder();

    $subquery = new SelectBuilder();

    $select->select('id,name')
            ->select('telephone')
            ->from('m_user')
                ->leftJoin('ljtable ljt1')
                    ->on('m_user.id = ljt1.user_id')
                ->innerJoin('ijtable ijt22', 'm_user.id = ijt22.user_id')
                ->where('name = :name', 'hiraishi')
                ->where('id = :id AND telephone = :tele', [':id' => 999, ':tele' => '0120333444'])
                ->where('del_flg != 1')
                ->groupBy('name')
                ->orderBy('name desc')
                ->orderBy('id asc');

    var_dump($select->build());
}

subquery_test();

function subquery_test() {
    $subquery = new SelectBuilder();
    
    $subquery->select('count(id) as count')
                ->from('m_property')
                ->where('user_id = :user_id',[':user_id' => 789]);
    
    $select = new SelectBuilder();
    
    $select->select('id, name')
                ->select($subquery)
                ->from('m_user')
                    ->where('name = :name', 'hiraishi')
                    ->where('id = :id AND telephone = :tele', [':id' => 999, ':tele' => '0120333444']);
    
    var_dump($select->build());
    var_dump($select->getBindValues());
}

?>
