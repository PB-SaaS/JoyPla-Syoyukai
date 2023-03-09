<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\ConsumptionForReference;
use JoyPla\Enterprise\Models\ConsumptionItemForReference;
use JoyPla\Enterprise\Models\HospitalId;
use Collection;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class ConsumptionHistoryRepository implements
    ConsumptionHistoryRepositoryInterface
{
    public function search(HospitalId $hospitalId, object $search)
    {
        if (count($search->divisionIds) === 0) {
            return [[], 0];
        }

        //$historyViewInstance = ConsumptionView::where('hospitalId', $hospitalId->value());
        $historyViewInstance = ModelRepository::getConsumptionViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $historyViewInstance->orWhere('divisionId', $divisionId);
            }
        }

        $histories = $historyViewInstance
            ->orderBy('billingDate', 'desc')
            ->page($search->currentPage)
            ->paginate($search->perPage);

        if ((int) count($histories->getData()->all()) === 0) {
            return [[], 0];
        }

        $consumptionItemViewInstance = ModelRepository::getConsumptionItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        foreach ($histories->getData()->all() as $history) {
            $consumptionItemViewInstance->orWhere(
                'billingNumber',
                $history->billingNumber
            );
        }
        $itemViewInstance = ModelRepository::getInHospitalItemViewInstance()
            ->where('notUsedFlag', '1', '!=')
            ->where('hospitalId', $hospitalId->value());

        $consumptionItems = $consumptionItemViewInstance->get();
        if ((int) $consumptionItems->count() === 0) {
            return [[], 0];
        }

        foreach ($consumptionItems->all() as $consumptionItem) {
            $itemViewInstance->orWhere(
                'inHospitalItemId',
                $consumptionItem->inHospitalItemId
            );
        }

        $items = $itemViewInstance->get();
        if ((int) $items->count() === 0) {
            return [[], 0];
        }

        $priceInstance = ModelRepository::getPriceInstance()
            ->where('hospitalId', $hospitalId->value())
            ->value('priceId')
            ->value('notice');

        foreach ($items->all() as $item) {
            $priceInstance->orWhere('priceId', $item->priceId);
        }
        $price = $priceInstance->get()->all();

        $merging = [];

        foreach ($consumptionItems->all() as $consumptionItem) {
            foreach ($items->all() as $item) {
                $price_fkey = array_search(
                    $item->priceId,
                    collect_column($price, 'priceId')
                );

                if (
                    $consumptionItem->inHospitalItemId ===
                        $item->inHospitalItemId &&
                    $price_fkey !== false
                ) {
                    $itemUnitPrice = is_numeric($item->unitPrice)
                        ? (float) $item->unitPrice
                        : 0;
                    $tmp = new Collection();
                    $tmp->billingNumber = $consumptionItem->billingNumber;
                    $tmp->inHospitalItemId = $consumptionItem->inHospitalItemId;
                    $tmp->hospitalId = $consumptionItem->hospitalId;
                    $tmp->billingQuantity = $consumptionItem->billingQuantity;
                    $tmp->divisionId = $consumptionItem->divisionId;
                    $tmp->divisionName = $consumptionItem->divisionName;
                    $tmp->inItemImage = $item->inItemImage;
                    $tmp->quantity = $item->quantity;
                    $tmp->price = (float) $item->price;
                    $tmp->unitPrice = $itemUnitPrice;
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
                    $tmp->priceNotice = $price[$price_fkey]->notice;
                    $merging[] = $tmp;
                }
            }
        }

        $consumptions = [];
        foreach ($histories->getData()->all() as $history) {
            $consumption = ConsumptionForReference::create($history);

            foreach ($merging as $item) {
                if (
                    $consumption
                        ->getConsumptionId()
                        ->equal($item->billingNumber)
                ) {
                    $consumption = $consumption->addConsumptionItemForReference(
                        ConsumptionItemForReference::create($item)
                    );
                }
            }

            $consumptions[] = $consumption;
        }

        return [$consumptions, $histories->getData()->count()];
    }
}

interface ConsumptionHistoryRepositoryInterface
{
    public function search(HospitalId $hospitalId, object $search);
}
