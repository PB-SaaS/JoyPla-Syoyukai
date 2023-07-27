<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use framework\SpiralConnecter\SpiralDB;
use JoyPla\Enterprise\Models\Acceptance;
use JoyPla\Enterprise\Models\AcceptanceId;
use JoyPla\Enterprise\Models\AcceptanceItem;
use JoyPla\Enterprise\Models\AcceptanceItemId;
use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\DateYearMonthDay;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\LotDate;
use JoyPla\Enterprise\Models\LotNumber;
use JoyPla\Enterprise\Models\Price;
use JoyPla\Enterprise\Models\UnitPrice;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use Model;
use stdClass;

class AcceptanceRepository implements AcceptanceRepositoryInterface
{
    public function register(Acceptance $acceptance)
    {
        $acceptance = $acceptance->toArray();
        ModelRepository::getAcceptanceInstance()->create([
            'registTime' => 'now',
            'acceptanceId' => $acceptance['acceptanceId'],
            'hospitalId' => $acceptance['hospitalId'],
            'sourceDivisionId' => $acceptance['sourceDivisionId'],
            'targetDivisionId' => $acceptance['targetDivisionId'],
            'acceptanceDate' => $acceptance['acceptanceDate'],
            'isComplete' => $acceptance['isComplete'],
        ]);
    }

    public function findByAcceptanceId(HospitalId $hospitalId, AcceptanceId $acceptanceId) {

        $payoutUnitPrice = ModelRepository::getHospitalInstance()->where('hospitalId', $hospitalId->value())->resetValue([
            'payoutUnitPrice'
        ])->get()->first();

        $payoutUnitPrice = $payoutUnitPrice->payoutUnitPrice;

        $items = ModelRepository::getAcceptanceItemInstance()->where('acceptanceId', $acceptanceId->value())->get();

        $history = ModelRepository::getAcceptanceInstance()->where(
            'hospitalId',
            $hospitalId->value()
        )->where('acceptanceId', $acceptanceId->value())->get()->first();

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
            $item->acceptancePrice = (float)$item->acceptanceCount * $unitPrice;

            $history->acceptancePrice += $item->acceptancePrice;
            $history->payoutTotalCount += (float)$item->payoutCount;
            $history->acceptanceTotalCount += (float)$item->acceptanceCount;
        }

