<?php

namespace Yamato\Model;
use Yamato\Core\Model\DataModel as DataModel;

class UserStatus extends DataModel {
    protected static $columns = ['id', 'user_id', 'name', 'create_date'];
    protected static $table_name = 'user_status';
    protected static $name = 'UserStatus';
    protected static $keys = ['id']; 
    
}

?>
