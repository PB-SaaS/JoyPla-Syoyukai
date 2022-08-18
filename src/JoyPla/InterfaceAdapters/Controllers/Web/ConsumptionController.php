<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use App\SpiralDb\Hospital;
use Auth;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Web\Consumption\ConsumptionIndexInputData;
use JoyPla\Application\InputPorts\Web\Consumption\ConsumptionIndexInputPortInterface;

class ConsumptionController extends Controller
{
    public function register($vars ) {

        if(Gate::denies('register_of_consumption_slips'))
        {
            Router::abort(403);
        }

        $consumptionUnitPriceUseFlag = (Hospital::where('hospitalId', $this->request->user()->hospitalId)->value('billingUnitPrice')->get())->data->get(0);
        $consumptionUnitPriceUseFlag = $consumptionUnitPriceUseFlag->billingUnitPrice;
        $body = View::forge('html/Consumption/ConsumptionRegister', compact('consumptionUnitPriceUseFlag'), false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    } 

    public function show($vars){
        if(Gate::denies('list_of_consumption_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_consumption_slips');
        $body = View::forge('html/Consumption/ConsumptionShow', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function index($vars, ConsumptionIndexInputPortInterface $inputPort ) {
        if(Gate::denies('list_of_consumption_slips'))
        {
            Router::abort(403);
        }
        $gate = Gate::getGateInstance('list_of_consumption_slips');
        $inputData = new ConsumptionIndexInputData($this->request->user(),$vars['consumptionId'],$gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }

    public function print($vars, ConsumptionIndexInputPortInterface $inputPort ) {
        if(Gate::denies('list_of_consumption_slips'))
        {
            Router::abort(403);
        }
        $gate = Gate::getGateInstance('list_of_consumption_slips');
        $inputData = new ConsumptionIndexInputData($this->request->user(),$vars['consumptionId'],$gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }
}

