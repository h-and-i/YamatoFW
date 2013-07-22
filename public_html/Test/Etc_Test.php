<?php

require_once '../initialize_test.php';

class Hoge {
    public function foo() {
        $a = ['a', 'b', 'c'];
        
        $ret = array_map(function($v) { return $this->bar($v); }, $a);
        
        return $ret;
    }
    
    protected function bar($value) {
        return 'hoge::' . $value;
    }
}

$hoge = new Hoge();

$ret = $hoge->foo();
var_dump($ret);
exit;
    

$hoge = array('kakeko');

$test = \Arr::get($hoge, 'aiueo');
var_dump($test);

?>
