<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use App\SpiralDb\Hospital;
use framework\Http\Controller;
use framework\Http\View;

class OptionController extends Controller
{
    public function index($vars) {

        $hospital = Hospital::where('hospitalId',$this->request->user()->hospitalId)->get();
        $hospital = $hospital->data->get(0);
        var_dump( $hospital );
        $body = View::forge('html/Option/Index', compact('hospital'), false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}