        return $history;
    }

    public function saveToArray(array $acceptances)
    {
        $acceptances = array_map(function (Acceptance $acceptance) {
            return $acceptance;
        }, $acceptances);

        $itemInstance = ModelRepository::getAcceptanceItemInstance();

        $upsertHistory = [];
        $itemUpsert = [];

        foreach ($acceptances as $acceptance) {
            $acceptance = $acceptance->toArray();
            $upsertHistory[] = [
                'updateTime' => 'now',
                'acceptanceId' => (string)$acceptance['acceptanceId'],
                'hospitalId' => (string)$acceptance['hospitalId'],
                'sourceDivisionId' => (string)$acceptance['sourceDivisionId'],
                'targetDivisionId' => (string)$acceptance['targetDivisionId'],
                'acceptanceDate' => (string)$acceptance['acceptanceDate'],
                'isComplete' => (string)($acceptance['isComplete'])? 't' : 'f',
            ];

            $itemInstance->orWhere('acceptanceId', $acceptance['acceptanceId'], '=');
            foreach ($acceptance['items'] as $item) {
                
                $itemUpsert[] = [
                    'updateTime' => 'now',
                    'acceptanceId' => (string)$item['acceptanceId'],
                    'acceptanceItemId' => (string)$item['acceptanceItemId'],
                    'inHospitalItemId' =>(string)$item['inHospitalItemId'],
                    'lotDate' =>(string)$item['lotDate'],
                    'lotNumber' => (string)$item['lotNumber'],
                    'quantity' =>(string)$item['quantity'],
                    'quantityUnit' =>(string)$item['quantityUnit'],
                    'itemUnit' => (string)$item['itemUnit'],
                    'price' => (string)$item['price'],
                    'unitPrice' =>(string)$item['unitPrice'],
                    'acceptanceCount' =>(string)$item['acceptanceCount'],
                    'payoutCount' => (string)$item['payoutCount'],
                ];
                $itemInstance->where(
                    'acceptanceItemId',
                    $item['acceptanceItemId'],
                    '!='
                );
            }
        }
        ModelRepository::getAcceptanceInstance()->upsertBulk(
            'acceptanceId',
            $upsertHistory
        );
        $itemInstance->delete();
        if (!empty($itemUpsert)) {
            ModelRepository::getAcceptanceItemInstance()->upsertBulk(
                'acceptanceItemId',
                $itemUpsert
            );
        }
    }

    public function find(HospitalId $hospitalId, AcceptanceId $acceptanceId)
    {
        $acceptance = ModelRepository::getAcceptanceInstance()->where(
            'hospitalId',
            $hospitalId->value(),
        )->where(
            'acceptanceId',
            $acceptanceId->value()
        )->get()->first();
        
        $items = ModelRepository::getAcceptanceItemInstance()->where(
            'acceptanceId',
            $acceptanceId->value()
        )->get();

        if(empty($acceptance))
        {
            return null;
        }

        $acceptance = new Acceptance(
            $acceptanceId,
            new DateYearMonthDay($acceptance->acceptanceDate),
            $hospitalId,
            new DivisionId($acceptance->sourceDivisionId),
            new DivisionId($acceptance->targetDivisionId),
        );

        foreach($items as $item){
            $acceptance->addItem(new AcceptanceItem(
                $acceptanceId,
                new AcceptanceItemId($item->acceptanceItemId),
                new InHospitalItemId($item->inHospitalItemId),
                new LotDate( $item->lotDate),
                new LotNumber($item->lotNumber),
                (int)$item->quantity,
                $item->quantityUnit,
                $item->itemUnit,
                new Price($item->price),
                new UnitPrice($item->unitPrice),
                $item->acceptanceCount,
                $item->payoutCount
            ));
        }

        return $acceptance ;
    }

    public function search(HospitalId $hospitalId , stdClass $search)
    {
        $payoutUnitPrice = ModelRepository::getHospitalInstance()->where('hospitalId', $hospitalId->value())->resetValue([
            'payoutUnitPrice'
        ])->get()->first();

        $payoutUnitPrice = $payoutUnitPrice->payoutUnitPrice;

        $itemViewInstance = ModelRepository::getAcceptanceItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        $itemViewInstance->orWhere('isComplete', '0', '=');
        $itemViewInstance->orWhere('isComplete', '0', 'ISNULL');

        $historyViewInstance = ModelRepository::getAcceptanceInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        if ($search->itemName) {
            $itemViewInstance->orWhere(
                'itemName',
                '%' . $search->itemName . '%',
                'LIKE'
            );
        }
        if ($search->makerName) {
            $itemViewInstance->orWhere(
                'makerName',
                '%' . $search->makerName . '%',
                'LIKE'
            );
        }
        if ($search->itemCode) {
            $itemViewInstance->orWhere(
                'itemCode',
                '%' . $search->itemCode . '%',
                'LIKE'
            );
        }
        if ($search->itemStandard) {
            $itemViewInstance->orWhere(
                'itemStandard',
                '%' . $search->itemStandard . '%',
                'LIKE'
            );
        }
        if ($search->itemJANCode) {
            $itemViewInstance->orWhere(
                'itemJANCode',
                '%' . $search->itemJANCode . '%',
                'LIKE'
            );
        }

        if (is_array($search->sourceDivisionIds) && count($search->sourceDivisionIds) > 0) {
            foreach ($search->sourceDivisionIds as $divisionId) {
                $itemViewInstance->orWhere('sourceDivisionId', $divisionId);
            }
        }

        if (is_array($search->targetDivisionIds) && count($search->targetDivisionIds) > 0) {
            foreach ($search->targetDivisionIds as $divisionId) {
                $itemViewInstance->orWhere('targetDivisionId', $divisionId);
            }
        }

        if ($search->yearMonth) {
            $yearMonth = new DateYearMonth($search->yearMonth);
            $nextMonth = $yearMonth->nextMonth();
            $itemViewInstance->where(
                'acceptanceDate',
                $yearMonth->format('Y-m-01'),
                '>='
            );
            $itemViewInstance->where(
                'acceptanceDate',
                $nextMonth->format('Y-m-01'),
                '<'
            );
        }

        $receivingNumbers = [];
        $items = $itemViewInstance->get();

        if ($items->count() == 0) {
            return [[], 0];
        }

        foreach ($items->all() as $item) {
            $historyViewInstance = $historyViewInstance->orWhere(
                'acceptanceId',
                $item->acceptanceId
            );
        }

        $historys = $historyViewInstance
            ->orderBy('id', 'desc')
            ->page($search->currentPage)
            ->paginate($search->perPage);

        if (count($historys->getData()->all()) == 0) {
            return [[], 0];
        }

        $viewItems = $itemViewInstance->get();
        $acceptances = [];

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
                if($item->acceptanceId == $history->acceptanceId){
                    $unitPrice = 0;
                    if(! empty($item->price) && ! empty($item->quantity)){
                        $unitPrice = ($payoutUnitPrice == '1')? (float)$item->unitPrice : (float)$item->price / (float)$item->quantity;
                    }
                    $item->acceptancePrice = (float)$item->acceptanceCount * $unitPrice;
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

            $acceptances[] = $history;
        }

        foreach($acceptances as &$acceptance)
        {
            foreach($acceptance->_items as $item)
            {
                $acceptance->acceptancePrice += $item->acceptancePrice;
                $acceptance->payoutTotalCount += (float)$item->payoutCount;
                $acceptance->acceptanceTotalCount += (float)$item->acceptanceCount;
            }
        }

        return [$acceptances, $historys->getTotal()];
    }
    
    public function delete(AcceptanceId $acceptanceId)
    {
        ModelRepository::getAcceptanceInstance()
            ->where('acceptanceId', $acceptanceId->value())
            ->delete();
    }

