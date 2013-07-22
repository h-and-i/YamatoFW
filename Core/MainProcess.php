<?php

namespace Yamato\Core;

/**
 * メイン処理（一連の文脈）に関する抽象基底クラス
 * 
 * どのような文脈であれ、
 * 基本はRequest(要求）があり、処理があり、Response（返答）があることが前提。
 * 
 */
abstract class MainProcess {
    protected $request  = null;
    protected $response = null;
    
    //put your code here
    public function setRequest(Request $request) {
        $this->request = $request;
    }
    
    public function getRequest() {
        return $this->request;
    }
    
    public function setResponse(Response $response) {
        $this->response = $response;
    }
    
    public function getReponse() {
        return $this->response;
    }
    
    /**
     * メイン処理を記述
     */
    abstract public function execute();
    
}

?>
