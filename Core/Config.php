<?php

namespace Yamato\Core;

use Yamato\Exception\FileNotFoundException as FileNotFoundException;
use Yamato\Core\Cache\FileCache as FileCache;

/**
 * コンフィグ群を管理するクラス
 * 
 */
class Config {
    use \Yamato\Core\Traits\StaticSingleton;
    
    protected $directory = null;
    protected $file_name = null;
    protected $cache_list = array();
    
    /**
     * configを取得する
     * Yamato/Config下のファイル名（拡張子以外）を指定する
     * 
     * @param type $config_name
     */
    public static function get($file_name, $directory = null) {
        $directory = $directory ?: CONFIG_PATH;
        
        $mine = static::getInstance();
        $mine->setDirectory($directory)
                ->setFileName($file_name);
        
        $cache = $mine->getCache();
        if($cache) {
            return $cache;
        }
        
        $mine->isExistFile();
        $data = $mine->getDataFromFile();
        $mine->setConfigDataToCache($data);
        
        return $data;
    }
    
    protected function setDirectory($directory) {
        $this->directory = $directory;
        return $this;
    }
    
    protected function setFileName($file_name) {
        $this->file_name = $file_name;
        return $this;
    }
    
    protected function getAbsoluteFilePath() {
        if(!$this->directory || !$this->file_name) {
            throw new \RuntimeException('directori or file_name not set.');
            return;
        }
        return $this->directory . $this->file_name;
    }
    
    protected function isExistFile() {
        $file_path = $this->getAbsoluteFilePath();
        // ファイル存在チェック
        if(!file_exists($file_path)){
            throw new FileNotFoundException($file_path . ' config file not found.');
        }
    }
    
    protected function getDataFromFile() {
        $file_path = $this->getAbsoluteFilePath();
        
        // ファイルキャッシュ使用時
        $file_cache = new FileCache();
        if($file_cache->isCached($file_path)) {
            return $file_cache->load($file_path);
        }
        
        // spyc
        $data = \Spyc::YAMLLoad($file_path);
        
        // ファイルキャッシュに保存
        $file_cache->store($file_path, $data);
        return $data;
    }
    
    protected function setConfigDataToCache($data) {
        $file_path = $this->getAbsoluteFilePath();
        
        // cacheに保存
        $key = $this->getCacheKey($file_path);
        $this->setCache($key, $data);
    }
    
    protected function getCacheKey($file_path) {
        return md5($file_path);
    }
    
    protected function setCache($key, $config_contents) {
        $this->cache_list[$key] = $config_contents;
    }
    
    protected function getCache() {
        $file_path = $this->getAbsoluteFilePath();
        $key = $this->getCacheKey($file_path);
        
        // cacheがあれば返却
        if(array_key_exists($key, $this->cache_list)) {
            return $this->cache_list[$key];
        }
        return null;
    }
}

?>
