<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use ApiResponse;
use App\Model\InHospitalItem;
use Csrf;
use Exception;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\Acceptance\AcceptanceRegisterInputData;
use JoyPla\Application\InputPorts\Api\Acceptance\AcceptanceRegisterInputPortInterface;
use JoyPla\Enterprise\Models\AcceptanceId;
use JoyPla\Enterprise\Models\AcceptanceItem;
use JoyPla\Enterprise\Models\CardId;
use JoyPla\Enterprise\Models\DateYearMonthDay;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\InventoryCalculation;
use JoyPla\Enterprise\Models\Lot;
use JoyPla\Enterprise\Models\Payout;
use JoyPla\Enterprise\Models\PayoutHistoryId;
use JoyPla\Enterprise\Models\PayoutItem;
use JoyPla\Enterprise\Models\PayoutItemId;
use JoyPla\Enterprise\Models\PayoutQuantity;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use JoyPla\Service\Repository\RepositoryProvider;
use stdClass;

class AcceptanceController extends Controller
{
    
    public function register($vars, AcceptanceRegisterInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('register_of_payouts')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_of_payouts');

        //$isOnlyPayout = ( $this->request->get('isOnlyPayout') === 'true' );

        $acceptanceItems = $this->request->get('acceptanceItems');

        $user = $this->request->user();

        $inputData = new AcceptanceRegisterInputData(
            $user,
            $acceptanceItems,
            $gate->isOnlyMyDivision(),
            $this->request->get('isOnlyAcceptance') === 'true'
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
        [ $acceptance , $totalCount ] = $repositoryProvider
            ->getAcceptanceRepository()
            ->search(
                new HospitalId($this->request->user()->hospitalId),
                $search
            );

        echo (new ApiResponse($acceptance, $totalCount, 200, 'acceptance', []))->toJson();
    }

    public function show($vars){
        $acceptanceId = new AcceptanceId($vars['acceptanceId']);
        $hospitalId = new HospitalId($this->request->user()->hospitalId);
        
        $repositoryProvider = new RepositoryProvider();
        $acceptance = $repositoryProvider
            ->getAcceptanceRepository()
            ->findByAcceptanceId(
                $hospitalId,
                $acceptanceId
            );
            
        echo (new ApiResponse($acceptance, 1, 200, 'acceptance', []))->toJson();
    }

    public function update($vars){
        if(gate('is_approver')){
            Router::abort(403);
        }
        $hospitalId = new HospitalId($this->request->user()->hospitalId);
        $acceptanceId = new AcceptanceId($vars['acceptanceId']);
        $updateItems = $this->request->get( 'updateItems' , []);

        $repositoryProvider = new RepositoryProvider();
        $acceptance = $repositoryProvider
            ->getAcceptanceRepository()
            ->find(
                $hospitalId,
                $acceptanceId
            );

        if((gate('is_user') && $this->request->user()->divisionId !== $acceptance->getSourceDivisionId()->value())){
            throw new Exception("you don't have execute permission" , 500);
        }
        $items = [];
        $inventoryCalculations = [];

        foreach($acceptance->getItems() as $item){
            foreach($updateItems as $requestItem){
                if($item->getAcceptanceItemId()->value() == $requestItem['acceptanceItemId']){
                    $inventoryCalculations[] = new InventoryCalculation(
                        $acceptance->getHospitalId(),
                        $acceptance->getSourceDivisionId(),
                        $item->getInHospitalItemId(),
                        0,
                        4,
                        new Lot(
                            $item->getLotNumber(),
                            $item->getLotDate()
                        ),
                        $item->getAcceptanceQuantity() - (int)$requestItem['acceptanceCount'] //減少した分戻す
                    );
                    $item = $item->changeAcceptanceCount((int)$requestItem['acceptanceCount']);
                }
            }
            $items[] = $item;
        }

        $acceptance = $acceptance->setItems($items);

        $repositoryProvider->getAcceptanceRepository()->saveToArray([$acceptance]);

        if(count($inventoryCalculations) > 0){
            $repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);
        }
        echo (new ApiResponse($acceptance->toArray(), 1, 200, 'acceptance', []))->toJson();
    }

