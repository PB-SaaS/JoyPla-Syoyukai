<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Http\Controller;
use framework\Http\View;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class OptionController extends Controller
{
    public function index($vars)
    {
        $tenant = ModelRepository::getTenantInstance()
            ->where('tenantId', $this->request->user()->tenantId)
            ->get();
        $hospital = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->get();
        $viewModel = $hospital->first();
        $tenant = $tenant->first();

        $viewModel->set('tenantKind', $tenant->tenantKind);

        $body = View::forge(
            'html/Option/Index',
            compact('viewModel'),
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}
