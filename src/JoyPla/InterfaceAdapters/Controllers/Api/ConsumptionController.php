<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use Exception;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionDeleteInputData;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionDeleteInputPortInterface;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionRegisterInputData;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionRegisterInputPortInterface;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionIndexInputData;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionIndexInputPortInterface;

class ConsumptionController extends Controller
{
    public function index($vars, ConsumptionIndexInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('list_of_consumption_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_consumption_slips');

        $search = $this->request->get('search');

        if ($gate->isOnlyMyDivision()) {
            $search['divisionIds'] = [$this->request->user()->divisionId];
        }

        $inputData = new ConsumptionIndexInputData(
            $this->request->user()->hospitalId,
            $search
        );
        $inputPort->handle($inputData);
    }

    public function show($vars)
    {
    }

    public function register(
        $vars,
        ConsumptionRegisterInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('register_of_consumption_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_of_consumption_slips');

        $consumptionItems = $this->request->get('consumptionItems');
        $consumptionDate = $this->request->get('consumptionDate');

        $user = $this->request->user();

        $inputData = new ConsumptionRegisterInputData(
            $user,
            $consumptionDate,
            $consumptionItems,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function update($vars)
    {
    }

    public function delete(
        $vars,
        ConsumptionDeleteInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('cancellation_of_consumption_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('cancellation_of_consumption_slips');

        $inputData = new ConsumptionDeleteInputData(
            $this->request->user(),
            $vars['consumptionId'],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }
}
