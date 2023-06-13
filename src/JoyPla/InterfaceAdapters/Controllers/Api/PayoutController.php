<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use ApiResponse;
use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\Payout\PayoutRegisterInputData;
use JoyPla\Application\InputPorts\Api\Payout\PayoutRegisterInputPortInterface;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InventoryCalculation;
use JoyPla\Enterprise\Models\Lot;
use JoyPla\Enterprise\Models\PayoutHistoryId;
use JoyPla\Enterprise\Models\PayoutQuantity;
use JoyPla\Service\Repository\RepositoryProvider;
use stdClass;

class PayoutController extends Controller
{
    public function register($vars, PayoutRegisterInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('register_of_payouts')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_of_payouts');
        $payoutType = (int)$this->request->get('payoutType' , 1);

        $isOnlyPayout = ( $this->request->get('isOnlyPayout') === 'true' );

        $payoutItems = $this->request->get('payoutItems');
        $payoutDate = $this->request->get('payoutDate' , 'now');

        $user = $this->request->user();

        $inputData = new PayoutRegisterInputData(
            $user,
            $payoutItems,
            $payoutDate,
            $gate->isOnlyMyDivision(),
            $isOnlyPayout,
            $payoutType
        );
        $inputPort->handle($inputData);
    }
    
    public function index()
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $searchRequest = $this->request->get('search');
        $search = new stdClass();
        $search->sortColumn = $searchRequest['sortColumn'] ?? 'id';
        $search->sortDirection = $searchRequest['sortDirection'] ?? 'desc';
        $search->itemName = $searchRequest['itemName'] ?? '';
        $search->makerName = $searchRequest['makerName'] ?? '';
        $search->itemCode = $searchRequest['itemCode'] ?? '';
        $search->itemStandard = $searchRequest['itemStandard'] ?? '';
        $search->itemJANCode = $searchRequest['itemJANCode'] ?? '';
        $search->yearMonth = $searchRequest['yearMonth'] ?? '';
        $search->sourceDivisionIds = $searchRequest['sourceDivisionIds'] ?? '';
        $search->targetDivisionIds = $searchRequest['targetDivisionIds'] ?? '';
        $search->perPage = $searchRequest['perPage'] ?? 1;
        $search->currentPage = $searchRequest['currentPage'] ?? 1;

        $repositoryProvider = new RepositoryProvider();
        [ $payouts , $totalCount ] = $repositoryProvider
            ->getPayoutRepository()
            ->search(
                new HospitalId($this->request->user()->hospitalId),
                $search
            );

        $result = [];
        foreach($payouts as $payout){
            if(
                gate('is_user') && 
                $this->request->user()->divisionId !== $payout->sourceDivisionId &&
                $this->request->user()->divisionId !== $payout->targetDivisionId 
                )
            {
                $payout->_items = [];
            }
            $result[] = $payout;
        }

