<?php

namespace Yamato\Model;
use Yamato\Core\Model\DataModel as DataModel;

class Post extends DataModel {
    protected static $columns = ['id', 'user_id', 'contents', 'create_date', 'del_flg'];
    protected static $table_name = 'post';
    protected static $name = 'Post';
    protected static $keys = ['id'];
}

?>
