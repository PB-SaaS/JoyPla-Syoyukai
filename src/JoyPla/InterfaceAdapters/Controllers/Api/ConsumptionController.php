<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use ApiResponse;
use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionDeleteInputData;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionDeleteInputPortInterface;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionRegisterInputData;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionRegisterInputPortInterface;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionIndexInputData;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionIndexInputPortInterface;
use JoyPla\Enterprise\Models\ConsumptionId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InventoryCalculation;
use JoyPla\Service\Repository\RepositoryProvider;

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
        $consumptionType = $this->request->get('consumptionType' , '1');
        $user = $this->request->user();

        $inputData = new ConsumptionRegisterInputData(
            $user,
            $consumptionDate,
            (int)$consumptionType,
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

    public function deleteItem($vars){
        
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('cancellation_of_consumption_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('cancellation_of_consumption_slips');

        $deleteItemId = $this->request->get('deleteItemId', '');

        $consumptionId = $vars['consumptionId'];
        $hospitalId = new HospitalId($this->request->user()->hospitalId);

        $repository = new RepositoryProvider();
        $consumption = $repository->getConsumptionRepository()
            ->find(
                $hospitalId ,
                new ConsumptionId($consumptionId)
            );

        if($gate->isOnlyMyDivision() && $consumption->getDivision()->getDivisionId()->value() === $this->request->user()->divisionId){
            Router::abort(403);
        }
        $items = [];
        $inventoryCalculations = [];

        foreach( $consumption->getConsumptionItems() as $item)
        {
            if($deleteItemId != $item->getId())
            {
                $items[] = $item;
            } else {
                $inventoryCalculations[] = new InventoryCalculation(
                    $item->getHospitalId(),
                    $item->getDivision()->getDivisionId(),
                    $item->getInHospitalItemId(),
                    0,
                    1,
                    $item->getLot(),
                    $item->getConsumptionQuantity() //消費の取り消しなので増やす
                );
            }
        }

        $consumption = $consumption->setConsumptionItems($items);
        
        if(count( $inventoryCalculations) > 0){
            $repository
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);
        }
        if(count($consumption->getConsumptionItems()) === 0 ){
            $repository->getConsumptionRepository()
                ->delete($hospitalId , $consumption->getConsumptionId());
            echo (new ApiResponse([$consumption->getConsumptionId()->value()] , 1 , 201 , 'slipDeleted', []))->toJson();
        } else {
            $repository->getConsumptionRepository()
                ->saveToArray([$consumption]);
            echo (new ApiResponse([$consumption->getConsumptionId()->value()] , 1 , 200 , 'success', []))->toJson();
        }
    }
}
