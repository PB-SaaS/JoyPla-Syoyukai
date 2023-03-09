<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\Order\OrderShowInputData;
use JoyPla\Application\InputPorts\Api\Order\OrderShowInputPortInterface;
use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterByOrderSlipInputData;
use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterByOrderSlipInputPortInterface;
use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterInputData;
use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterInputPortInterface;
use JoyPla\Application\InputPorts\Api\Received\ReceivedReturnRegisterInputData;
use JoyPla\Application\InputPorts\Api\Received\ReceivedReturnRegisterInputPortInterface;
use JoyPla\Application\InputPorts\Api\Received\ReceivedShowInputPortInterface;
use JoyPla\Application\InputPorts\Api\Received\ReceivedShowInputData;
use JoyPla\Enterprise\Models\OrderStatus;

class ReceivedController extends Controller
{
    public function orderList($vars, OrderShowInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);
        $search = $this->request->get('search');

        if (Gate::denies('receipt')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('receipt');

        if (!$search['orderStatus'] || count($search['orderStatus']) === 0) {
            $search['orderStatus'] = [
                OrderStatus::OrderCompletion,
                OrderStatus::OrderFinished,
                OrderStatus::DeliveryDateReported,
                OrderStatus::PartOfTheCollectionIsIn,
            ];
        }
        $user = $this->request->user();
        if ($gate->isOnlyMyDivision()) {
            $search['divisionIds'] = [$user->divisionId];
        }

        $inputData = new OrderShowInputData($user, $search);
        $inputPort->handle($inputData);
    }

    public function orderRegister(
        $vars,
        ReceivedRegisterByOrderSlipInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('receipt')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('receipt');

        $registerModel = $this->request->get('registerModel');
        $inputData = new ReceivedRegisterByOrderSlipInputData(
            $this->request->user(),
            $vars['orderId'],
            $registerModel,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function register(
        $vars,
        ReceivedRegisterInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('receipt')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('receipt');

        $receivedItems = $this->request->get('receivedItems');
        $inputData = new ReceivedRegisterInputData(
            $this->request->user(),
            $receivedItems,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function show($vars, ReceivedShowInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);
        $user = $this->request->user();
        $search = $this->request->get('search');
        if (Gate::denies('list_of_acceptance_inspection_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_acceptance_inspection_slips');

        if ($gate->isOnlyMyDivision()) {
            $search['divisionIds'] = [$user->divisionId];
        }

        $inputData = new ReceivedShowInputData($user, $search);
        $inputPort->handle($inputData);
    }

    public function returnRegister(
        $vars,
        ReceivedReturnRegisterInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('register_return_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_return_slips');

        $returnItems = $this->request->get('returnItems');

        $inputData = new ReceivedReturnRegisterInputData(
            $this->request->user(),
            $vars['receivedId'],
            $returnItems,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }
}
