<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Exception\NotFoundException;
use framework\Http\Controller;
use framework\Http\View;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class AcceptanceController extends Controller
{
    public function index($vars)
    {
        $body = View::forge('html/Acceptance/Index', [
            'userDivisionId' => $this->request->user()->divisionId,
        ], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
    public function show($vars)
    {
        $acceptance = ModelRepository::getAcceptanceInstance()->where('hospitalId', $this->request->user()->hospitalId)->where('acceptanceId', $vars['acceptanceId'])->get();

        if($acceptance->count() == 0){
            throw new NotFoundException('not found' , 404);
        }

        $acceptance = $acceptance->first();

        $isUnitPrice = ModelRepository::getHospitalInstance()->where('hospitalId',$this->request->user()->hospitalId)->resetValue('payoutUnitPrice')->get()->first();

        $body = View::forge('html/Acceptance/Show', [
            'acceptanceId' => $vars['acceptanceId'],
            'isUpdateSuccess' => (!gate('is_user') || (gate('is_user') && $this->request->user()->divisionId === $acceptance->sourceDivisionId)),
            'isPayoutSuccess' => (!gate('is_user') || (gate('is_user') && $this->request->user()->divisionId === $acceptance->targetDivisionId)),
            'isUnitPrice' => ($isUnitPrice->payoutUnitPrice == '1')
        ], false)->render();

        echo view('html/Common/Template', [
            'body' => $body,
        ], false)->render();
    }
}
