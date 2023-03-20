<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Web\Received\OrderReceivedSlipIndexInputData;
use JoyPla\Application\InputPorts\Web\Received\OrderReceivedSlipIndexInputPortInterface;
use JoyPla\Application\InputPorts\Web\Received\ReceivedIndexInputData;
use JoyPla\Application\InputPorts\Web\Received\ReceivedIndexInputPortInterface;
use JoyPla\Application\InputPorts\Web\Received\ReceivedLabelInputData;
use JoyPla\Application\InputPorts\Web\Received\ReceivedLabelInputPortInterface;

class ReceivedController extends Controller
{
    public function orderList($vars)
    {
        if (Gate::denies('receipt')) {
            Router::abort(403);
        }
        $body = View::forge('html/Received/OrderList', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function orderReceivedSlipIndex(
        $vars,
        OrderReceivedSlipIndexInputPortInterface $inputPort
    ) {
        if (Gate::denies('receipt')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('receipt');

        $inputData = new OrderReceivedSlipIndexInputData(
            $this->request->user(),
            $vars['orderId'],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function register($vars)
    {
        if (Gate::denies('receipt')) {
            Router::abort(403);
        }
        $body = View::forge('html/Received/Register', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function show($vars)
    {
        if (Gate::denies('list_of_acceptance_inspection_slips')) {
            Router::abort(403);
        }
        $body = View::forge('html/Received/Show', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function index($vars, ReceivedIndexInputPortInterface $inputPort)
    {
        if (Gate::denies('list_of_acceptance_inspection_slips')) {
            Router::abort(403);
        }
        $gate = Gate::getGateInstance('list_of_acceptance_inspection_slips');
        $inputData = new ReceivedIndexInputData(
            $this->request->user(),
            $vars['receivedId'],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function labelsetting(
        $vars,
        ReceivedIndexInputPortInterface $inputPort
    ) {
        if (Gate::denies('list_of_acceptance_inspection_slips')) {
            Router::abort(403);
        }
        $gate = Gate::getGateInstance('list_of_acceptance_inspection_slips');
        $inputData = new ReceivedIndexInputData(
            $this->request->user(),
            $vars['receivedId'],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function label($vars, ReceivedLabelInputPortInterface $inputPort)
    {
        if (Gate::denies('list_of_acceptance_inspection_slips')) {
            Router::abort(403);
        }
        $inputData = new ReceivedLabelInputData(
            $this->request->user()->hospitalId,
            $vars['receivedId']
        );
        $inputPort->handle($inputData);
    }
}
