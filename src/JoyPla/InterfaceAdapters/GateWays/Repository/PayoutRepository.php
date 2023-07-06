<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\CardId;
use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\DateYearMonthDay;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\ItemId;
use JoyPla\Enterprise\Models\LotDate;
use JoyPla\Enterprise\Models\LotNumber;
use JoyPla\Enterprise\Models\Payout;
use JoyPla\Enterprise\Models\PayoutHistoryId;
use JoyPla\Enterprise\Models\PayoutItem;
use JoyPla\Enterprise\Models\PayoutItemId;
use JoyPla\Enterprise\Models\PayoutQuantity;
use JoyPla\Enterprise\Models\Price;
use JoyPla\Enterprise\Models\UnitPrice;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use stdClass;

class PayoutRepository implements PayoutRepositoryInterface
{
    public function saveToArray(array $payouts)
    {
        $payouts = array_map(function (Payout $payout) {
            return $payout;
        }, $payouts);

        $histories = [];
        $insertItems = [];
        $updateItems = [];

        $undeleteItems = [];
        $payoutIds = [];
        foreach ($payouts as $payout) {
            $payoutToArray = $payout->toArray();
            $payoutIds[] = $payoutToArray['payoutHistoryId'];
            $histories[] = [
                'updateTime'=> 'now',
                'payoutDate' =>  (string) $payoutToArray['payoutDate'],
                'payoutHistoryId' => (string) $payoutToArray['payoutHistoryId'],
                'hospitalId' =>
                    (string) $payoutToArray['hospitalId'],
                'sourceDivisionId' =>
                    (string) $payoutToArray['sourceDivisionId'],
                'sourceDivision' =>
                    (string) $payoutToArray['sourceDivisionName'],
                'targetDivisionId' =>
                    (string) $payoutToArray['targetDivisionId'],
                'targetDivision' =>
                    (string) $payoutToArray['targetDivisionName'],
                'itemsNumber' => (string) $payoutToArray['itemCount'],
                'totalAmount' => (string) $payoutToArray['totalAmount'],
            ];

            foreach ($payoutToArray['payoutItems'] as $payoutItem) {
                if($payoutItem['payoutItemId'] == ''){
                    $insertItems[] = [
                        'registrationTime' => (string) $payoutToArray['payoutDate'],
                        'payoutHistoryId' => (string) $payoutToArray['payoutHistoryId'],
                        'hospitalId' => (string) $payoutToArray['hospitalId'],
                        'itemId' => (string) $payoutItem['itemId'],
                        'inHospitalItemId' =>
                            (string) $payoutItem['inHospitalItemId'],
                        'sourceDivisionId' =>
                            (string) $payoutToArray['sourceDivisionId'],
                        'targetDivisionId' =>
                            (string) $payoutToArray['targetDivisionId'],
                        'payoutQuantity' => (string) $payoutItem['payoutQuantity'],
                        'quantity' =>
                            (string) $payoutItem['quantityNum'],
                        'quantityUnit' =>
                            (string) $payoutItem['quantityUnit'],
                        'itemUnit' => (string) $payoutItem['itemUnit'],
                        'price' => (string) $payoutItem['price'],
                        'unitPrice' => (string) $payoutItem['unitPrice'],
                        'lotNumber' => (string) $payoutItem['lotNumber'],
                        'lotDate' => (string) $payoutItem['lotDate'],
                        'cardId' => (string) $payoutItem['cardId'],
                        'payoutType' => (string) $payoutItem['payoutType'],
                        'payoutAmount' => (string) $payoutItem['payoutAmount'],
                    ];
                } else {
                    $undeleteItems[] = $payoutItem['payoutItemId'];
                    $updateItems[] = [
                        'updateTime' => 'now',
                        'payoutHistoryId' => (string) $payoutToArray['payoutHistoryId'],
                        'payoutId' => (string) $payoutItem['payoutItemId'],
                        'hospitalId' => (string) $payoutItem['hospitalId'],
                        'itemId' => (string) $payoutItem['itemId'],
                        'inHospitalItemId' =>
                            (string) $payoutItem['inHospitalItemId'],
                        'sourceDivisionId' =>
                            (string) $payoutToArray['sourceDivisionId'],
                        'targetDivisionId' =>
                            (string) $payoutToArray['targetDivisionId'],
                        'payoutQuantity' => (string) $payoutItem['payoutQuantity'],
                        'quantity' =>
                            (string) $payoutItem['quantityNum'],
                        'quantityUnit' =>
                            (string) $payoutItem['quantityUnit'],
                        'itemUnit' => (string) $payoutItem['itemUnit'],
                        'price' => (string) $payoutItem['price'],
                        'unitPrice' => (string) $payoutItem['unitPrice'],
                        'lotNumber' => (string) $payoutItem['lotNumber'],
                        'lotDate' => (string) $payoutItem['lotDate'],
                        'cardId' => (string) $payoutItem['cardId'],
                        'payoutType' =>  (string) $payoutItem['payoutType'],
                        'payoutAmount' => (string) $payoutItem['payoutAmount'],
                    ];
                }
            }
        }

        if(!$undeleteItems && !empty($payoutIds)){
            ModelRepository::getPayoutItemInstance()->whereIn('payoutHistoryId',$payoutIds)->orWhereNotIn('payoutId',$undeleteItems)->delete();
        }
        
        ModelRepository::getPayoutInstance()->upsertBulk('payoutHistoryId',$histories);
        if(!empty($updateItems)){
            ModelRepository::getPayoutItemInstance()->updateBulk('payoutId',$updateItems);
        }
        if(!empty($insertItems)){
            ModelRepository::getPayoutItemInstance()->insert($insertItems);
        }

        return $payouts;
    }

