<?php

namespace Test\Exceptions;

use Exception;
use framework\Exception\ExceptionHandler as BaseExceptionHandler;
use framework\Http\View;

class WebExceptionHandler extends BaseExceptionHandler {
    public $debug = false;

    public function render($request, Exception $exception)
    {
        $body = View::forge('html/Common/Error', [ 
            'code' => $exception->getCode(), 
            'message' => $exception->getMessage()
        ], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}