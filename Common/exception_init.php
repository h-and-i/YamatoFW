<?php

set_exception_handler(
        function ($exception)
        {
            echo 'Uncaught Exception:' . $exception->getMessage();
            debug_print_backtrace();
        }
);

// エラー処理を例外処理に変換
set_error_handler(
        function ($errno, $errstr, $errfile, $errline )
        {
            //@演算子によるエラー抑止の場合
            if(!error_reporting()) {
                return true;
            }
            
            switch($errno) {
                case E_NOTICE:
                    //return true; // エラーを抑止したい場合はtrueを返却する
                    break;
            }
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
);
