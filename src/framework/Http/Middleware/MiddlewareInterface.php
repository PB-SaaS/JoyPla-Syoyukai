<?php

namespace App\Http\Middleware;

use framework\Http\Request;

/**
 * Interface Middleware
 */
interface MiddlewareInterface
{
    /**
     * @param Request $request
     */
    public function process(array $vars) : void; 
}