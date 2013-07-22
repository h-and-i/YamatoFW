<?php

/**
 * php標準の例外クラスを継承したクラス
 * 
 */

namespace Yamato\Core\Exception;

/**
 * 例外クラス
 *
 * @author hiraishi
 */
class CoreException extends \Exception { }

class CoreRuntimeException extends CoreException { }

class File404Exception extends CoreRuntimeException { }

?>
