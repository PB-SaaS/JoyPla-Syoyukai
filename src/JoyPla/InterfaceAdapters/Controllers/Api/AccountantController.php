<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use ApiResponse;
use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantIndexInputData;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantIndexInputPortInterface;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantItemsIndexInputData;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantItemsIndexInputPortInterface;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantRegisterInputData;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantRegisterInputPortInterface;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantShowInputData;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantShowInputPortInterface;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantUpdateInputData;
use JoyPla\Application\InputPorts\Api\Accountant\AccountantUpdateInputPortInterface;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

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

    public function items(
        $vars,
        AccountantItemsIndexInputPortInterface $inputPort
    ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $gate = Gate::getGateInstance('list_of_accountant_slips');

        $search = $this->request->get('search', []);

        if ($gate->isOnlyMyDivision()) {
            $search['divisionIds'] = [$this->request->user()->divisionId];
        }

        $inputData = new AccountantItemsIndexInputData(
            $this->request->user(),
            $search
        );

        $inputPort->handle($inputData);
    }

    public function show($vars, AccountantShowInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $accountantId = $vars['accountantId'];

        $inputData = new AccountantShowInputData(
            $this->request->user(),
            $accountantId
        );

        $inputPort->handle($inputData);
    }

    public function update($vars, AccountantUpdateInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $accountantId = $vars['accountantId'];
        $accountant = $this->request->get('accountant');

        $inputData = new AccountantUpdateInputData(
            $this->request->user(),
            $accountantId,
            $accountant['items'] ?? []
        );

        $inputPort->handle($inputData);
    }

    public function delete($vars)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $accountantId = $vars['accountantId'];

        ModelRepository::getAccountantInstance()
            ->where('accountantId', $accountantId)
            ->where('hospitalId', $this->request->user()->hospitalId)
            ->delete();
        echo (new ApiResponse('', 1, 200, 'deleted', []))->toJson();
    }
}
