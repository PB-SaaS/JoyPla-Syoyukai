<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;

class StocktakingController extends Controller
{
    
    public function import($vars)
    {
        
        if(Gate::denies('register_of_stocktaking_slips') )
        {
            Router::abort(403);
        }

        $body = View::forge('html/Stocktaking/Import', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}
 