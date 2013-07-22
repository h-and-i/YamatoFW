<?php

namespace Yamato\Core\Query;

class SelectBuilder {
    const LATEST_JOIN_IS_LEFT = 'left';
    const LATEST_JOIN_IS_INNER = 'inner';

    protected $db = null;
    
    protected $select = null;
    protected $from = null;
    protected $left_join = array();
    protected $inner_join = array();
    protected $where = null;
    protected $group_by = null;
    protected $order_by = null;
    protected $limit = null;
    protected $latest_join = null;
    
    public function __construct($db = null) {
        if($db) {
            $this->db = $db;
        }
        $this->where = new WhereBuilder();
    }
    
    public function setDb($db) {
        $this->db = $db;
    }
    
    public function select($query) {
        if(!empty($query)) {
            $this->select[] = (string)$query;
            
            if($query instanceof SelectBuilder) {
                $bind_values = $this->where->getBindValues()
                                    + $query->where->getBindValues();
                $this->where->setBindValues($bind_values);
            }
        }
        return $this;
    }
    
    public function from($from) {
        $this->from = $from;
        return $this;
    }
    
    public function groupBy($query) {
        $this->group_by = $query;
        return $this;
    }
    
    public function orderBy($query) {
        if(is_string($this->order_by)) {
            $this->order_by .= ', ';
        }
        $this->order_by .= $query;
        return $this;
    }
    
    public function limit($query) {
        $this->limit = $query;
        return $this;
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
    
    public function leftJoin($table, $on_query = null) {
        list($raw_table_query, $table, $alias) = $this->analyzeJoinTable($table);
        $this->left_join[] = $this->makeJoinTableInfo($raw_table_query, $table, $alias, $on_query);
        $this->latest_join = static::LATEST_JOIN_IS_LEFT;
        return $this;
    }
    
    public function innerJoin($table, $on_query = null) {
        list($table, $alias) = $this->analyzeJoinTable($table);
        $this->inner_join[] = $this->makeJoinTableInfo($table, $table, $alias, $on_query);
        $this->latest_join = static::LATEST_JOIN_IS_INNER;
        return $this;
    }
    
    public function on($on_query) {
        if($this->latest_join == static::LATEST_JOIN_IS_LEFT) {
            $latest_table = array_pop($this->left_join);
            $latest_table['on'] = $on_query;
            array_push($this->left_join, $latest_table);
        } elseif($this->latest_join == static::LATEST_JOIN_IS_INNER) {
            $latest_table = array_pop($this->inner_join);
            $latest_table['on'] = $on_query;
            array_push($this->inner_join, $latest_table);
        }
        return $this;
    }
    
    protected function analyzeJoinTable($table) {
        // alias
        if(preg_match('/(?<table>\w+)\s*(?<alias>\w+)/', $table, $matches)) {
            return [$table,
                    $matches['table'],
                    $matches['alias']];
        }
        // normal
        return [$table, $table, null];
    }
    
    protected function makeJoinTableInfo($raw_table_query, $table, $alias = null, $on = null) {
        $info = ['raw' => $raw_table_query,
                 'table' => $table,
                 'alias' => $alias,
                 'on' => $on];
        return $info;
    }
    
    public function build() {
        $sql = 'SELECT '
                . implode(', ', $this->select)
                . ' FROM '
                . (string)$this->from;
        
        if(count($this->left_join)) {
            foreach($this->left_join as $join_table) {
                $sql .= ' LEFT JOIN ' . $join_table['raw']
                            .' ON ' . $join_table['on'];
            }
        }
        
        if(count($this->inner_join)) {
            foreach($this->inner_join as $join_table) {
                $sql .= ' INNER JOIN ' . $join_table['raw']
                            .' ON ' . $join_table['on'];
            }
        }
        
        ($this->where->build()) and ($sql .= ' WHERE ' . $this->where->build());
        ($this->group_by) and ($sql .= ' GROUP BY ' . $this->group_by);
        ($this->order_by) and ($sql .= ' ORDER BY ' . $this->order_by);
        ($this->limit) and ($sql .= ' LIMIT ' . $this->limit);
        
        return $sql;
    }
    
    // 入れ子の場合なので、括弧で閉じる
    public function __toString() {
        $sql = ' ( ' . $this->build() . ' ) ';
        return $sql;
    }
    
    public function execute() {
        if(!$db) {
            throw new \RuntimeException('database connect is not set');
        }
    }
    
    public function getBindValues() {
        return $this->where->getBindValues();
    }
}

?>
