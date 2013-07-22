<?php

namespace Yamato\Core\Template;

/**
 * テンプレート処理
 *
 * @author hiraishi
 */
abstract class TemplateDriver {
    protected $file = null;
    
    public function setFile($file) {
        $this->file = $file;
    }
    
    public function getFile() {
        return $this->file;
    }
    
    abstract public function assign($name, $value, $options = null);
    
    abstract public function display($template_name = null, $options = null);
    
    abstract public function fetch($template_name = null, $options = null);
}

?>