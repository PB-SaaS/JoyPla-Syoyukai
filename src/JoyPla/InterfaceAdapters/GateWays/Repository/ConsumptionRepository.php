<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;
use JoyPla\Enterprise\Models\Consumption;
use JoyPla\Enterprise\Models\ConsumptionId;
use JoyPla\Enterprise\Models\ConsumptionItem;
use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Item;
use JoyPla\Enterprise\Models\Lot;
use JoyPla\Enterprise\Models\LotDate;
use JoyPla\Enterprise\Models\LotNumber;
use JoyPla\Enterprise\Models\Price;
use JoyPla\Enterprise\Models\Quantity;
use JoyPla\Enterprise\Models\UnitPrice;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class ConsumptionRepository implements ConsumptionRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId)
    {
        $billingHistory = ModelRepository::getConsumptionInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->all();
        return $billingHistory;
    }

    public function findByInHospitalItem(
        HospitalId $hospitalId,
        array $consumptionItems
    ) {
        $consumptionUnitPriceUseFlag = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->first();

        $division = ModelRepository::getDivisionInstance();

        $division->where('hospitalId', $hospitalId->value());

        foreach ($consumptionItems as $item) {
            $division->orWhere('divisionId', $item->divisionId);
        }

        $division = $division->get()->all();

        $inHospitalItem = ModelRepository::getInHospitalItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        foreach ($consumptionItems as $item) {
            $inHospitalItem->orWhere(
                'inHospitalItemId',
                $item->inHospitalItemId
            );
        }
        $inHospitalItem = $inHospitalItem->get()->all();

        $result = [];
        foreach ($consumptionItems as $item) {
            $division_find_key = array_search(
                $item->divisionId,
                collect_column($division, 'divisionId')
            );
            $inHospitalItem_find_key = array_search(
                $item->inHospitalItemId,
                collect_column($inHospitalItem, 'inHospitalItemId')
            );

            $unitprice = 0;
            if (
                is_numeric($inHospitalItem[$inHospitalItem_find_key]->unitPrice)
            ) {
                $unitprice =
                    (float) $inHospitalItem[$inHospitalItem_find_key]
                        ->unitPrice;
            }

            if ($consumptionUnitPriceUseFlag->billingUnitPrice !== '1') {
                if (
                    $inHospitalItem[$inHospitalItem_find_key]->quantity != 0 &&
                    $inHospitalItem[$inHospitalItem_find_key]->price != 0
                ) {
                    $unitprice =
                        $inHospitalItem[$inHospitalItem_find_key]->price /
                        $inHospitalItem[$inHospitalItem_find_key]->quantity;
                } else {
                    $unitprice = 0;
                }
            }

            $result[] = new ConsumptionItem(
                $item->id,
                new ConsumptionId(''),
                new InHospitalItemId(
                    $inHospitalItem[$inHospitalItem_find_key]->inHospitalItemId
                ),
                Item::create($inHospitalItem[$inHospitalItem_find_key]),
                $hospitalId,
                Division::create($division[$division_find_key]),
                Quantity::create($inHospitalItem[$inHospitalItem_find_key]),
                new Price($inHospitalItem[$inHospitalItem_find_key]->price),
                new UnitPrice($unitprice),
                new Lot(
                    new LotNumber($item->consumeLotNumber),
                    new LotDate($item->consumeLotDate),
                    $inHospitalItem[$inHospitalItem_find_key]->lotManagement ===
                        '1'
                ),
                (int) $item->consumeQuantity +
                    $inHospitalItem[$inHospitalItem_find_key]->quantity *
                        $item->consumeUnitQuantity,
                (int) $inHospitalItem[$inHospitalItem_find_key]->lotManagement,
                $inHospitalItem[$inHospitalItem_find_key]->inItemImage
            );
        }
        return $result;
    }

    public function saveToArray(array $consumptions)
    {
        $consumptions = array_map(function (Consumption $consumption) {
            return $consumption;
        }, $consumptions);

        $history = [];
        $items = [];
        $consumptionItemInstance = ModelRepository::getConsumptionItemInstance();

        foreach ($consumptions as $consumption) {

            $consumptionToArray = $consumption->toArray();

            $history[] = [
                'registrationTime' => $consumption
                    ->getConsumptionDate()
                    ->isToday()
                    ? 'now'
                    : $consumptionToArray['consumptionDate'],
                'billingDate' => $consumptionToArray['consumptionDate'],
                'billingNumber' => $consumptionToArray['consumptionId'],
                'hospitalId' => $consumptionToArray['hospital']['hospitalId'],
                'divisionId' => $consumptionToArray['division']['divisionId'],
                'itemsNumber' => $consumptionToArray['itemCount'],
                'totalAmount' => $consumptionToArray['totalAmount'],
                'billingStatus' => $consumptionToArray['consumptionStatus'],
            ];

            $consumptionItemInstance->orWhere('billingNumber',$consumptionToArray['consumptionId']);

            foreach (
                $consumptionToArray['consumptionItems']
                as $consumptionItem
            ) {
                $items[] = [
                    'registrationTime' => $consumption
                        ->getConsumptionDate()
                        ->isToday()
                        ? 'now'
                        : $consumptionToArray['consumptionDate'],
                    'inHospitalItemId' => $consumptionItem['inHospitalItemId'],
                    'billingNumber' => $consumptionToArray['consumptionId'],
                    'price' => $consumptionItem['price'],
                    'billingQuantity' =>
                        $consumptionItem['consumptionQuantity'],
                    'billingAmount' => $consumptionItem['consumptionPrice'],
                    'hospitalId' => $consumptionItem['hospitalId'],
                    'divisionId' => $consumptionItem['division']['divisionId'],
                    'quantity' => $consumptionItem['quantity']['quantityNum'],
                    'quantityUnit' =>
                        $consumptionItem['quantity']['quantityUnit'],
                    'itemUnit' => $consumptionItem['quantity']['itemUnit'],
                    'lotNumber' => $consumptionItem['lot']['lotNumber'],
                    'lotDate' => $consumptionItem['lot']['lotDate'],
                    'unitPrice' => $consumptionItem['unitPrice'],
                    'lotManagement' => $consumptionItem['lotManagement']
                        ? 't'
                        : 'f',
                    'itemId' => $consumptionItem['item']['itemId'],
                ];
            }
        }
        
        ModelRepository::getConsumptionInstance()->upsertBulk('billingNumber',$history);

        $consumptionItemInstance->delete();

        ModelRepository::getConsumptionItemInstance()->insert($items);

        return $consumptions;
    }

    public function search(HospitalId $hospitalId, object $search)
    {
        $itemSearchFlag = false;
        $itemViewInstance = ModelRepository::getConsumptionItemViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->value('billingNumber');

        $historyViewInstance = ModelRepository::getConsumptionViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        if ($search->itemName !== '') {
            $itemViewInstance->orWhere(
                'itemName',
                '%' . $search->itemName . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->makerName !== '') {
            $itemViewInstance->orWhere(
                'makerName',
                '%' . $search->makerName . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemCode !== '') {
            $itemViewInstance->orWhere(
                'itemCode',
                '%' . $search->itemCode . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemStandard !== '') {
            $itemViewInstance->orWhere(
                'itemStandard',
                '%' . $search->itemStandard . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemJANCode !== '') {
            $itemViewInstance->orWhere(
                'itemJANCode',
                '%' . $search->itemJANCode . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }

        if ($itemSearchFlag) {
            $itemViewInstance = $itemViewInstance->get();
            if ($itemViewInstance->count() == 0) {
                return [[], 0];
            }
            foreach ($itemViewInstance->all() as $item) {
                $historyViewInstance = $historyViewInstance->orWhere(
                    'billingNumber',
                    $item->billingNumber
                );
            }
        }

        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $historyViewInstance->orWhere('divisionId', $divisionId);
            }
        }

        if ($search->yearMonth !== '') {
            $yearMonth = new DateYearMonth($search->yearMonth);
            $nextMonth = $yearMonth->nextMonth();

            $historyViewInstance->where(
                'billingDate',
                $yearMonth->format('Y-m-01'),
                '>='
            );
            $historyViewInstance->where(
                'billingDate',
                $nextMonth->format('Y-m-01'),
                '<'
            );
        }

        $historys = $historyViewInstance
            ->orderBy('billingDate', 'desc')
            ->page($search->currentPage)
            ->paginate($search->perPage);

        if ($historys->getData()->count() == 0) {
            return [[], 0];
        }

        $itemViewInstance = ModelRepository::getConsumptionItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        foreach ($historys->getData()->all() as $history) {
            $itemViewInstance = $itemViewInstance->orWhere(
                'billingNumber',
                $history->billingNumber
            );
        }

        $items = $itemViewInstance->get();
        $consumptions = [];
        foreach ($historys->getData()->all() as $history) {
            $consumption = Consumption::create($history);

            foreach ($items->all() as $item) {
                if (
                    $consumption
                        ->getConsumptionId()
                        ->equal($item->billingNumber)
                ) {
                    $consumption = $consumption->addConsumptionItem(
                        ConsumptionItem::create($item)
                    );
                }
            }

            $consumptions[] = $consumption;
        }

        return [$consumptions, $historys->getTotal()];
    }

    public function find(HospitalId $hospitalId, ConsumptionId $consumptionId)
    {
        $consumptionView = ModelRepository::getConsumptionViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('billingNumber', $consumptionId->value())
            ->get();
        if ($consumptionView->count() <= 0) {
            return null;
        }
        $consumptionItemView = ModelRepository::getConsumptionItemViewInstance()
            ->orderBy('id', 'asc')
            ->where('hospitalId', $hospitalId->value())
            ->where('billingNumber', $consumptionId->value())
            ->get();

        $consumption = Consumption::create($consumptionView->first());

        foreach ($consumptionItemView->all() as $item) {
            $consumption = $consumption->addConsumptionItem(
                ConsumptionItem::create($item)
            );
        }

        return $consumption;
    }

    public function delete(HospitalId $hospitalId, ConsumptionId $consumptionId)
    {
        ModelRepository::getConsumptionInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('billingNumber', $consumptionId->value())
            ->delete();
    }

}

interface ConsumptionRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId);
    public function findByInHospitalItem(
        HospitalId $hospitalId,
        array $consumptionItems
    );
    public function saveToArray(array $consumptionItems);

    public function search(HospitalId $hospitalId, object $search);

    public function find(HospitalId $hospitalId, ConsumptionId $consumptionId);

    public function delete(
        HospitalId $hospitalId,
        ConsumptionId $consumptionId
    );
}
