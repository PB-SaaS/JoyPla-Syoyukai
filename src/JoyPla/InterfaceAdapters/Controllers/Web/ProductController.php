<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Http\Controller;
use framework\Http\View;

class ProductController extends Controller
{
    public function labelIndex($vars)
    {
        $body = View::forge(
            'html/Product/Label/Index',
            [],
            false
        )->render();

        echo view('html/Common/Template', compact('body'), false)->render();
    }
}
