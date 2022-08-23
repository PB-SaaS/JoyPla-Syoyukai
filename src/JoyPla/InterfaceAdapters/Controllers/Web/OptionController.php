<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use App\SpiralDb\Hospital;
use App\SpiralDb\Tenant;
use framework\Http\Controller;
use framework\Http\View;

class OptionController extends Controller
{
    public function index($vars) {

        $tenant = Tenant::where('tenantId', $this->request->user()->tenantId)->get();
        $hospital = Hospital::where('hospitalId',$this->request->user()->hospitalId)->get();
        $viewModel = $hospital->data->get(0);
        $tenant = $tenant->data->get(0);

        $viewModel->set('tenantKind', $tenant->tenantKind);

        $body = View::forge('html/Option/Index', compact('viewModel'), false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}

