<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use framework\Http\Controller;
use framework\Http\View;

class ReturnController extends Controller
{
    
    public function show($vars)
    {
        $body = View::forge('html/Return/Show', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}
 