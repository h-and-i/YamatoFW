<?php

namespace Yamato\Core;

/**
 * Request処理
 *
 * @author hiraishi
 */
class Request {
    const CONTROLLER_NAME = 'Controller';
    
    protected $url = null;
    protected $controller = null;
    protected $action = null;
    protected $params = array();
    protected $user_agent;
    
    public function __construct() {
    }
    
    public function getUrl() {
        return $url;
    }
    
    public function setUrl($url) {
        $this->url = $url;
    }
    protected function getRequestUri() {
        if(isset($_SERVER['REQUEST_URI'])) {
            $raw_data = trim($_SERVER['REQUEST_URI'], '/');
        } elseif(isset($_SERVER['QUERY_STRING'])) {
            $raw_data = trim($_SERVER['QUERY_STRING'], '/');
        }
        $this->url = $raw_data;
    }
    
    protected function setControllerName($controller) {
        $this->controller = $controller;
    }
    
    protected function setActionName($action) {
        $this->action = $action;
    }
    
    protected function setParams($params) {
        $this->params = $params;
    }
    
    public function getParams() {
        return $this->params;
    }
    
    protected function setUserAgent() {
        $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
    }
    
    public function execute() {
        $this->getRequestUri();
        
        // @todo@ ルーティングがあれば、そちら優先に設定を行う
        if($routing = $this->findRouting()) {
            list($controller, $action, $params) = $routing->getRouteInfo();
            $this->setControllerName($controller);
            $this->setActionName($action);
            $this->setParams($params);
            return;
        }
        
        $this->setRequestValues();
        $this->setUserAgent();
    }
    
    protected function setRequestValues() {
        $values = explode('/', $this->url);
        
        if(!is_array($values) || count($values) < 2) {
            $values = ['index', 'index', []];
        }
        $this->setControllerName($values[0]);
        $this->setActionName($values[1]);
        $this->setParams(array_slice($values, 2));
    }
    
    // @todo@ 実装すること
    protected function findRouting() {
        $route_config = Config::get('routing.yml');
        
        $routing = null;
        foreach($route_config as $route => $setting) {
            if(preg_match("#^{$route}#", '/'. $this->url)) {
                $routing = new Routing($setting);
                $routing->setParamsList($this->url, $route);
            }
        }
        return $routing;
    }
    
    public function getControllerClassName() {
        $class_name = ucfirst($this->controller) . static::CONTROLLER_NAME;
        return $class_name;
    }
    
    public function getAction() {
        return $this->action;
    }
}

?>