/*
    public function saveItemLog(array $logs)
    {
        $logs = array_map(function (AccountantItemChageLog $log) {
            return $log;
        }, $logs);

        $insert = [];
        foreach ($logs as $log) {
            $log = $log->toArray();
            $insert[] = [
                'accountantId' =>
                    (string) $log['accountantItem']['accountantId'],
                'itemId' => (string) $log['accountantItem']['itemId'],
                'itemName' => (string) $log['accountantItem']['itemName'],
                'makerName' => (string) $log['accountantItem']['makerName'],
                'itemCode' => (string) $log['accountantItem']['itemCode'],
                'itemStandard' =>
                    (string) $log['accountantItem']['itemStandard'],
                'itemJANCode' => (string) $log['accountantItem']['itemJANCode'],
                'count' => (string) $log['accountantItem']['count'],
                'unit' => (string) $log['accountantItem']['unit'],
                'price' => (string) $log['accountantItem']['price'],
                'taxrate' => (string) $log['accountantItem']['taxrate'],
                'accountantItemId' =>
                    (string) $log['accountantItem']['accountantItemId'],
                'action' => (string) $log['accountantItem']['action'],
                'method' => (string) $log['accountantItem']['method'],
                'index' => (string) $log['accountantItem']['index'],
                'kinds' => (string) $log['kinds'],
                'userId' => (string) $log['userId'],
            ];
        }
        if (!empty($insert)) {
            ModelRepository::getAccountantItemLogInstance()->insert($insert);
        }
    }
    */
}

interface AcceptanceRepositoryInterface
{
}
