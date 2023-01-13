<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use App\SpiralDb\Hospital;
use Auth;
use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Web\ItemRequest\ItemRequestShowInputData;
use JoyPla\Application\InputPorts\Web\ItemRequest\ItemRequestShowInputPortInterface;
use JoyPla\Application\InputPorts\Web\ItemRequest\PickingListInputData;
use JoyPla\Application\InputPorts\Web\ItemRequest\PickingListInputPortInterface;

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

    public function show($vars, ItemRequestShowInputPortInterface $inputPort)
    {
        if (Gate::denies('list_of_item_request_history')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_item_request_history');
        $inputData = new ItemRequestShowInputData($this->request->user(), $vars['requestHId'], $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }

    public function totalization($vars)
    {
        if (Gate::denies('totalization_of_item_requests')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('totalization_of_item_requests');
        $body = View::forge('html/ItemRequest/Totalization', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function pickingList($vars, PickingListInputPortInterface $inputPort)
    {
        $token = $this->request->get('_token');
        Csrf::validate($token, true);

        if (Gate::denies('totalization_of_item_requests')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('totalization_of_item_requests');

        $search = $this->request->get('search');

        $user = $this->request->user();

        if ($gate->isOnlyMyDivision()) {
            $search['targetDivisionIds'] = [$user->divisionId];
        }

        $inputData = new PickingListInputData($this->request->user(), $search, $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }
}
