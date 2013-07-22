<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('Asia/Tokyo');

/**
 * 定数定義
 */
define('DS',            DIRECTORY_SEPARATOR);
define('DOC_ROOT_PATH', realpath(__DIR__) . DS);
define('ROOT_PATH',     realpath(__DIR__ . '/../')  . DS);
define('APP_PATH',      ROOT_PATH . 'App' . DS);
define('COMMON_PATH',   ROOT_PATH . 'Common' . DS);
define('CONFIG_PATH',   ROOT_PATH . 'Config' . DS);
define('CORE_PATH',     ROOT_PATH . 'Core'. DS);
define('VENDOR_PATH',   ROOT_PATH . 'Vendor' . DS);
define('VAR_PATH',      ROOT_PATH . 'Var' . DS);
define('LIB_PATH',      CORE_PATH . 'Lib' . DS);
define('EXCEPTION_PATH', CORE_PATH . 'Exception' . DS);
define('FILE_CACHE_STORE_DIR', VAR_PATH . 'Cache' . DS);
define('APP_TEMPLATE_PATH', APP_PATH . 'Template' . DS);
define('TEMPLATE_CONFIG_PATH', CONFIG_PATH . 'Template' . DS);
define('LOGGER_FILE_PATH', '');

// Smarty default
define('SMARTY_DEFAULT_PATH', VAR_PATH . 'Template' . DS . 'Smarty' . DS);

// 名前空間
define('MODEL_NAMESPACE', 'Yamato\\Model\\');
define('CORE_NAMESPACE', 'Yamato\\Core\\');
define('LIB_NAMESPACE', CORE_NAMESPACE . 'Lib\\');
define('EXCEPTION_NAMESPACE', CORE_NAMESPACE . 'Exception\\');
define('VALIDATION_NAMESPACE', CORE_NAMESPACE . 'Validation\\');

// オートローダー設定
require_once COMMON_PATH . 'autoload.php';

// 例外処理
require_once COMMON_PATH . 'exception_init.php';


// リクエスト解析
$request = new Request();
$request->execute();

// bootstrap
$bootstrap = new Bootstrap();

//try {
    $response = $bootstrap->run($request);
//} catch(\Exception $e) {
//      //@todo@ 404エラー画面表示処理とか
//}

// レスポンス返却
$response->sendProtocolHeader();
$response->sendHeader();    
echo $response->getBody();

