<?php

require_once '../initialize_test.php';

function validator_test() {

    $validator = new Validator();

    $validator->target('hoge')
                ->rule('hoge', 'notEmpty')
                ->bind('hoge', '')
                ->run();
    
    var_dump($validator->getErrorMessage());
    
}

validator_test();

?>
