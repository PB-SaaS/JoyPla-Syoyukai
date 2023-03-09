<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\Payout;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class PayoutRepository implements PayoutRepositoryInterface
{
    public function saveToArray(array $payouts)
    {
        $payouts = array_map(function (Payout $payout) {
            return $payout;
        }, $payouts);

        $histories = [];
        $items = [];

        foreach ($payouts as $payout) {
            $payoutToArray = $payout->toArray();

            $histories[] = [
                'payoutHistoryId' => (string) $payoutToArray['payoutHId'],
                'hospitalId' =>
                    (string) $payoutToArray['hospital']['hospitalId'],
                'sourceDivisionId' =>
                    (string) $payoutToArray['sourceDivision']['divisionId'],
                'sourceDivision' =>
                    (string) $payoutToArray['sourceDivision']['divisionName'],
                'targetDivisionId' =>
                    (string) $payoutToArray['targetDivision']['divisionId'],
                'targetDivision' =>
                    (string) $payoutToArray['targetDivision']['divisionName'],
                'itemsNumber' => (string) $payoutToArray['itemCount'],
                'totalAmount' => (string) $payoutToArray['totalAmount'],
            ];

            foreach ($payoutToArray['payoutItems'] as $payoutItem) {
                $items[] = [
                    'payoutHistoryId' => (string) $payoutToArray['payoutHId'],
                    'hospitalId' => (string) $payoutItem['hospitalId'],
                    'itemId' => (string) $payoutItem['item']['itemId'],
                    'inHospitalItemId' =>
                        (string) $payoutItem['inHospitalItemId'],
                    'sourceDivisionId' =>
                        (string) $payoutItem['sourceDivision']['divisionId'],
                    'targetDivisionId' =>
                        (string) $payoutItem['targetDivision']['divisionId'],
                    'payoutQuantity' => (string) $payoutItem['payoutQuantity'],
                    'quantity' =>
                        (string) $payoutItem['quantity']['quantityNum'],
                    'quantityUnit' =>
                        (string) $payoutItem['quantity']['quantityUnit'],
                    'itemUnit' => (string) $payoutItem['quantity']['itemUnit'],
                    'price' => (string) $payoutItem['price'],
                    'unitPrice' => (string) $payoutItem['unitPrice'],
                    'lotNumber' => (string) $payoutItem['lot']['lotNumber'],
                    'lotDate' => (string) $payoutItem['lot']['lotDate'],
                    'cardId' => (string) $payoutItem['card'],
                    'payoutType' => '2',
                    'payoutAmount' => (string) $payoutItem['payoutAmount'],
                ];
            }
        }
        ModelRepository::getPayoutInstance()->insert($histories);
        ModelRepository::getPayoutItemInstance()->insert($items);

        return $payouts;
    }
}

interface PayoutRepositoryInterface
{
    public function saveToArray(array $Payouts);
}
