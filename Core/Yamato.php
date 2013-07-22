<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Yamato\Core;

/**
 * アプリケーション全体の設定や動作などを決定するクラス
 *
 * @author hiraishi
 */
class Yamato {
    // アプリケーションで現在使用している設定を格納
    private $config = null;
    
    /**
     * 設定情報を格納する
     * 
     * @param type $config
     */
    public function setConfig($config) {
        $this->config = $config;
    }
    
    /**
     * 設定情報を取得
     * 
     * @return type
     */
    public function getConfig() {
        return $this->config;
    }
    
}

?>
