<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class PayoutController extends Controller
{
    public function register($vars)
    {
        if (Gate::denies('register_of_payout')) {
            Router::abort(403);
        }

        $payoutUnitPriceUseFlag = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->resetValue(['payoutUnitPrice'])
            ->get()
            ->first();
        $payoutUnitPriceUseFlag =
            $payoutUnitPriceUseFlag->payoutUnitPrice;

        $body = View::forge('html/Payout/Register', compact('payoutUnitPriceUseFlag'), false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
}
