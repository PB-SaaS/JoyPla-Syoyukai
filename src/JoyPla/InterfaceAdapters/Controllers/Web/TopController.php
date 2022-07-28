<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use framework\Http\Controller;
use framework\Http\View;

class TopController extends Controller
{
    public function index($vars) {
        $body = View::forge('html/Common/Top', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function orderpage($vars) {
        $body = View::forge('html/Common/OrderPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function consumptionpage($vars)
    {
        $body = View::forge('html/Common/ConsumptionPage', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}

