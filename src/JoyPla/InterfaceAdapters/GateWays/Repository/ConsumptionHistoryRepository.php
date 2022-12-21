<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\ConsumptionItemView;
use App\SpiralDb\ConsumptionView;
use App\SpiralDb\InHospitalItemView;
use Exception;
use framework\SpiralConnecter\SpiralDB;
use JoyPla\Enterprise\Models\ConsumptionForReference;
use JoyPla\Enterprise\Models\ConsumptionItemForReference;
use JoyPla\Enterprise\Models\HospitalId;
use Collection;
class ConsumptionHistoryRepository implements ConsumptionHistoryRepositoryInterface
{
    public function search(HospitalId $hospitalId, object $search)
    {
        if (count($search->divisionIds) === 0) {
            return [[],0];
        }

        $historyViewInstance = ConsumptionView::where('hospitalId', $hospitalId->value());
        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $historyViewInstance->orWhere('divisionId', $divisionId);
            }
        }

        $histories = $historyViewInstance->sort('billingDate', 'desc')->page($search->currentPage)->paginate($search->perPage);

        if ((int)$histories->count === 0) {
            return [[],0];
        }

        $consumptionItemViewInstance = ConsumptionItemView::where('hospitalId', $hospitalId->value());

        foreach ($histories->data->all() as $history) {
            $consumptionItemViewInstance->orWhere('billingNumber', $history->billingNumber);
        }
        $itemViewInstance = InHospitalItemView::where('notUsedFlag', '1', '!=')->where('hospitalId', $hospitalId->value());

        $consumptionItems = $consumptionItemViewInstance->get();
        if ((int)$consumptionItems->count === 0) {
            return [[],0];
        }

        foreach ($consumptionItems->data->all() as $consumptionItem) {
            $itemViewInstance->orWhere('inHospitalItemId', $consumptionItem->inHospitalItemId);
        }

        $items = $itemViewInstance->get();
        if ((int)$items->count === 0) {
            return [[],0];
        }

        $merging = [];
        
        foreach ($consumptionItems->data->all() as $consumptionItem) {
            foreach ($items->data->all() as $item) {
                if ($consumptionItem->inHospitalItemId === $item->inHospitalItemId) {
                    $tmp = new Collection();
                    $tmp->billingNumber = $consumptionItem->billingNumber;
                    $tmp->inHospitalItemId = $consumptionItem->inHospitalItemId;
                    $tmp->hospitalId = $consumptionItem->hospitalId;
                    $tmp->billingQuantity = $consumptionItem->billingQuantity;
                    $tmp->divisionId = $consumptionItem->divisionId;
                    $tmp->divisionName = $consumptionItem->divisionName;
                    $tmp->inItemImage = $item->inItemImage;
                    $tmp->quantity = $item->quantity;
                    $tmp->price = $item->price;
                    $tmp->unitPrice = $item->unitPrice;
                    $tmp->quantityNum = $item->quantity;
                    $tmp->quantityUnit = $item->quantityUnit;
                    $tmp->itemUnit = $item->itemUnit;
                    $tmp->itemId = $item->itemId;
                    $tmp->itemName = $item->itemName;
                    $tmp->itemCode = $item->itemCode;
                    $tmp->itemStandard = $item->itemStandard;
                    $tmp->itemJANCode = $item->itemJANCode;
                    $tmp->makerName = $item->makerName;
                    $tmp->serialNo = $item->serialNo;
                    $tmp->catalogNo = $item->catalogNo;
                    $merging[] = $tmp;
                }
            }
        }

        $consumptions = [];
        foreach ($histories->data->all() as $history) {
            $consumption = ConsumptionForReference::create($history);

            foreach ($merging as $item) {
                if ($consumption->getConsumptionId()->equal($item->billingNumber)) {
                    $consumption = $consumption->addConsumptionItemForReference(ConsumptionItemForReference::create($item));
                }
            }

            $consumptions[] = $consumption;
        }
        
        return [ $consumptions , $histories->count ];
    }
}

interface ConsumptionHistoryRepositoryInterface
{
    public function search(HospitalId $hospitalId, object $search);
}