    public function payoutRegister($vars){
        if(gate('is_approver')){
            Router::abort(403);
        }

        $hospitalId = new HospitalId($this->request->user()->hospitalId);
        $acceptanceId = new AcceptanceId($vars['acceptanceId']);

        $payoutItems = $this->request->get( 'payoutItems' , []);
        $payoutDate = $this->request->get( 'payoutDate' , 'now');
        
        $repositoryProvider = new RepositoryProvider();
        $acceptance = $repositoryProvider
            ->getAcceptanceRepository()
            ->find(
                $hospitalId,
                $acceptanceId
            );

        if((gate('is_user') && $this->request->user()->divisionId !== $acceptance->getTargetDivisionId()->value())){
            throw new Exception("you don't have execute permission" , 500);
        }

        $items = [];
        $inventoryCalculations = [];
        $cardIds = [];

        $divisions = ModelRepository::getDivisionInstance()->where('hospitalId', $hospitalId->value())
        ->orWhere('divisionId', $acceptance->getSourceDivisionId()->value())
        ->orWhere('divisionId', $acceptance->getTargetDivisionId()->value())->get();

        $sourceDivision = array_find($divisions, function($division) use ($acceptance){
            return $division->divisionId === $acceptance->getSourceDivisionId()->value();
        });
        
        $targetDivision = array_find($divisions, function($division) use ($acceptance){
            return $division->divisionId === $acceptance->getTargetDivisionId()->value();
        });

        $payout = new Payout(
            new DateYearMonthDay($payoutDate),
            PayoutHistoryId::generate(),
            $acceptance->getHospitalId(),
            $acceptance->getSourceDivisionId(),
            ($sourceDivision) ? $sourceDivision->divisionName : '',
            $acceptance->getTargetDivisionId(),
            ($targetDivision) ? $targetDivision->divisionName : '',
        );

        $inHospitalItemIds = array_map(function (AcceptanceItem $item) {
            return $item->getInHospitalItemId();
        }, $acceptance->getItems());

        $inHospitalItems = $repositoryProvider
            ->getInHospitalItemRepository()
            ->getByInHospitalItemIds($hospitalId, $inHospitalItemIds);

        if (count($inHospitalItems) === 0) {
            throw new Exception("payout items don't exist.", 999);
        }

        $items = $acceptance->getItems();

        foreach($items as $key => $item){
            foreach($payoutItems as $requestItem){
                if($item->getAcceptanceItemId()->value() == $requestItem['acceptanceItemId']){
                    $inventoryCalculations[] = new InventoryCalculation(
                        $acceptance->getHospitalId(),
                        $acceptance->getTargetDivisionId(),
                        $item->getInHospitalItemId(),
                        0,
                        4,
                        new Lot(
                            $item->getLotNumber(),
                            $item->getLotDate()
                        ),
                        (int)$requestItem['payoutCount']
                    );
                    $item = $item->addPayoutCount((int)$requestItem['payoutCount']);
                    if($requestItem['cardId'] != ''){
                        $cardIds[] = new CardId($requestItem['cardId']);
                    }

                    $inHospitalItem = array_find($inHospitalItems, function (
                        $value
                    ) use ($item) {
                        return $value->getInHospitalItemId()->value() ===
                            $item->getInHospitalItemId()->value();
                    });
    

                    $payout->addPayoutItem(new PayoutItem(
                        $payout->getPayoutHistoryId(),
                        new PayoutItemId(''),
                        $item->getInHospitalItemId(),
                        $inHospitalItem->getItem()->getItemId(),
                        $hospitalId,
                        $item->getQuantity(),
                        $item->getQuantityUnit(),
                        $item->getItemUnit(),
                        $item->getPrice(),
                        $item->getUnitPrice(),
                        new PayoutQuantity((int)$requestItem['payoutCount']),
                        $item->getLotDate(),
                        $item->getLotNumber(),
                        $inHospitalItem->isLotManagement(),
                        new CardId($requestItem['cardId'])
                    ), false);
                }
            }
            $items[$key] = $item;
        }

        if($this->request->get('isAll') === 'true'){
            foreach($items as $key => $item){
                $count = $item->getAcceptanceQuantity() - $item->getPayoutQuantity();
                if($count > 0)
                {
                    $inventoryCalculations[] = new InventoryCalculation(
                        $acceptance->getHospitalId(),
                        $acceptance->getTargetDivisionId(),
                        $item->getInHospitalItemId(),
                        0,
                        4,
                        new Lot(
                            $item->getLotNumber(),
                            $item->getLotDate()
                        ),
                        (int)$count
                    );

                    
                    $inHospitalItem = array_find($inHospitalItems, function (
                        $value
                    ) use ($item) {
                        return $value->getInHospitalItemId()->value() ===
                            $item->getInHospitalItemId()->value();
                    });
    
                    $payout->addPayoutItem(new PayoutItem(
                        $payout->getPayoutHistoryId(),
                        new PayoutItemId(''),
                        $item->getInHospitalItemId(),
                        $inHospitalItem->getItem()->getItemId(),
                        $hospitalId,
                        $item->getQuantity(),
                        $item->getQuantityUnit(),
                        $item->getItemUnit(),
                        $item->getPrice(),
                        $item->getUnitPrice(),
                        new PayoutQuantity($count),
                        $item->getLotDate(),
                        $item->getLotNumber(),
                        $inHospitalItem->isLotManagement(),
                        new CardId('')
                    ), false);
                    $item = $item->addPayoutCount($count);
                }
            }
            $items[$key] = $item;
        }

        $acceptance = $acceptance->setItems($items);

        $updateCards = [];
        if (!empty($cardIds)) {
            $cards = $repositoryProvider
                ->getCardRepository()
                ->getCards($hospitalId, $cardIds);

            foreach ($acceptance->getItems() as $item) {
                $card = array_find($cards, function ($card) use ($item) {
                    return $card->getCardId()->value() === $item->card;
                });

                if (!$card) {
                    throw new Exception("card don't exist.", 998);
                }

                $updateCards[] = $card->setLot(
                    new Lot(
                        $item->getLotNumber(),
                        $item->getLotDate()
                    )
                );
            }
        }

        $repositoryProvider->getAcceptanceRepository()->saveToArray([$acceptance]);

        if(count($inventoryCalculations) > 0){
            $repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);
        }

