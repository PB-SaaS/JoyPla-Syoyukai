<?php

namespace Test\Exceptions;

use ApiResponse;
use Exception;
use framework\Exception\ExceptionHandler as BaseExceptionHandler;
use framework\Http\View;

class ApiExceptionHandler extends BaseExceptionHandler {
    public $debug = false;

    public function render($request, Exception $exception)
    {
        echo (new ApiResponse( [], 0 , $exception->getCode(), $exception->getMessage() , [ "path" , $request->getRequestUri() ]))->toJson();
    }
}