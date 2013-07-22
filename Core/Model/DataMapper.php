<?php
namespace Yamato\Core\Model;

use Yamato\Core\Query\WhereBuilder as WhereBuilder;
use Yamato\Core\Query\SelectBuilder as SelectBuilder;
use Yamato\Core\Query\UpdateBuilder as UpdateBuilder;
use Yamato\Core\Query\InsertBuilder as InsertBuilder;
use Yamato\Core\Database\Db as Db;
use Yamato\Core\Model\DataModel as DataModel;

class DataMapper {
    protected static $model_name = null;
    
    public function getModelName() {
        if(empty(static::$model_name)) {
            throw new \RuntimeException('$model_name must be overrided,');
        }
        return static::$model_name;
    }
    
    public function getFullModelName() {
        $model_name = $this->getModelName();
        return MODEL_NAMESPACE . $model_name;
    }
    
    protected function getTableName() {
        $model = $this->getFullModelName();
        return call_user_func($model . '::getTableName');
    }
    
    protected function getHasOne() {
        $model = $this->getFullModelName();
        return call_user_func($model . '::getHasOne');
    }
    
    protected function getHasMany() {
        $model = $this->getFullModelName();
        return call_user_func($model . '::getHasMany');
    }
    
    protected function getModelRelations() {
        $model = $this->getFullModelName();
        return call_user_func($model . '::getRelations');
    }
    
    protected function getRelation() {
        $relation = new Relation($this->getModelName(), $this->getModelRelations());
        return $relation;
    }
    
    public function insert(DataModel $model) {
        $columns_set = $model->getActiveColumns();
        
        $field_list = array_keys($columns_set);
        $value_list = array_values($columns_set);
        
        $this->insertRaw($field_list, [$value_list]);
    }
    
    public function insertRaw($field_list, $value_set) {
        $table = $this->getTableName();
        
        $builder = new InsertBuilder();
        $builder->into($table)
                    ->field($field_list);
        foreach($value_set as $value_list) {
            $builder->value($value_list);
        }
        Db::getInstance()->prepare($builder->build());
        Db::getInstance()->execute($builder->getBindValues());
    }
    
    public function update(DataModel $model) {
        $columns_set = $model->getActiveColumns();
        
        $primary_keys = $model->getKeys();
        
        $where = array();
        foreach($primary_keys as $key) {
            $where["{$key} = :{$key}"] = [":{$key}" => $model->$key];
        }
        
        $this->updateRaw($columns_set, $where);
    }
    
    public function updateRaw($field_list, array $where = []) {
        $table = $this->getTableName();
        $builder = new UpdateBuilder();
        $builder->update($table);
        
        $builder->setArray($field_list);
        
        foreach($where as $statement => $bind_set) {
            $builder->where($statement, $bind_set);
        }
        
        Db::getInstance()->prepare($builder->build());
        Db::getInstance()->execute($builder->getBindValues());
    }
    
    public function findRawOne($conditions = array()) {
        $conditions['mode'] = 'one';
        return $this->findRaw($conditions);
    }
    
    public function findRawAll($conditions = array()) {
        $conditions['mode'] = 'all';
        return $this->findRaw($conditions);
    }
    
    protected function findRaw($conditions = array()) {
        if($this->hasRelation()) {
            return $this->findRawWithRelation($conditions);
        }
        
        $mode = \Arr::get($conditions, 'mode', 'all');
        $filter = \Arr::get($conditions, 'filter');
        $limit = \Arr::get($conditions, 'limit');
        $order_by = \Arr::get($conditions, 'order_by');
        $where = \Arr::get($conditions, 'where');
        
        $table = $this->getTableName();
        $columns = call_user_func($this->getFullModelName() . '::getColumns');
        
        $select = new SelectBuilder();
        $select->from($table)
                    ->select(implode(', ', $columns))
                    ->orderBy($order_by)
                    ->limit($limit);
                                                
        $select = $this->addFindFilter($select, $filter);
        $select = $this->addFindWhere($select, $where);
        
        Db::getInstance()->prepare($select->build());
        Db::getInstance()->execute($select->getBindValues());
        
        $result = $this->fetch($mode);
        return $result;
    }
    
    protected function findRawWithRelation($conditions) {
        $relation = $this->getRelation();
        
        $mode = \Arr::get($conditions, 'mode', 'all');
        $filter = \Arr::get($conditions, 'filter');
        $limit = \Arr::get($conditions, 'limit');
        $order_by = \Arr::get($conditions, 'order_by');
        $where = \Arr::get($conditions, 'where');
        
        $table = $relation->getAliasParentModelTable();
        $columns = $relation->getAliasParentTableColumns();
        $order_by = $relation->getAliasParentTableOrderBy($order_by);
        
        $select = new SelectBuilder();
        $select->from($table)
                    ->select(implode(', ', $columns))
                    ->orderBy($order_by)
                    ->limit($limit);
        $select = $relation->addRelationColumns($select);
        $select = $relation->leftJoinRelationTable($select);
        
        $select = $relation->addFindFilter($select, $filter);
        $select = $relation->addFindWhere($select, $where);
        
        Db::getInstance()->prepare($select->build());
        Db::getInstance()->execute($select->getBindValues());
        
        $result = $relation->fetch($mode);
        return $result;
    }
    
    protected function fetch($mode = 'all') {
        $result = null;
        if($mode == 'all') {
            $result = Db::getInstance()->fetchAll();
        } else {
            $result = Db::getInstance()->fetch();
        }
        return $result;
    }
    
    public function findOne($conditions) {
        $conditions['mode'] = 'one';
        return $this->find($conditions);
    }
    
    public function findAll($conditions) {
        $conditions['mode'] = 'all';
        return $this->find($conditions);
    }
    
    protected function find($conditions) {
        $mode = \Arr::get($conditions, 'mode', 'all');
        
        $model_name = $this->getFullModelName();
        $raw_list = $this->findRaw($conditions);
        
        if($this->hasRelation()) {
            return $this->findWithRelation($conditions, $raw_list);
        }
        
        if($mode != 'all') {
            $model = new $model_name();
            $model->setArrayValues($raw_list);
            return $model;
        }
        
        $model_list = array();
        foreach($raw_list as $raw) {
            $model = new $model_name();
            $model->setArrayValues($raw);
            $model_list[] = $model;
        }
        return $model_list;
    }
    
    protected function findWithRelation($conditions, $raw_list) {
        $relation = $this->getRelation();
        $mode = \Arr::get($conditions, 'mode', 'all');
        
        return $relation->getModel($raw_list, $mode);
    }
    
    public function hasRelation() {
        $has_one = $this->getHasOne();
        $has_many = $this->getHasMany();
        if(!empty($has_one) || !empty($has_many)) {
            return true;
        }
        return false;
    }
    
    protected function addFindFilter(SelectBuilder $select, $filter_condition) {
        if(empty($filter_condition)) {
            return $select;
        }
        
        foreach($filter_condition as $key => $val) {
            $bind_key = ":$key";
            $select->where($key.' = '. $bind_key, array($bind_key => $val));
        }
        
        // del_flgは必ず見る
        $select->where('del_flg != 1');
        return $select;
    }
    
    protected function addFindWhere(SelectBuilder $select, $where) {
        if(empty($where)) {
            return $select;
        }
        
        list($where_query, $bind_values) = $where;
        $select->where($where_query, $bind_values);
        return $select;
    }
    
}

?>
