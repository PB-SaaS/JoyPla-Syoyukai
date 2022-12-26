<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use Exception;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestRegisterInputData;
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestRegisterInputPortInterface;
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestDeleteInputData;
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestDeleteInputPortInterface;
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestHistoryInputData;
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestHistoryInputPortInterface;
use JoyPla\Application\InputPorts\Api\ItemRequest\RequestItemDeleteInputData;
use JoyPla\Application\InputPorts\Api\ItemRequest\RequestItemDeleteInputPortInterface;


class ItemRequestController extends Controller
{
    public function history($vars, ItemRequestHistoryInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('list_of_item_request_history')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_item_request_history');

        $search = $this->request->get('search');

        $user = $this->request->user();

        if ($gate->isOnlyMyDivision()) {
            $search['sourceDivisionIds'] = [$user->divisionId];
        }

        $inputData = new ItemRequestHistoryInputData($user, $search);
        $inputPort->handle($inputData);
    }


    public function register($vars, ItemRequestRegisterInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('register_of_item_requests')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_of_item_requests');

        $requestItems = $this->request->get('requestItems');

        $requestType = (int)$this->request->get('requestType');

        $user = $this->request->user();

        $inputData = new ItemRequestRegisterInputData(
            $user,
            $requestItems,
            $requestType,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }


    public function delete($vars, ItemRequestDeleteInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('delete_of_item_request_history')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('delete_of_item_request_history');

        $inputData = new ItemRequestDeleteInputData($this->request->user(), $vars['requestHId'], $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }


    public function itemDelete($vars, RequestItemDeleteInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $requestHId = $vars['requestHId'];
        $requestId = $vars['requestId'];

        if (Gate::denies('update_of_item_request_history')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('update_of_item_request_history');

        $inputData = new RequestItemDeleteInputData($this->request->user(), $requestHId, $requestId, $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }


    public function update($vars, OrderUnapprovedUpdateInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('revision_of_unordered_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('revision_of_unordered_slips');

        $orderId = $vars['orderId'];
        $adjustment = $this->request->get('adjustment');
        $updateModel = $this->request->get('updateModel');
        $comment = $this->request->get('comment');

        $order = [
            'orderId' => $orderId,
            'adjustment' => $adjustment,
            'updateModel' => $updateModel,
            'comment' => $comment,
        ];

        $inputData = new OrderUnapprovedUpdateInputData(
            $this->request->user(),
            $order,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }
}
