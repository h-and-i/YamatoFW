<?php
namespace Yamato\Core\Model;

use Yamato\Core\Query\SelectBuilder as SelectBuilder;
use Yamato\Core\Database\Db as Db;

class Relation {
    const HAS_ONE = 'has_one';
    const HAS_MANY = 'has_many';
    
    protected $relations = array();
    protected $has_one = array();
    protected $has_many = array();
    protected $parent_model = null;
    
    public function __construct($parent_model, $relations) {
        $this->parent_model = $parent_model;
        (!empty($relations[static::HAS_ONE])) and ($this->has_one = $relations[static::HAS_ONE]);
        (!empty($relations[static::HAS_MANY])) and ($this->has_many = $relations[static::HAS_MANY]);
        
        $this->setRelationInfo();
    }
    
    public function getAliasParentModelTable() {
        return $this->getAliasModelTable($this->parent_model);
    }
    
    protected function getAliasModelTable($model) {
        $full_model = MODEL_NAMESPACE . $model;
        $alias = $this->relations[$model]['alias'];
        $table = call_user_func($full_model . '::getTableName');
        return $table. ' ' . $alias;
    }
    
    public function getAliasParentTableColumns() {
        return $this->getAliasTableColumns($this->parent_model);
    }
    
    protected function getTableColumns($model) {
        $full_model = MODEL_NAMESPACE. $model;
        $columns = call_user_func($full_model . '::getColumns');
        return $columns;
    }
    
    protected function getAliasTableColumns($model) {
        $columns = $this->getTableColumns($model);
        $alias = $this->relations[$model]['alias'];
        return array_map(function($c) use($alias) { return "$alias.{$c} as {$alias}_{$c}"; } , $columns);
    }
    
    public function getAliasParentTableOrderBy($order_by) {
        $order_by = preg_replace('/(?=\W?)(' . $this->getReplaceParentRegex() . ')(?=\W?)/', 'T0.\1', $order_by);
        return $order_by;
    }
    
    protected function getReplaceParentRegex() {
        $parent_model = MODEL_NAMESPACE . $this->parent_model;
        $columns = call_user_func($parent_model . '::getColumns');
        $columns_regex = implode('|', $columns);
        return $columns_regex;
    }
            
    protected function setRelationInfo() {
        $index = 1;
        if(count($this->has_one)) {
            foreach($this->has_one as $model => $config) {
                $config['alias'] = 'T' . (string)$index;
                $config['mode'] = static::HAS_ONE;
                $this->relations[$model] = $config;
                ++$index;
            }
        }
        
        if(count($this->has_many)){
            foreach($this->has_many as $model => $config) {
                $config['alias'] = 'T' . (string)$index;
                $config['mode'] = static::HAS_MANY;
                $this->relations[$model] = $config;
                ++$index;
            }
        }
        $this->relations[$this->parent_model] = ['alias' => 'T0'
                                                ,'mode' => null];
    }
    
    protected function getHasOneRelationInfo() {
        return array_filter($this->relations, function($c){ return ($c['mode'] == static::HAS_ONE); });
    }
    
    protected function getHasManyRelationInfo() {
        return array_filter($this->relations, function($c){ return ($c['mode'] == static::HAS_MANY); });
    }
    
    
    public function leftJoinRelationTable(SelectBuilder $select) {
        $relations = $this->relations;
        unset($relations[$this->parent_model]);
        foreach($relations as $model => $relation) {
            $select->leftJoin($this->getAliasModelTable($model))
                    ->on($this->getOnQuery($model));
        }
        return $select;
    }
    
    protected function getOnQuery($model){
        $parent_alias = $this->relations[$this->parent_model]['alias'];
        $alias = $this->relations[$model]['alias'];
        
        $config = $this->relations[$model];
        
        return "{$parent_alias}." . $config['my_key'] . ' = ' . "{$alias}." . $config['foreign_key'];
    }
    
    public function addFindFilter(SelectBuilder $select, $filter_condition) {
        if(empty($filter_condition)) {
            return $select;
        }
        
        $parent_alias = $this->relations[$this->parent_model]['alias'];
        foreach($filter_condition as $key => $val) {
            $bind_key = ":$key";
            $select->where("{$parent_alias}.$key" . ' = '. $bind_key, array($bind_key => $val));
        }
        
        // del_flgは必ず見る
        $select->where("{$parent_alias}.del_flg != 1");
        return $select;
    }
    
    public function addFindWhere(SelectBuilder $select, $where) {
        if(empty($where)) {
            return $select;
        }
        
        list($where_query, $bind_values) = $where;
        $where_query = $this->getAliasParentTableWhere($where_query);
        $select->where($where_query, $bind_values);
        return $select;
    }
    
    protected function getAliasParentTableWhere($where_query) {
        $pattern = '/(?=\W?)(?<!:)(' . $this->getReplaceParentRegex() . ')(?=\W?)/';
        $where_query = preg_replace($pattern, 'T0.\1', $where_query);
        return $where_query;
    }
    
    public function addRelationColumns(SelectBuilder $select) {
        foreach($this->relations as $model => $config) {
            $columns = $this->getAliasTableColumns($model);
            $select->select(implode(', ', $columns));
        }
        return $select;
    }
    
    public function getModel($raw_data, $mode) {
        $ret = null;
        if($mode == 'all') {
            $ret =  $this->getModelAll($raw_data);
        } else {
            $ret = $this->getModelOne($raw_data);
        }
        return $ret;
    }
    
