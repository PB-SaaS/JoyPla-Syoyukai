<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\RequestItemCountTransaction;
use JoyPla\Enterprise\Models\RequestItemCount;

class RequestItemCountRepository implements RequestItemCountRepositoryInterface
{
    public function saveToArray(array $requestItemCounts)
    {
        $requestItemCounts = array_map(function (RequestItemCount $requestItemCount) {
            return $requestItemCount;
        }, $requestItemCounts);

        $insert = [];

        foreach ($requestItemCounts as $requestItemCount) {
            $requestItemCountArray = $requestItemCount->toArray();
            $insert[] = [
                "registrationTime" => 'now',
                "recordId" => (string)$requestItemCountArray['recordId'],
                "hospitalId" => (string)$requestItemCountArray['hospitalId'],
                "inHospitalItemId" => (string)$requestItemCountArray['inHospitalItemId'],
                "itemId" => (string)$requestItemCountArray['itemId'],
                "quantity" => (string)$requestItemCountArray['quantity'],
                "sourceDivisionId" => (string)$requestItemCountArray['sourceDivisionId'],
                "targetDivisionId" => (string)$requestItemCountArray['targetDivisionId']
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
