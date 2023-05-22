<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;

class PayoutController extends Controller
{
    public function register($vars)
    {
        if (Gate::denies('register_of_payout')) {
            Router::abort(403);
        }
        $body = View::forge('html/Payout/Register', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}