        echo (new ApiResponse($result, $totalCount, 200, 'payouts', []))->toJson();
    }

    public function show($vars){
        $payoutHistoryId = new PayoutHistoryId($vars['payoutHistoryId']);
        $hospitalId = new HospitalId($this->request->user()->hospitalId);
        
        $repositoryProvider = new RepositoryProvider();
        $payout = $repositoryProvider
            ->getPayoutRepository()
            ->findByPayoutHistoryId(
                $hospitalId,
                $payoutHistoryId
            );
            
        $gate = Gate::getGateInstance('list_of_payout_slips');
        if(empty($payout) || ( $gate->isOnlyMyDivision() && 
        $payout->sourceDivisionId !== $this->request->user()->divisionId &&
        $payout->targetDivisionId !== $this->request->user()->divisionId )){
            Router::abort(403);
        }
        echo (new ApiResponse($payout, 1, 200, 'payout', []))->toJson();
    }

    public function update($vars)
    {
        if(gate('is_approver')){
            Router::abort(403);
        }
        $payoutHistoryId = new PayoutHistoryId($vars['payoutHistoryId']);
        $hospitalId = new HospitalId($this->request->user()->hospitalId);
        
        $repositoryProvider = new RepositoryProvider();
        $payout = $repositoryProvider
            ->getPayoutRepository()
            ->find(
                $hospitalId,
                $payoutHistoryId
            );

        $updateItems = $this->request->get('updateItems' , []);

        $gate = Gate::getGateInstance('list_of_payout_slips');

        if(empty($payout) || ( $gate->isOnlyMyDivision() && $payout->getSourceDivisionId()->value() !== $this->request->user()->divisionId)){
            Router::abort(403);
        }

        $items = [];
        $inventoryCalculations = [];
        foreach($payout->getItems() as $key => $item){
            $updateItem = array_find($updateItems , function($updateItem) use ($item){
                return $item->getPayoutItemId()->value() === $updateItem['payoutItemId'];
            });

            if($updateItem){
                $quantity = $item->getPayoutQuantity()->value() - (int)$updateItem['payoutQuantity'];

                $inventoryCalculations[] = new InventoryCalculation(
                    $item->getHospitalId(),
                    $payout->getSourceDivisionId(),
                    $item->getInHospitalItemId(),
                    0,
                    4,
                    new Lot(
                        $item->getLotNumber(),
                        $item->getLotDate()
                    ),
                    $quantity
                );
                $inventoryCalculations[] = new InventoryCalculation(
                    $item->getHospitalId(),
                    $payout->getTargetDivisionId(),
                    $item->getInHospitalItemId(),
                    0,
                    5,
                    new Lot(
                        $item->getLotNumber(),
                        $item->getLotDate()
                    ),
                    $quantity * -1
                );
                if((int) $updateItem['payoutQuantity'] != 0){
                    $item = $item->setPayoutQuantity(new PayoutQuantity($updateItem['payoutQuantity']));
                    $items[] = $item;
                }
            } else {
                $items[] = $item;
            }
        }

        if(empty($items)){
            
            $repositoryProvider
                ->getPayoutRepository()
                ->delete($hospitalId , $payoutHistoryId);

            if(!empty($inventoryCalculations)){
                $repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);
            }
    
            echo (new ApiResponse($payout, 1, 201, 'payout', []))->toJson();
        } else {
            
            $payout = $payout->setPayoutItems($items);
            $repositoryProvider
                ->getPayoutRepository()
                ->saveToArray([$payout]);

            if(!empty($inventoryCalculations)){
                $repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);
            }
    
            echo (new ApiResponse($payout, 1, 200, 'payout', []))->toJson();
         }
    }

    public function delete($vars)
    {
        if(gate('is_approver')){
            Router::abort(403);
        }
        $payoutHistoryId = new PayoutHistoryId($vars['payoutHistoryId']);
        $hospitalId = new HospitalId($this->request->user()->hospitalId);
        
        $repositoryProvider = new RepositoryProvider();
        $payout = $repositoryProvider
            ->getPayoutRepository()
            ->find(
                $hospitalId,
                $payoutHistoryId
            );
            
        $gate = Gate::getGateInstance('list_of_payout_slips');
        if(empty($payout) || ( $gate->isOnlyMyDivision() && $payout->getSourceDivisionId()->value() !== $this->request->user()->divisionId)){
            Router::abort(403);
        }

        $items = [];
        $inventoryCalculations = [];
        foreach($payout->getItems() as $key => $item){
            $inventoryCalculations[] = new InventoryCalculation(
                $item->getHospitalId(),
                $payout->getSourceDivisionId(),
                $item->getInHospitalItemId(),
                0,
                4,
                new Lot(
                    $item->getLotNumber(),
                    $item->getLotDate()
                ),
                $item->getPayoutQuantity()->value() 
            );
            $inventoryCalculations[] = new InventoryCalculation(
                $item->getHospitalId(),
                $payout->getTargetDivisionId(),
                $item->getInHospitalItemId(),
                0,
                5,
                new Lot(
                    $item->getLotNumber(),
                    $item->getLotDate()
                ),
                $item->getPayoutQuantity()->value()* -1
            );
            
        }

        $repositoryProvider
            ->getPayoutRepository()
            ->delete($hospitalId , $payoutHistoryId);

        if(!empty($inventoryCalculations)){
            $repositoryProvider
            ->getInventoryCalculationRepository()
            ->saveToArray($inventoryCalculations);
        }

        echo (new ApiResponse($payout, 1, 200, 'payout', []))->toJson();
    }
}
