<?php

namespace Yamato\Core\Query;

class WhereBuilder {
    const AND_SECT = 'and';
    const OR_SECT  = 'or';
    
    protected $statement_list = array();
    protected $bind_values = array();
    
    public function getBindValues() {
        return $this->bind_values;
    }
    
    public function setBindValues($bind_values) {
        $this->bind_values = $bind_values;
    }
    
    /**
     * 
     * @param string or WhereBuilder $statement
     * @param type $connect_word
     */
    protected function addStatement($statement, $connect_word) {
        $this->statement_list[]
                = array(
                    'connect' => $connect_word,
                    'body' => $statement
                  );
    }
    
    protected function plusBindSet(array $bind_set) {
        if(empty($bind_set)) { 
            return;
        }
        $this->bind_values = $bind_set + $this->bind_values;
    }
    
    public function addAnd($statement, $bind_set = array()) {
        if(empty($bind_set)) {
            $this->addStatement($statement, self::AND_SECT);
            return $this;
        }
        $this->addStatement($statement, self::AND_SECT);
        
        $bind_keys = $this->analyzeStatement($statement);
        $bind_set = $this->analyzedBindSet($bind_keys, $bind_set);
        
        $this->plusBindSet($bind_set);
        return $this;
    }
    
    public function addOr($statement, $bind_set = array()) {
        if(empty($bind_set)) {
            $this->addStatement($statement, self::OR_SECT);
            return $this;
        }
        $this->addStatement($statement, self::OR_SECT);
        
        $bind_keys = $this->analyzeStatement($statement);
        $bind_set = $this->analyzedBindSet($bind_keys, $bind_set);
        
        $this->plusBindSet($bind_set);
        return $this;
    }
    
    protected function analyzeStatement($statement) {
        // bindパラムを抽出
        $matches = array();
        preg_match_all('/(?<param>:\w+)/', $statement, $matches);
        return $matches['param'];
    }
    
    protected function analyzedBindSet($bind_keys, $bind_set) {
        
        if(empty($bind_keys)) {
            return array();
        }
        
        // [and|or]Where('hoge = :foo', 'fooの値')のパターン
        if(count($bind_keys) == 1 && !is_array($bind_set) ) {
            return array(reset($bind_keys) => $bind_set);
        }
        
        if(count($bind_keys) != count($bind_set)) {
            throw new \InvalidArgumentException('count($bind_keys) != count($bind_set)');
        }
        
        // [and|or]Where('hoge = :hoge1 ', [':hoge1' => 'hoge'])のパターン
        if(array_values($bind_keys) == array_keys($bind_set)) {
            return $bind_set;
        }
        
        // addOr('hoge4 = :hoge4 AND hoge5 = :hoge5', ['value4', 'value5'])のパターン
        if(count($bind_keys) == count($bind_set)) {
            $ret_bind_set = [];
            foreach($bind_keys as $key => $val) {
                $ret_bind_set[$val] = $bind_set[$key];
            }
            return $ret_bind_set;
        }
        
        return array();
    }
    
    public function build() {
        $sql = '';
        foreach($this->statement_list as $statement) {
            if(!$sql) {
                $sql .= (string)$statement['body'];
                continue;
            }
            
            if($statement['connect'] == self::AND_SECT) {
                $sql .= ' AND ' . (string)$statement['body'];
            } elseif($statement['connect'] == self::OR_SECT) {
                $sql .= ' OR ' . (string)$statement['body'];
            }
        }
        
        ($sql) and ($sql = '( ' . $sql . ' )');
        return $sql;
    }
    
    public function bind($bind_values) {
        $this->plusBindSet($bind_values);
    }
    
    // 入れ子の場合に起動
    public function __toString() {
        return $this->build();
    }
    
}

?>
