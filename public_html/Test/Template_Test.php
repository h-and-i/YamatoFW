<?php

require_once '../initialize_test.php';

function template_test() {
    $template = new Template();
    $template->assign('hoge', 'aiueodayo!');
    $template->display('hoge.tpl');
}

template_test();

?>
