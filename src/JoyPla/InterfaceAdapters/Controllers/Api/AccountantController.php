<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantIndexInputData;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantIndexInputPortInterface;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantRegisterInputData;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantRegisterInputPortInterface;

class AccountantController extends Controller
{
    public function register(
        $vars,
        AccountantRegisterInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $gate = Gate::getGateInstance('register_of_accountant');

        $inputData = new AccountantRegisterInputData(
            $this->request->user(),
            [
                'hospitalId' => $this->request->user()->hospitalId,
                'divisionId' => $this->request->get('divisionId'),
                'distributorId' => $this->request->get('distributorId'),
                'accountantDate' => $this->request->get('accountantDate'),
                'orderId' => $this->request->get('orderId'),
                'receivedId' => $this->request->get('receivedId'),
            ],
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function index($vars, AccountantIndexInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $gate = Gate::getGateInstance('list_of_accountant_slips');

        $search = $this->request->get('search');

        if ($gate->isOnlyMyDivision()) {
            $search['divisionIds'] = [$this->request->user()->divisionId];
        }

        $inputData = new AccountantIndexInputData(
            $this->request->user(),
            $search
        );
        $inputPort->handle($inputData);
    }
}