    public function getModelOne($raw_data) {
        $parent_model = MODEL_NAMESPACE . $this->parent_model;
        $parent = new $parent_model();
        
        $parent->setArrayValues($raw_data);
        
        // relation
        foreach($this->has_one as $model => $config) {
            if(isset($raw_data[$model])) {
                $model_fullname = MODEL_NAMESPACE . $model;
                $parent->$model = new $model_fullname();
                $parent->$model->setArrayValues($raw_data[$model]);
            }
        }
        
        foreach($this->has_many as $model => $config) {
            if(isset($raw_data[$model])) {
                $model_fullname = MODEL_NAMESPACE .$model;
                foreach($raw_data[$model] as $value_list) {
                    (!isset($parent->$model)) and ($parent->$model = array());
                    $tmp = new $model_fullname();
                    $tmp->setArrayValues($value_list);
                    array_push($parent->$model, $tmp);
                }
            }
        }
        
        return $parent;
    }
    
    public function getModelAll($raw_data_list) {
        $result = array();
        foreach($raw_data_list as $raw_data) {
            $result[] = $this->getModelOne($raw_data);
        }
        return $result;
    }
    
    protected function getDataSetByModel($raw_data, $model) {
        $alias = $this->relations[$model]['alias'];
        
        $model = MODEL_NAMESPACE . $model;
        $columns = call_user_func($model . '::getColumns');
        
        $ret = new $model();
        foreach($columns as $column) {
            if(isset($raw_data[$alias.'_'.$column])) {
                $ret->$column = $raw_data[$alias.'_'.$column];
            }
        }
        return $ret;
    }
    
    public function fetch($mode) {
        $result = Db::getInstance()->fetchAll();
        if(empty($result)) {
            return $result;
        }
        
        if($mode != 'all') {
            $result =  $this->fetchModeOne($result);
        } else {
            $result =  $this->fetchModeAll($result);
        }
        return $result;
    }
    
    protected function getParentKeyValues($raw, $alias) {
        $parent_model = MODEL_NAMESPACE . $this->parent_model;
        $keys = call_user_func($parent_model . '::getKeys');
        
        $parent_key_values = array();
        foreach($keys as $key) {
            $parent_key_values[$alias.'_'.$key] = $raw[$alias.'_'.$key];
        }
        
        return $parent_key_values;
    }
    
    protected function fetchModeOne($raw_list) {
        $result = array();
        // parent
        $parent_alias = $this->relations[$this->parent_model]['alias'];
        $columns = $this->getTableColumns($this->parent_model);
        
        $tmp = array();
        $this->compareParentKeyValues(null);// 初期化
        
        $has_one = $this->getHasOneRelationInfo();
        $has_many = $this->getHasManyRelationInfo();
        
        foreach($raw_list as $raw) {
            
            if(!$this->compareParentKeyValues($this->getParentKeyValues($raw, $parent_alias))) {
                break;
            }
            
            foreach($columns as $column) {
                if(isset($raw[$parent_alias.'_'.$column])) {
                    $tmp[$column] = $raw[$parent_alias.'_'.$column];
                }
            }
            
            if(count($has_one)) {
                foreach($has_one as $model => $relation) {
                    $alias = $relation['alias'];
                    $columns = $this->getTableColumns($model);
                    
                    $tmp[$model] = [];
                    
                    foreach($columns as $column) {
                        if(isset($raw[$alias.'_'.$column])) {
                            $tmp[$model][$column] = $raw[$alias.'_'.$column];
                        }
                    }
                }
            }
            
            if(empty($has_many)) {
                return $tmp;
            }
            
            foreach($has_many as $model => $relation) {
                (!isset($tmp[$model])) and ($tmp[$model] = array());
                $has_many_tmp = array();
                $alias = $relation['alias'];
                $columns = $this->getTableColumns($model);
                
                foreach($columns as $column) {
                    if(isset($raw[$alias.'_'.$column])) {
                        $has_many_tmp[$column] = $raw[$alias.'_'.$column];
                    }
                }
                if(!empty($has_many_tmp)) {
                    $tmp[$model][] = $has_many_tmp;
                }
            }
        }
        
        return $tmp;
    }
    
    protected function getParentValues($parent_alias, $parent_columns, $row) {
        $parent_data = array();
        foreach($parent_columns as $column) {
            if(isset($raw[$parent_alias.'_'.$column])) {
                $parent_data[$column] = $raw[$parent_alias.'_'.$column];
            }
        }
        return $parent_data;
    }
    
    protected function compareParentKeyValues($parent_key_values) {
        static $compare_values = null;
        
        if(is_null($parent_key_values)) {
            $compare_values = null;
            return;
        }
        
        if(empty($compare_values)) {
            $compare_values = $parent_key_values;
            return true;
        }
        
        if($compare_values == $parent_key_values) {
            return true;
        } else {
            $compare_values = null;
            return false;
        } 
    }
    
    protected function fetchModeAll($raw_list) {
        $result = array();
        
        $parent_alias = $this->relations[$this->parent_model]['alias'];
        
        $parent_process_unit = array();
        $this->compareParentKeyValues(null);// 初期化

        foreach($raw_list as $raw) {
            $parent_key_values = $this->getParentKeyValues($raw, $parent_alias);
            if($this->compareParentKeyValues($parent_key_values)) {
                $parent_process_unit[] = $raw;
            } else {
                $result[] = $this->fetchModeOne($parent_process_unit);
                
                // リセット
                $parent_process_unit = array();
                $parent_process_unit[] = $raw;
            }
        }
        
        $result[] = $this->fetchModeOne($parent_process_unit);
        return $result;
    }
    
}

?>