        if(count($payout->getItems()) > 0 ){
            $repositoryProvider->getPayoutRepository()->saveToArray([$payout]);
        }
        if (!empty($updateCards)) {
            $repositoryProvider
                ->getCardRepository()
                ->update($hospitalId, $updateCards);
        }

        echo (new ApiResponse($acceptance->toArray(), 1, 200, 'acceptance', []))->toJson();
    }

    public function delete($vars){
        if(gate('is_approver')){
            Router::abort(403);
        }
        $hospitalId = new HospitalId($this->request->user()->hospitalId);
        $acceptanceId = new AcceptanceId($vars['acceptanceId']);

        $repositoryProvider = new RepositoryProvider();
        $acceptance = $repositoryProvider
            ->getAcceptanceRepository()
            ->find(
                $hospitalId,
                $acceptanceId
            );
        if(empty($acceptance))
        {
            throw new Exception("acceptance not found.", 998);
        }

        if($acceptance->status() !== 1){
            throw new Exception("acceptance don't delete", 998);
        }
        $inventoryCalculations = [];
        foreach($acceptance->getItems() as $item){
            $inventoryCalculations[] = new InventoryCalculation(
                $acceptance->getHospitalId(),
                $acceptance->getSourceDivisionId(),
                $item->getInHospitalItemId(),
                0,
                4,
                new Lot(
                    $item->getLotNumber(),
                    $item->getLotDate()
                ),
                (int)$item->getAcceptanceQuantity()
            );
        }

        if(count($inventoryCalculations) > 0){
            $repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);
        }

        $repositoryProvider
            ->getAcceptanceRepository()
            ->delete(
                $acceptanceId
            );

        echo (new ApiResponse($acceptance->toArray(), 1, 200, 'acceptance', []))->toJson();
    }
}
