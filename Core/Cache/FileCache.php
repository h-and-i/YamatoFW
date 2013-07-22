<?php

namespace Yamato\Core\Cache;

/**
 * Description of FileCache
 *
 * @author hiraishi
 */
class FileCache implements \Yamato\Core\Cache {
    protected $cache_dir = null;
    protected $cache_file = null;
    
    public function __construct($cache_dir = null, $cache_file = null) {
        $this->cache_dir = $cache_dir ?: FILE_CACHE_STORE_DIR;
    }
    
    /**
     * 
     * @param type $store_key キャッシュの対象となるファイルの絶対パス名
     * @param type $data 
     * @param type $options
     */
    public function store($store_key, $data, $options = array()) {
        if($this->isCached($store_key)) {
            return true;
        }
        $serialized_data = serialize($data);
        file_put_contents($this->getCacheFullPath($store_key), $serialized_data);
    }
    
    public function load($store_key, $options = array()) {
        // キャッシュ存在チェック
        if($this->isCached($store_key)) {
            return unserialize(file_get_contents($this->getCacheFullPath($store_key)));
        }
        return null;
    }
    
    public function reset($store_key, $options = array()) {
        ;
    }
    
    public function isCached($store_key) {
        $path = $this->getCacheFullPath($store_key);
        return (file_exists($path)) and (filemtime($path) >= filemtime($store_key));
    }
    
    protected function getCacheFullPath($store_key) {
        $cache_file_full_path = $this->cache_dir . md5($store_key);
        return $cache_file_full_path;
    }
}

?>
