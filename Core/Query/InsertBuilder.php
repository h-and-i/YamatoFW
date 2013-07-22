<?php

namespace Yamato\Core\Query;

class InsertBuilder {
    protected $fields = null;
    protected $values = array();
    protected $into = null;
    
    public function __construct($db = null) {
        if($db) {
            $this->db = $db;
        }
    }
    
    public function into($into) {
        $this->into = $into;
        return $this;
    }
    
    public function field($field) {
        if(is_array($field)) {
            foreach($field as $f) {
                $this->field($f);
            }
        } else {
            $this->fields[] = $field;
        }
        return $this;
    }
    
    public function value(array $values) {
        $this->values[] = $values;
        return $this;
    }
    
    protected function getFieldQuery() {
        $query = '';
        $query .= implode(', ', $this->fields);
        return $query;
    }
    
    protected function getPlaceHolder () {
        $place_holder = array();
        foreach($this->values as $v) {
            $place_holder[] = '(' . implode(', ', array_fill(0, count($v), '?')) . ')';
        }
        return implode(', ', $place_holder);
    }
    
    public function getBindValues() {
        $bind_values = array();
        foreach($this->values as $v) {
            $bind_values = array_merge($bind_values, $v);
        }
        return $bind_values;
    }
    
    public function build() {
        $sql = 'INSERT '
                . ' INTO '
                . $this->into
                . ' ('
                    . $this->getFieldQuery()
                . ') '
                . ' VALUES '
                    . $this->getPlaceHolder()
                ;
        return $sql;
    }
    
    // 入れ子の場合なので、括弧で閉じる
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
