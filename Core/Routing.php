<?php

namespace Yamato\Core;

/**
 * Routing処理
 *
 * @author hiraishi
 */
class Routing {
    protected $params_list = null;
    protected $setting = null;
    
    public function __construct($setting) {
        if(!isset($setting['controller'])) {
            // @todo@ throw Exception
        }
        
        if(!isset($setting['action'])) {
            // @todo@ throw Exception
        }
        
        $this->setting = $setting;
    }
    
    public function getRouteInfo() {
        $controller = $this->setting['controller'];
        $action = $this->setting['action'];
        
        $params = explode('/', $this->params_list);
        return [$controller, $action, $params];
    }
    
    public function setParamsList($url, $route) {
        $param_list = preg_replace("#^{$route}#", '', '/'.$url);
        $this->params_list = trim($param_list, '/');
    }
    
}

?>