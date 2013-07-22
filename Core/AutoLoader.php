<?php
namespace Yamato\Core;

require_once LIB_PATH . 'Utils.php';

// smartyのバグ回避
// require_once VENDOR_PATH . 'Smarty' . DS. 'libs' . DS . 'Smarty.class.php';

/**
 * オートローダー
 * 
 * @author hiraishi
 */
class AutoLoader {
    public static $static_load_classes = array();
    public static $alias_classes = array();
    
    public static function initialize() {
        
        static::setStaticLoadClasses(
            [
                'Spyc' => VENDOR_PATH. 'spyc' . DS . 'spyc.php',
                'Smarty' => VENDOR_PATH . 'Smarty' . DS. 'libs' . DS . 'Smarty.class.php',
                'ImageWorkshopLayer' =>  VENDOR_PATH . 'PHPImageWorkshop' . DS. 'ImageWorkshop.php',
            ]
        );
        
        static::setCoreExceptionClasses(
                    [
                        'CoreException',
                        'CoreRuntimeException',
                        'File404Exception',
                    ]
            );

        static::setAliasClasses(
                [
                    LIB_NAMESPACE.'Arr' => 'Arr',
                    LIB_NAMESPACE.'Utils' => 'Utils',
                ]
        );
    }
    
    public static function setCoreExceptionClasses($class_name_list) {
        $file_path = EXCEPTION_PATH . 'CoreException.php';
        
        foreach($class_name_list as $class_name) {
            $full_name = EXCEPTION_NAMESPACE . $class_name;
            static::setStaticLoadClass(
                    $full_name,
                    $file_path
            );
            static::setAliasClass($full_name, $class_name);
        }
    }
    
    public static function setStaticLoadClass($class, $path) {
        static::$static_load_classes[$class] = $path;
    }
    
    public static function setStaticLoadClasses($path_list) {
        foreach($path_list as $class => $path ) {
            static::setStaticLoadClass($class, $path);
        }
    }
    
    public static function setAliasClasses(array $aliases) {
        foreach($aliases as $full_name => $alias) {
            static::setAliasClass($full_name, $alias);
        }
    }
    
    public static function setAliasClass($full_name, $alias) {
        static::$alias_classes[$alias] = ['full' => $full_name];
    }
    
    public static function entity($class_name) {
        $class_name = ltrim($class_name, '\\');
        
        if(static::isRegistedStaticClass($class_name)) {
            return Lib\Utils::requireOnce(static::$static_load_classes[$class_name]);
        }
        
        if(static::isAliasClass($class_name)) {
            $full_name = static::$alias_classes[$class_name]['full'];
            return static::loadClassAndAlias($full_name, $class_name);
        }
        
        if(static::isCoreClass($class_name)) {
            $full_name = CORE_NAMESPACE . $class_name;
            return static::loadClassAndAlias($full_name, $class_name);
        }
        
        try {
            Lib\Utils::requireOnce(static::getClassPath($class_name));
        } catch (\Yamato\Core\Exception\File404Exception $e) {
            //throw new Exception\CoreRuntimeException($class_name . ' class autoload fault.');
        }
//        
//        if(file_exists($file_name)){
//            require_once $file_name;
//            return true;
//        }
//        throw new Exception\CoreRuntimeException($class_name . ' class autoload fault.');
    }
    
    protected static function loadClassAndAlias($full_name, $alias) {
        class_alias($full_name, $alias);
        return static::entity($full_name);
    }
    
    protected static function getClassPath($full_class_name) {
        $class_path = ROOT_PATH . '../' . str_replace('\\', DS, $full_class_name);
        $file_path  = $class_path . '.php';
        return $file_path;
    }
    
    protected static function isCoreClass($class_name) {
        $file_path = static::getClassPath(CORE_NAMESPACE . $class_name);
        return file_exists($file_path);
    }
    
    protected static function isRegistedStaticClass($class_name) {
        $is_registed = false;
        
        if(array_key_exists($class_name, static::$static_load_classes)
                && file_exists(static::$static_load_classes[$class_name]))
        {
            $is_registed = true;
        }
        return $is_registed;
    }
    
    protected static function isAliasClass($class_name) {
        if(array_key_exists($class_name, static::$alias_classes)) {
            return true;
        }
        return false;
    }
    
}

?>
