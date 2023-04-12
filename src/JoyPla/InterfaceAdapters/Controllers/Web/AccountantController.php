<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Http\Controller;
use framework\Http\View;

class AccountantController extends Controller
{
    public function index($vars)
    {
        $body = View::forge('html/Accountant/Index', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}