    public function search(HospitalId $hospitalId , stdClass $search)
    {

        $itemViewInstance = ModelRepository::getPayoutViewItemInstance()->where(
            'hospitalId',
            $hospitalId->value()
        )->resetValue('payoutHistoryId');

        $historyViewInstance = ModelRepository::getPayoutInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        $isSearch = false;

        if ($search->itemName) {
            $itemViewInstance->orWhere(
                'itemName',
                '%' . $search->itemName . '%',
                'LIKE'
            );
            $isSearch = true;
        }
        if ($search->makerName) {
            $itemViewInstance->orWhere(
                'makerName',
                '%' . $search->makerName . '%',
                'LIKE'
            );
            $isSearch = true;
        }
        if ($search->itemCode) {
            $itemViewInstance->orWhere(
                'itemCode',
                '%' . $search->itemCode . '%',
                'LIKE'
            );
            $isSearch = true;
        }
        if ($search->itemStandard) {
            $itemViewInstance->orWhere(
                'itemStandard',
                '%' . $search->itemStandard . '%',
                'LIKE'
            );
            $isSearch = true;
        }
        if ($search->itemJANCode) {
            $itemViewInstance->orWhere(
                'itemJANCode',
                '%' . $search->itemJANCode . '%',
                'LIKE'
            );
            $isSearch = true;
        }

        if (is_array($search->sourceDivisionIds) && count($search->sourceDivisionIds) > 0) {
            foreach ($search->sourceDivisionIds as $divisionId) {
                $itemViewInstance->orWhere('sourceDivisionId', $divisionId);
                $isSearch = true;
            }
        }

        if (is_array($search->targetDivisionIds) && count($search->targetDivisionIds) > 0) {
            foreach ($search->targetDivisionIds as $divisionId) {
                $itemViewInstance->orWhere('targetDivisionId', $divisionId);
                $isSearch = true;
            }
        }

        $receivingNumbers = [];
        if($isSearch){
            $items = $itemViewInstance->get();

            if($items->count() === 0){
                return [[], 0];
            }

            foreach ($items->all() as $item) {
                $historyViewInstance = $historyViewInstance->orWhere(
                    'payoutHistoryId',
                    $item->payoutHistoryId
                );
            }
        }

        if (is_array($search->sourceDivisionIds) && count($search->sourceDivisionIds) > 0) {
            foreach ($search->sourceDivisionIds as $divisionId) {
                $historyViewInstance->orWhere('sourceDivisionId', $divisionId);
            }
        }

        if (is_array($search->targetDivisionIds) && count($search->targetDivisionIds) > 0) {
            foreach ($search->targetDivisionIds as $divisionId) {
                $historyViewInstance->orWhere('targetDivisionId', $divisionId);
            }
        }

        if ($search->yearMonth) {
            $yearMonth = new DateYearMonth($search->yearMonth);
            $nextMonth = $yearMonth->nextMonth();
            $historyViewInstance->where(
                'payoutDate',
                $yearMonth->format('Y-m-01'),
                '>='
            );
            $historyViewInstance->where(
                'payoutDate',
                $nextMonth->format('Y-m-01'),
                '<'
            );
        }

        $historys = $historyViewInstance
            ->orderBy('id', 'desc')
            ->page($search->currentPage)
            ->paginate($search->perPage);

        if (count($historys->getData()->all()) == 0) {
            return [[], 0];
        }
        
        $itemViewInstance = ModelRepository::getPayoutViewItemInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        foreach($historys->getData()->all() as $history)
        {
            $itemViewInstance->orWhere('payoutHistoryId',$history->payoutHistoryId);
        }
        
        $viewItems = $itemViewInstance->get();
        $payouts = [];

        $divisionInstance = ModelRepository::getDivisionInstance();
        foreach($historys->getData()->all() as $key => $history)
        {
            $divisionInstance->orWhere('divisionId', $history->sourceDivisionId);
            $divisionInstance->orWhere('divisionId', $history->targetDivisionId);
        }

        $divisions = $divisionInstance->get();
        foreach($historys->getData()->all() as $key => $history)
        {
            $items = [];
            foreach($viewItems->all() as $item){
                if($item->payoutHistoryId == $history->payoutHistoryId){
                    $items[] = $item;
                }
            }
            $history->_items = $items;
            $history->_sourceDivision = array_find($divisions, function($division) use ($history){
                return $division->divisionId == $history->sourceDivisionId;
            });

            $history->_targetDivision = array_find($divisions, function($division) use ($history){
                return $division->divisionId == $history->targetDivisionId;
            });

            $payouts[] = $history;
        }

        return [$payouts, $historys->getTotal()];
    }

    
    public function findByPayoutHistoryId(HospitalId $hospitalId, PayoutHistoryId $payoutHistoryId) {

        $payoutUnitPrice = ModelRepository::getHospitalInstance()->where('hospitalId', $hospitalId->value())->resetValue([
            'payoutUnitPrice'
        ])->get()->first();

        $payoutUnitPrice = $payoutUnitPrice->payoutUnitPrice;

        $items = ModelRepository::getPayoutItemInstance()->where('payoutHistoryId', $payoutHistoryId->value())->get();

        $history = ModelRepository::getPayoutInstance()->where(
            'hospitalId',
            $hospitalId->value()
        )->where('payoutHistoryId', $payoutHistoryId->value())->get()->first();

        if(empty($history) && $items->count() === 0)
        {
            return null;
        }

        $inHospitalItem = ModelRepository::getInHospitalItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        foreach( $items as $item){
            $inHospitalItem->orWhere('inHospitalItemId' , $item->inHospitalItemId);
        }

        $divisionInstance = ModelRepository::getDivisionInstance();
        $divisionInstance->orWhere('divisionId', $history->sourceDivisionId);
        $divisionInstance->orWhere('divisionId', $history->targetDivisionId);

        
        $divisions = $divisionInstance->get();

        $history->_sourceDivision = array_find($divisions, function($division) use ($history){
            return $division->divisionId == $history->sourceDivisionId;
        });
        $history->_targetDivision = array_find($divisions, function($division) use ($history){
            return $division->divisionId == $history->targetDivisionId;
        });

        $inHospitalItem = $inHospitalItem->get();

        $history->_inHospitalItems = $inHospitalItem;
        $history->_items = $items;

        foreach($history->_items as $item)
        {
            $unitPrice = 0;
            if(! empty($item->price) && ! empty($item->quantity)){
                $unitPrice = ($payoutUnitPrice == '1')? (float)$item->unitPrice : (float)$item->price / (float)$item->quantity;
            }
            
            $item->payoutPrice = (float)$item->payoutQuantity * $unitPrice;
        }

        return $history;
    }

