<?php

namespace Yamato\Core;

use Yamato\Core\Validation\ValidateRawTarget as ValidateRawTarget;
use Yamato\Core\Validation\ValidateRule as ValidateRule;

/**
 * Description of Validator
 *
 * @author hiraishi
 */
class Validator {
    protected $target_list = array();
    
    public function __construct() {
        ;
    }
    
    public function target($name) {
        $this->setTargetName($name);
        $this->setTargetValidState($name, false);
        return $this;
    }
    
    protected function setTargetName($target_name) {
        if(!isset($this->target_list[$target_name])) {
            $this->target_list[$target_name] = array();
        }
    }
    
    protected function setTargetValidState($target_name, $is_valid) {
        $this->target_list[$target_name]['is_valid'] = $is_valid;
    }
    
    public function rule($target_name, $rule_name) {
        $rule = $this->createRule($rule_name);
        $this->assignRule($target_name, $rule);
        return $this;
    }
    
    protected function createRule($rule_name) {
        $class_name = VALIDATION_NAMESPACE . ucfirst($rule_name) . 'ValidateRule';
        
        $rule = new $class_name();
        return $rule;
    }
    
    protected function assignRule($target_name, ValidateRule $rule) {
        $this->target_list[$target_name]['rule'][] = $rule;
    }
    
    public function bind($target_name, $target_object) {
        if(is_string($target_object)) {
            $target_value = $target_object;
            $target_object = (new ValidateRawTarget());
            $target_object->setValue($target_value);
        }
        
        $this->checkBind($target_name, $target_object);
        
        $target_object->setName($target_name);
        $this->setTargetObject($target_name, $target_object);
        return $this;
    }
    
    protected function setTargetObject($target_name, $target_object) {
        $this->target_list[$target_name]['target'] = $target_object;
    }
    
    protected function checkBind($target_name, $target_object) {
        
        if(!array_key_exists($target_name, $this->target_list)) {
            throw new \InvalidArgumentException('Please set target name by target() method before calling bind() method.');
        }
        
        if(!($target_object instanceof Validation\ValidateTarget)) {
            throw new \InvalidArgumentException('$target_object need be ValidateTarget instance in bind() method.');
        }
        
    }
    
    public function run() {
        if(!count($this->target_list)) {
            return true;
        }
        $isValid = false;
        
        foreach($this->target_list as $name => &$object_set) {
            $object_set['is_valid'] = $this->runRule($object_set);
            if(!$object_set['is_valid']) {
                return $isValid;
            }
        }
        
        $isValid = true;
        return $isValid;
    }
    
    protected function runRule($object_set) {
        $is_valid = false;
        if(!array_key_exists('rule', $object_set)) {
            $is_valid = true;
            return $is_valid;
        }
        
        foreach($object_set['rule'] as $rule) {
            $rule->setTarget($object_set['target']->getValue());
            if(!$rule->run()) {
                return $is_valid;
            }
        }
        
        $is_valid = true;
        return $is_valid;
    }
    
    public function getErrorMessage() {
        $error_message = array();
        foreach($this->target_list as $name => $object_set) {
            if($object_set['is_valid']) {
               continue; 
            }
            
            foreach($object_set['rule'] as $rule) {
                if(!$rule->isValid()) {
                    $error_message[$name][] = $rule->getErrorMessage();
                }
            }
        }
        return $error_message;
    }
    
}

?>
