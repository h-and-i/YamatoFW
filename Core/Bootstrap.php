<?php

namespace Yamato\Core;

/**
 *
 * @author hiraishi
 */
class Bootstrap {
    protected $request = null;
    protected $controller = null;
    
    public function run(Request $request) {
        $this->request = $request;
        
        $controller = $this->getController();
        $action = $request->getAction();
        
        $response = $this->callAppMethod($request);
        
        if(!($response instanceof Response)) {
            $body = $response;
            $response = new Response();
            
            if(!$body) {
                // @todo@ $bodyが空の場合はlogに残す
            }
            $response->setBody($body);
        }
        return $response;
    }
    
    protected function callAppMethod(Request $request) {
        $controller = $this->getController();
        $action = $request->getAction();
        
        if(!method_exists($controller, $action)) {
            throw new \CoreRuntimeException(
                        $request->getControllerClassName() .' doesn\'t have '
                            . "[[$action]]" .' method. Please make it.'
                    );
        }
        return $controller->$action($request->getParams());
    }
    
    public function getController() {
        if($this->controller) {
            return $this->controller;
        }
        
        $class_name = $this->request->getControllerClassName();
        $file_path = APP_PATH . $class_name . '.php';
        
        if(file_exists($file_path)) {
            require_once $file_path;
            $this->controller = new $class_name();
        } else {
            throw new Exception\File404Exception($file_path .' is not found. Please make it.');
        }
        return $this->controller;
    }
    
}

?>