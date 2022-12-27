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
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestUpdateInputData;
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestUpdateInputPortInterface;
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestTotalizationInputData;
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestTotalizationInputPortInterface;


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


    public function update($vars, ItemRequestUpdateInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('update_of_item_request_history')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('update_of_item_request_history');

        $requestHId = $vars['requestHId'];
        $requestType = $this->request->get('requestType');
        $updateModel = $this->request->get('updateModel');

        $itemRequest = [
            'requestHId' => $requestHId,
            'requestType' => $requestType,
            'updateModel' => $updateModel
        ];

        $inputData = new ItemRequestUpdateInputData($this->request->user(), $itemRequest, $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }

    public function totalization($vars, ItemRequestTotalizationInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('totalization_of_item_requests')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('totalization_of_item_requests');

        $search = $this->request->get('search');

        $user = $this->request->user();

        if ($gate->isOnlyMyDivision()) {
            $search['sourceDivisionIds'] = [$user->divisionId];
        }

        $inputData = new ItemRequestTotalizationInputData($user, $search);
        //       $inputPort->handle($inputData);
    }
}
