<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Web\Consumption\ConsumptionShowInputData;
use JoyPla\Application\InputPorts\Web\Consumption\ConsumptionShowInputPortInterface;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class ConsumptionController extends Controller
{
    public function register($vars)
    {
        if (Gate::denies('register_of_consumption_slips')) {
            Router::abort(403);
        }

        $consumptionUnitPriceUseFlag = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->value('billingUnitPrice')
            ->get()
            ->first();

        $consumptionUnitPriceUseFlag =
            $consumptionUnitPriceUseFlag->billingUnitPrice;
        $body = View::forge(
            'html/Consumption/ConsumptionRegister',
            compact('consumptionUnitPriceUseFlag'),
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function bulkRegister($vars)
    {
        if (Gate::denies('bulkregister_of_consumption_slips')) {
            Router::abort(403);
        }

        $consumptionUnitPriceUseFlag = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->value('billingUnitPrice')
            ->get()
            ->first();
        $consumptionUnitPriceUseFlag =
            $consumptionUnitPriceUseFlag->billingUnitPrice;
        $body = View::forge(
            'html/Consumption/ConsumptionBulkRegister',
            compact('consumptionUnitPriceUseFlag'),
            false
        )->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function index($vars)
    {
        if (Gate::denies('list_of_consumption_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_consumption_slips');
        $body = View::forge('html/Consumption/Index', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function show($vars, ConsumptionShowInputPortInterface $inputPort)
    {
        if (Gate::denies('list_of_consumption_slips')) {
            Router::abort(403);
        }
        $gate = Gate::getGateInstance('list_of_consumption_slips');
        $inputData = new ConsumptionShowInputData(
            $this->request->user(),
            $vars['consumptionId'],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function print($vars, ConsumptionShowInputPortInterface $inputPort)
    {
        if (Gate::denies('list_of_consumption_slips')) {
            Router::abort(403);
        }
        $gate = Gate::getGateInstance('list_of_consumption_slips');
        $inputData = new ConsumptionShowInputData(
            $this->request->user(),
            $vars['consumptionId'],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }
}
