<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\RequestItemCountTransaction;
use JoyPla\Enterprise\Models\RequestItemCount;

class RequestItemCountRepository implements RequestItemCountRepositoryInterface
{
    public function saveToArray(array $requestItemCounts)
    {
        $requestItemCountsToArray = array_map(function (RequestItemCount $requestItemCount) {
            return $requestItemCount->toArray();
        }, $requestItemCounts);

        $insert = [];

        foreach ($requestItemCountsToArray as $requestItemCount) {
            $insert[] = [
                "registrationTime" => 'now',
                "recordId" => $requestItemCount['recordId'],
                "hospitalId" => $requestItemCount['hospitalId'],
                "divisionId" => $requestItemCount['divisionId'],
                "inHospitalItemId" => $requestItemCount['inHospitalItemId'],
                "itemId" => $requestItemCount['itemId'],
                "quantity" => $requestItemCounts['quantity']
            ];
        }
        RequestItemCountTransaction::insert($insert);

        return $requestItemCounts;
    }
}

interface RequestItemCountRepositoryInterface
{
    public function saveToArray(array $requestItemCounts);
}
