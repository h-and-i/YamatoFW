<?php

require_once '../initialize_test.php';

function notEmptyTest() {
    $testvalue = 'a';
    $validation = new \Yamato\Core\Validation\NotEmptyValidateRule();
    $validation->setTarget($testvalue);
    $validation->run();
    var_dump($validation->isValid());
    
    // リセット
    $validation->reset();
    var_dump($validation->isValid());
}

notEmptyTest();


?>
