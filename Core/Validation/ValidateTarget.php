<?php

namespace Yamato\Core\Validation;

/**
 * Description of Validation
 *
 * @author hiraishi
 */
abstract class ValidateTarget {
    // バリデート対象となる実際の値
    protected $name = null;
    protected $value = null;
    protected $message = null;
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setValue($value) {
        $this->value = $value;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function setMessage($message) {
        $this->message = $message;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function __toString() {
        return $this->value;
    }
    
}

?>
