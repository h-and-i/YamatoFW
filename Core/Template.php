<?php

namespace Yamato\Core;

use Yamato\Core\Template\SmartyDriver as SmartyDriver;

/**
 * Description of Template
 *
 * @author hiraishi
 */
class Template {
    protected $driver = null;
    
    public function __construct($driver = null) {
        if(!$driver) {
            $driver = new SmartyDriver();
            // デフォルトはSmarty
        }
        $this->driver = $driver;
    }
    
    public function setDriver($driver) {
        $this->driver = $driver;
    }
    
    public function assign($name, $value, $options = array()) {
        $this->driver->assign($name, $value, $options);
    }
    
    public function display($template_name = null, $options = array()) {
        return $this->driver->display($template_name, $options);
    }
    
    public function fetch($template_name = null, $options = array()) {
        return $this->driver->fetch($template_name, $options);
    }
}

?>