    public function find(HospitalId $hospitalId, PayoutHistoryId $payoutHistoryId)
    {
        $payout = ModelRepository::getPayoutInstance()->where(
            'hospitalId',
            $hospitalId->value(),
        )->where(
            'payoutHistoryId',
            $payoutHistoryId->value()
        )->get()->first();
        
        $items = ModelRepository::getPayoutItemInstance()->where(
            'payoutHistoryId',
            $payoutHistoryId->value()
        )->get();

        if(empty($payout))
        {
            return null;
        }

        $divisionInstance = ModelRepository::getDivisionInstance();
        $divisionInstance->orWhere('divisionId', $payout->sourceDivisionId);
        $divisionInstance->orWhere('divisionId', $payout->targetDivisionId);
        $divisions = $divisionInstance->get();

        $sourceDivision = array_find($divisions, function($division) use ($payout){
            return $division->divisionId == $payout->sourceDivisionId;
        });
        $targetDivision = array_find($divisions, function($division) use ($payout){
            return $division->divisionId == $payout->targetDivisionId;
        });

        $payout = new Payout(
            new DateYearMonthDay($payout->payoutDate),
            $payoutHistoryId,
            $hospitalId,
            new DivisionId($payout->sourceDivisionId),
            $sourceDivision->divisionName,
            new DivisionId($payout->targetDivisionId),
            $targetDivision->divisionName,
        );

        $inHospitalItem = ModelRepository::getInHospitalItemInstance();

        foreach($items as $item){
            $inHospitalItem->orWhere('inHospitalItemId', $item->inHospitalItemId);
        }

        $inHospitalItems = $inHospitalItem->get();

        $payoutItems = [];
        foreach($items as $item){
            $inHospitalItem = array_find($inHospitalItems, function($inHospitalItem) use ($item){
                return $inHospitalItem->inHospitalItemId === $item->inHospitalItemId ;
            });

            $payoutItems[] = new PayoutItem(
                $payoutHistoryId,
                new PayoutItemId($item->payoutId),
                new InHospitalItemId($item->inHospitalItemId),
                new ItemId($item->itemId),
                $hospitalId,
                (int)$item->quantity,
                $item->quantityUnit,
                $item->itemUnit,
                new Price($item->price),
                new UnitPrice($item->unitPrice),
                new PayoutQuantity($item->payoutQuantity),
                new LotDate( $item->lotDate),
                new LotNumber($item->lotNumber),
                ($inHospitalItem->lotManagement == '1') ? true : false,
                new CardId($item->cardId),
                (int)$item->payoutType
            );
        }

        return $payout->setPayoutItems($payoutItems) ;
    }

    public function delete(HospitalId $hospitalId , PayoutHistoryId $payoutHistoryId){
        ModelRepository::getPayoutInstance()->where(
            'hospitalId',
            $hospitalId->value(),
        )->where(
            'payoutHistoryId',
            $payoutHistoryId->value()
        )->delete();
    }
}

interface PayoutRepositoryInterface
{
    public function saveToArray(array $Payouts);
}