<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use App\SpiralDb\Hospital;
use Auth;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Web\ItemRequest\ItemRequestIndexInputData;
use JoyPla\Application\InputPorts\Web\ItemRequest\ItemRequestIndexInputPortInterface;

class ItemRequestController extends Controller
{
    public function register($vars)
    {
        if (Gate::denies('register_of_item_requests')) {
            Router::abort(403);
        }

        $payoutUnitPriceUseFlag = (Hospital::where('hospitalId', $this->request->user()->hospitalId)->value('payoutUnitPrice')->get())->data->get(0);
        $payoutUnitPriceUseFlag = $payoutUnitPriceUseFlag->payoutUnitPrice;
        $body = View::forge('html/ItemRequest/Register', compact('payoutUnitPriceUseFlag'), false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function history($vars)
    {
        if (Gate::denies('list_of_item_request_history')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_item_request_history');
        $body = View::forge('html/ItemRequest/History', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function index($vars, ItemRequestIndexInputPortInterface $inputPort)
    {
        if (Gate::denies('list_of_item_request_history')) {
            Router::abort(403);
        }
        $gate = Gate::getGateInstance('list_of_item_request_history');
        $inputData = new ItemRequestIndexInputData($this->request->user(), $vars['requestHId'], $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }

    public function show($vars)
    {
        if (Gate::denies('list_of_item_requests')) {
            Router::abort(403);
        }
        $gate = Gate::getGateInstance('list_of_item_requests');
        $body = View::forge('html/ItemRequest/Show', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function pickingList($vars, ItemRequestsInputPortInterface $inputPort)
    {
        if (Gate::denies('list_of_item_requests')) {
            Router::abort(403);
        }
        $gate = Gate::getGateInstance('list_of_item_requests');
        $inputData = new ItemRequestsInputData($this->request->user(), $vars['requestHId'], $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }
}
