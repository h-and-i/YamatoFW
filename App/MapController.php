<?php

/**
 * Description of IndexController
 *
 * @author hiraishi
 */
class MapController extends Controller {
    
    public function index() {
        $template = new Template();
        $template->assign('hoge', 'aiueodayo!');
        $hoge = $template->fetch('googlemap.tpl');
        return $hoge;
    }
}

?>
