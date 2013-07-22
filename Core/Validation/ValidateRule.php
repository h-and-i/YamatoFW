<?php

namespace Yamato\Core\Validation;

/**
 * Description of Validation
 *
 * @author hiraishi
 */
abstract class ValidateRule {
    protected static $default_message = '';
    
    protected $is_valid = false;
    protected $target = null;
    protected $error_message = null;
    
    public function __construct($error_message = null) {
        if(!$error_message) {
            $this->error_message = static::$default_message;
        }
    }
    
    public function setTarget($target) {
        $this->target = $target;
    }
    
    public function setErrorMessage($error_message) {
        $this->error_message = $error_message;
    }
    
    public function getErrorMessage() {
        return $this->error_message;
    }
    
    public function isValid() {
        return $this->is_valid;
    }
    
    public function run() {
        if($this->execute()) {
            $this->is_valid = true;
            return $this->is_valid;
        }
        return $this->is_valid;
    }
    
    abstract protected function execute();
    
    public function reset() {
        $this->is_valid = false;
        
    }
    
}

?>
