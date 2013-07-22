<?php

namespace Yamato\Core\Logger;

/**
 * Description of Logger
 *
 * @author hiraishi
 */
class Logger {
    const DEBUG_LV = 'looger.debug';
    const INFO_LV = 'logger.info';
    const WARN_LV = 'looger.warn';
    const ERROR_LV = 'looger.error';
    const FATAL_LV = 'logger.fatal';
    
    protected $file_path = null;
    
    public function __construct($file_path =null) {
        $file_path or ($file_path = LOGGER_FILE_PATH);
        
        $this->file_path = $file_path;
    }
    
    // ログファイルをローテート＆zip
    protected function locate() {
        
    }
    
    public function write($original_message, $level = self::INFO_LV) {
        $contents = $this->getLogContents($original_message, $level);
        error_log($contents, 3, $this->file_path);
        $this->locate();
    }
    
    protected function getLogContents($original_message, $level) {
        // 改行除去
        $original_message = preg_replace('[\n\r]', '', $original_message);
        
        $log_contents = "{$level}:" . $original_message;
        return $log_contents;
    }
}

?>
