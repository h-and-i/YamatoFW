<?php

namespace Yamato\Core\Validation;

/**
 * Description of Validation
 *
 * @author hiraishi
 */
class NotEmptyValidateRule extends ValidateRule {
    protected static $default_message = '入力してください。';
    
    protected function execute() {
        $target = $this->target;
        if(is_string($target)) {
            $target = trim($target);
        }
        
        if(is_null($target)
            || $target === ''
            || (is_array($target) && !count($target)))
        {
            return false;
        }
        return true;
    }
    
}

?>
