<?php
namespace Yamato\Core\Model;

class DataModel {
    protected static $columns = array();
    protected static $table_name = null;
    protected static $name = null;
    protected static $keys = array();
    protected static $has_one = array();
    protected static $has_many = array();
    
    public static function getColumns() {
        if(empty(static::$columns)) {
            throw new \RuntimeException('columns must be overrided.');
        }
        return static::$columns;
    }
    
    public static function getTableName() {
        if(empty(static::$table_name)) {
            throw new \RuntimeException('table_name must be overrided.');
        }
        return static::$table_name;
    }
    
    public static function getName() {
        return static::$name;
    }
    
    public static function getKeys() {
        return static::$keys;
    }
    
    public static function getRelations() {
        $relations = [
            Relation::HAS_ONE => static::$has_one,
            Relation::HAS_MANY => static::$has_many
        ];
        return $relations;
    }
    
    public static function getHasOne() {
        return static::$has_one;
    }
    
    public static function getHasMany() {
        return static::$has_many;
    }
    
    public function setArrayValues(array $values) {
        foreach($values as $key => $value) {
            if(in_array($key, static::$columns)) {
                $this->__set($key, $value);
            }
        }
    }
    
    public function __set($name, $value) {
        $this->isMemberValue($name);
        $this->$name = $value;
    }
    
    public function __get($name) {
        $this->isMemberValue($name);
        return $this->$name;
    }
    
    protected function isMemberValue($name) {
        $is_column_member = in_array($name, static::$columns);
        $is_has_one_member = in_array($name, array_keys(static::$has_one));
        $is_has_many_member = in_array($name, array_keys(static::$has_many));
        
        $is_member = $is_column_member || $is_has_one_member || $is_has_many_member;
        
        if(!$is_member) {
            throw new \InvalidArgumentException($name . ' isn\'t this class member.');
        }
    }
    
    public function getActiveColumns() {
        $ret = array();
        foreach(static::$columns as $field) {
            if(isset($this->$field)) {
                $ret[$field] = $this->$field;
            }
        }
        return $ret;
    }
}

?>
