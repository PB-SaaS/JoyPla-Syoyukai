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
        $insertItems = [];
        $updateItems = [];

        foreach ($payouts as $payout) {
            $payoutToArray = $payout->toArray();

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
                        'cardId' => (string) $payoutItem['card'],
                        'payoutType' => '2',
                        'payoutAmount' => (string) $payoutItem['payoutAmount'],
                    ];
                } else {
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
                        'cardId' => (string) $payoutItem['card'],
                        'payoutType' => '2',
                        'payoutAmount' => (string) $payoutItem['payoutAmount'],
                    ];
                }
            }
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
}

interface PayoutRepositoryInterface
{
    public function saveToArray(array $Payouts);
}