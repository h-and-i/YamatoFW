<?php

namespace Yamato\Core\Query;

class UpdateBuilder {
    protected $fields = null;
    protected $values = array();
    protected $table = null;
    protected $where = null;
    
    public function __construct($db = null) {
        if($db) {
            $this->db = $db;
        }
        $this->where = new WhereBuilder();
    }
    
    public function update($table) {
        $this->table = $table;
        return $this;
    }
    
    public function set($field, $value) {
        $this->values[$field] = $value;
        return $this;
    }
    
    // param array $value_set
    // array(':name' => 'Mark',
    //       ':age' => 21) のような配列
    public function setArray(array $value_set = array()) {
        $this->values = $this->values + $value_set;
        return $this;
    }
    public function value(array $values) {
        $this->values[] = $values;
        return $this;
    }
    
    protected function getFieldQuery() {
        if(!count($this->values)) {
            // @todo@ 例外送出
            return '';
        }
        $set_statement_list = array();
        $field_list = array_keys($this->values);
        foreach($field_list as $field) {
            $set_statement_list[] =  "{$field} = " . $this->getBindKey($field);
        }
        return implode(', ', $set_statement_list);
    }
    
    public function where($statement, $bind_set = array()) {
        $this->whereAnd($statement, $bind_set);
        return $this;
    }
    
    public function whereAnd($statement, $bind_set = array()) {
        $this->where->addAnd($statement, $bind_set);
        return $this;
    }
    
    public function whereOr($statement, $bind_set = array()) {
        $this->where->addOr($statement, $bind_set);
        return $this;
    }
    
    public function getBindValues() {
        $update_values = array_combine(
                                        array_map(
                                                function($k){ return $this->getBindKey($k); },
                                                array_keys($this->values)
                                                ),
                                        array_values($this->values)
                                      );
                                                
        $where_values = $this->where->getBindValues();
        return $update_values + $where_values;
    }
    
    protected function getBindKey($field) {
        return ':' . $field;
    }
    
    
    public function build() {
        $sql = ' UPDATE '
                . $this->table
                . ' SET '
                    . $this->getFieldQuery()
                ;
        ($this->where->build()) and ($sql .= ' WHERE ' . $this->where->build());
        return $sql;
    }
    
    public function __toString() {
        return $this->build();
    }
    
    public function execute() {
        if(!$db) {
            throw new \RuntimeException('database connect is not set');
        }
        return $this;
    }
}

?>
