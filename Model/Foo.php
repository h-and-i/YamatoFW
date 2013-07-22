<?php

namespace Yamato\Model;
use Yamato\Core\Model\DataModel as DataModel;

class Foo extends DataModel {
    protected static $columns = ['id', 'name', 'create_date','del_flg'];
    protected static $table_name = 'm_user';
    protected static $name = 'Foo';
    protected static $keys = ['id']; 
    
    protected static $has_one = [
                                    'UserStatus'
                                        => ['my_key' => 'id',
                                            'foreign_key' => 'user_id']
                                ];
    
    protected static $has_many = [
                                    'Post'
                                        => ['my_key' => 'id',
                                            'foreign_key' => 'user_id']
                                 ];
}

?>
