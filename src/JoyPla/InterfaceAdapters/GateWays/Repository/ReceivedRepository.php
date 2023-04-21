<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\Received;
use JoyPla\Enterprise\Models\ReceivedId;
use JoyPla\Enterprise\Models\ReceivedItem;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class ReceivedRepository implements ReceivedRepositoryInterface
{
    public function saveToArray(HospitalId $hospitalId, array $receiveds)
    {
        $receiveds = array_map(function (Received $received) {
            return $received;
        }, $receiveds);

        $history = [];
        $items = [];

        foreach ($receiveds as $key => $received) {
            $receivedToArray = $received->toArray();
            $history[] = [
                'registrationTime' => $receivedToArray['registDate'],
                'receivingHId' => $receivedToArray['receivedId'],
                'distributorId' =>
                    $receivedToArray['distributor']['distributorId'],
                'orderHistoryId' => $receivedToArray['orderId'],
                'hospitalId' => $receivedToArray['hospital']['hospitalId'],
                'itemsNumber' => $receivedToArray['itemCount'],
                'divisionId' => $receivedToArray['division']['divisionId'],
                'recevingStatus' => $receivedToArray['receivedStatus'],
                'slipCategory' => $receivedToArray['slipCategory'],
                'totalAmount' => (string) $receivedToArray['totalAmount'],
            ];

            foreach ($receivedToArray['receivedItems'] as $receivedItem) {
                $items[] = [
                    'orderCNumber' => $receivedItem['orderItemId'],
                    'receivingCount' => $receivedItem['receivedQuantity'],
                    'receivingHId' => $receivedItem['receivedId'],
                    'inHospitalItemId' => $receivedItem['inHospitalItemId'],
                    'receivingNumber' => $receivedItem['receivedItemId'],
                    'price' => (string) $receivedItem['price'],
                    'receivingPrice' => (string) $receivedItem['receivedPrice'],
                    'hospitalId' => $receivedItem['hospitalId'],
                    'totalReturnCount' => $receivedItem['returnQuantity'],
                    'divisionId' => $receivedItem['division']['divisionId'],
                    'distributorId' =>
                        $receivedItem['distributor']['distributorId'],
                    'adjAmount' => (string) $receivedItem['adjustmentAmount'],
                    'priceAfterAdj' =>
                        (string) $receivedItem['priceAfterAdjustment'],
                    'lotNumber' => $receivedItem['lot']['lotNumber'],
                    'lotDate' => $receivedItem['lot']['lotDate'],
                    'itemId' => $receivedItem['item']['itemId'],
                ];
            }
        }
        if (count($history) > 0) {
            ModelRepository::getReceivedInstance()->upsertBulk(
                'receivingHId',
                $history
            );
        }
        if (count($items) > 0) {
            ModelRepository::getReceivedItemInstance()->upsertBulk(
                'receivingNumber',
                $items
            );
        }

        return $receiveds;
    }

    public function search(HospitalId $hospitalId, object $search)
    {
        $itemSearchFlag = false;
        $itemViewInstance = ModelRepository::getReceivedItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        $historyViewInstance = ModelRepository::getReceivedViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        if ($search->itemName) {
            $itemViewInstance->orWhere(
                'itemName',
                '%' . $search->itemName . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->makerName) {
            $itemViewInstance->orWhere(
                'makerName',
                '%' . $search->makerName . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemCode) {
            $itemViewInstance->orWhere(
                'itemCode',
                '%' . $search->itemCode . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemStandard) {
            $itemViewInstance->orWhere(
                'itemStandard',
                '%' . $search->itemStandard . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemJANCode) {
            $itemViewInstance->orWhere(
                'itemJANCode',
                '%' . $search->itemJANCode . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }

        if ($search->receivedFlag === 0) {
            $itemViewInstance->orWhere('receivingFlag', '0', '=');
            $itemViewInstance->orWhere('receivingFlag', '0', 'ISNULL');
            $itemSearchFlag = true;
        }
        if ($search->receivedFlag === 1) {
            $itemViewInstance->where('receivingFlag', '1', '=');
            $itemSearchFlag = true;
        }

        $receivingNumbers = [];
        if ($itemSearchFlag) {
            $itemViewInstance = $itemViewInstance->get();
            if ($itemViewInstance->count() == 0) {
                return [[], 0];
            }
            foreach ($itemViewInstance->all() as $item) {
                $historyViewInstance = $historyViewInstance->orWhere(
                    'receivingHId',
                    $item->receivingHId
                );
                $receivingNumbers[] = $item->receivingNumber;
            }
        }

        if (
            is_array($search->distributorIds) &&
            count($search->distributorIds) > 0
        ) {
            foreach ($search->distributorIds as $distributorId) {
                $historyViewInstance->orWhere('distributorId', $distributorId);
            }
        }

        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $historyViewInstance->orWhere('divisionId', $divisionId);
            }
        }

        if ($search->registerDate) {
            $registerDate = new DateYearMonth($search->registerDate);
            $nextMonth = $registerDate->nextMonth();

            $historyViewInstance->where(
                'registrationTime',
                $registerDate->format('Y-m-01'),
                '>='
            );
            $historyViewInstance->where(
                'registrationTime',
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

        $itemViewInstance = ModelRepository::getReceivedItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        foreach ($historys->getData()->all() as $history) {
            $itemViewInstance = $itemViewInstance->orWhere(
                'receivingHId',
                $history->receivingHId
            );
        }
        foreach ($receivingNumbers as $receivingNumber) {
            $itemViewInstance = $itemViewInstance->orWhere(
                'receivingNumber',
                $receivingNumber
            );
        }

        $items = $itemViewInstance->get();
        $receiveds = [];
        foreach ($historys->getData()->all() as $history) {
            $received = Received::create($history);

            foreach ($items->all() as $item) {
                if ($received->getReceivedId()->equal($item->receivingHId)) {
                    $received = $received->addReceivedItem(
                        ReceivedItem::create($item)
                    );
                }
            }

            $receiveds[] = $received;
        }

        return [$receiveds, $historys->getTotal()];
    }

    public function index(HospitalId $hospitalId, ReceivedId $receivedId)
    {
        $receivedView = ModelRepository::getReceivedViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('receivingHId', $receivedId->value());

        $receivedView = $receivedView->get();

        if ((int) $receivedView->count() === 0) {
            return null;
        }
        $receivedItemView = ModelRepository::getReceivedItemViewInstance()
            ->orderBy('id', 'asc')
            ->where('hospitalId', $hospitalId->value())
            ->where('receivingHId', $receivedId->value())
            ->get();

        $received = Received::create($receivedView->first());

        foreach ($receivedItemView->all() as $item) {
            $received = $received->addReceivedItem(ReceivedItem::create($item));
        }

        return $received;
    }
}

interface ReceivedRepositoryInterface
{
    public function saveToArray(HospitalId $hospitalId, array $receiveds);
    public function search(HospitalId $hospitalId, object $search);
    public function index(HospitalId $hospitalId, ReceivedId $receivedId);
}
