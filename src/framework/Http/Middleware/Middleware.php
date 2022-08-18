<?php

namespace App\Http\Middleware;

use framework\Http\Request;

/**
 * Interface Middleware
 */
class Middleware
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}