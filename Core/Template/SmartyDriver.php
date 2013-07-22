<?php

namespace Yamato\Core\Template;

class SmartyDriver extends TemplateDriver {
    protected $smarty = null;
    
    public function __construct($template_dir = null) {
        // 設定ファイル読み込み
        $config = \Yamato\Core\Config::get('smarty.yml', TEMPLATE_CONFIG_PATH);
        
        $this->smarty = new \Smarty();
        
        $template_dir = $template_dir ?: APP_TEMPLATE_PATH;
        $compile_dir = $config['compile_dir'] ?: SMARTY_DEFAULT_PATH.'templates_c';
        $config_dir = $config['config_dir'] ?: SMARTY_DEFAULT_PATH.'configs';
        $cache_dir = $config['cache_dir'] ?: SMARTY_DEFAULT_PATH.'cache';
        
        $this->initializeSetting([$template_dir, $compile_dir, $config_dir, $cache_dir]);
        
        $this->smarty->template_dir = $template_dir;
        $this->smarty->compile_dir = $compile_dir;
        $this->smarty->config_dir = $config_dir;
        $this->smarty->cache_dir = $cache_dir;
    }
    
    protected function initializeSetting($settings) {
        list($template_dir, $compile_dir, $config_dir, $cache_dir) = $settings;
        
        (!is_dir($template_dir)) and (@mkdir($template_dir, 0775));
        chmod($template_dir, 0775);
        
        (!is_dir($compile_dir)) and (@mkdir($compile_dir, 0775));
        chmod($compile_dir, 0775);
        
        (!is_dir($config_dir)) and (@mkdir($config_dir, 0775));
        chmod($config_dir, 0775);
        
        (!is_dir($cache_dir)) and (@mkdir($cache_dir, 0775));
        chmod($cache_dir, 0775);
    }
    
    public function assign($name, $value, $options = null) {
        $this->smarty->assign($name, $value);
    }
    
    public function fetch($template_name = null, $options = null) {
        return $this->smarty->fetch($template_name);
    }
    
    public function display($template_name = null, $options = null) {
        $this->smarty->display($template_name);
    }
}

?>
