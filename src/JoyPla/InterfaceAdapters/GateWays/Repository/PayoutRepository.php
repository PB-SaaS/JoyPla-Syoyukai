<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Payout as SpiralDbPayout;
use App\SpiralDb\PayoutItem as SpiralDbPayoutItem;
use App\SpiralDb\Hospital;
use App\SpiralDb\InHospitalItemView;

use Exception;
use Auth;
use framework\SpiralConnecter\SpiralDB;
use JoyPla\Enterprise\Models\Payout;
use JoyPla\Enterprise\Models\PayoutItem;
use JoyPla\Enterprise\Models\PayoutHId;
use JoyPla\Enterprise\Models\PayoutQuantity;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Item;
use JoyPla\Enterprise\Models\Price;
use JoyPla\Enterprise\Models\Quantity;
use JoyPla\Enterprise\Models\UnitPrice;
use JoyPla\Enterprise\Models\Lot;
use JoyPla\Enterprise\Models\LotDate;
use JoyPla\Enterprise\Models\LotNumber;
use JoyPla\Enterprise\Models\CardId;

class PayoutRepository implements PayoutRepositoryInterface
{
    public function findByInHospitalItem(HospitalId $hospitalId, array $payoutItems)
    {
        $payoutUnitPriceUseFlag = (Hospital::where('hospitalId', $hospitalId->value())->value('payoutUnitPrice')->get())->data->get(0);
        //$division = SpiralDbDivision::where('hospitalId',$hospitalId->value());

        $division = SpiralDB::title('NJ_divisionDB')->value([
            "registrationTime",
            "divisionId",
            "hospitalId",
            "divisionName",
            "divisionType",
            "deleteFlag",
            "authkey",
            "deliveryDestCode"
        ]);

        $division->where('hospitalId', $hospitalId->value());

        $inHospitalItem = InHospitalItemView::where('hospitalId', $hospitalId->value())->where('notUsedFlag', '1', '!=');

        foreach ($payoutItems as $item) {
            $inHospitalItem->orWhere('inHospitalItemId', $item->inHospitalItemId);
            $division->orWhere('divisionId', $item->payoutSourceDivisionId);
            $division->orWhere('divisionId', $item->payoutTargetDivisionId);
        }
        $inHospitalItem = ($inHospitalItem->get())->data->all();
        $division = $division->get();
        $division = $division->all();

        $result = [];
        foreach ($payoutItems as $item) {
            if ((int)$item->payoutQuantity < 1) {
                continue;
            }

            $source_division_find_key = array_search($item->payoutSourceDivisionId, collect_column($division, 'divisionId'));
            $target_division_find_key = array_search($item->payoutTargetDivisionId, collect_column($division, 'divisionId'));
            $inHospitalItem_find_key = array_search($item->inHospitalItemId, collect_column($inHospitalItem, 'inHospitalItemId'));

            if (($source_division_find_key !== false) && ($target_division_find_key !== false) && ($inHospitalItem_find_key !== false)) {

                $unitprice = 0;
                if (is_numeric($inHospitalItem[$inHospitalItem_find_key]->unitPrice)) {
                    $unitprice = (float)$inHospitalItem[$inHospitalItem_find_key]->unitPrice;
                }

                if ($payoutUnitPriceUseFlag->payoutUnitPrice !== '1') {
                    if ($inHospitalItem[$inHospitalItem_find_key]->quantity != 0 && $inHospitalItem[$inHospitalItem_find_key]->price != 0) {
                        $unitprice = ((int)$inHospitalItem[$inHospitalItem_find_key]->price / (int)$inHospitalItem[$inHospitalItem_find_key]->quantity);
                    } else {
                        $unitprice = 0;
                    }
                }

                $result[] = new PayoutItem(
                    (PayoutHId::generate()),
                    '',
                    (new InHospitalItemId($inHospitalItem[$inHospitalItem_find_key]->inHospitalItemId)),
                    (Item::create($inHospitalItem[$inHospitalItem_find_key])),
                    $hospitalId,
                    (Division::create($division[$source_division_find_key])),
                    (Division::create($division[$target_division_find_key])),
                    (Quantity::create($inHospitalItem[$inHospitalItem_find_key])),
                    (new Price((float)$inHospitalItem[$inHospitalItem_find_key]->price)),
                    (new UnitPrice($unitprice)),
                    (new PayoutQuantity((int)$item->payoutQuantity)),
                    (new Lot(new LotNumber($item->lotNumber), new LotDate($item->lotDate))),
                    (int)$inHospitalItem[$inHospitalItem_find_key]->lotManagement,
                    (new CardId((string)$item->card))
                );
            }
        }

        return $result;
    }


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
                "payoutHistoryId" => (string)$payoutToArray['payoutHId'],
                "hospitalId" => (string)$payoutToArray['hospital']['hospitalId'],
                "sourceDivisionId" => (string)$payoutToArray['sourceDivision']['divisionId'],
                "sourceDivision" => (string)$payoutToArray['sourceDivision']['divisionName'],
                "targetDivisionId" => (string)$payoutToArray['targetDivision']['divisionId'],
                "targetDivision" => (string)$payoutToArray['targetDivision']['divisionName'],
                "itemsNumber" => (string)$payoutToArray['itemCount'],
                "totalAmount" => (string)$payoutToArray['totalAmount']
            ];

            foreach ($payoutToArray['payoutItems'] as $payoutItem) {
                $items[] = [
                    "payoutHistoryId" => (string)$payoutToArray['payoutHId'],
                    "hospitalId" => (string)$payoutItem['hospitalId'],
                    "itemId" => (string)$payoutItem['item']['itemId'],
                    "inHospitalItemId" => (string)$payoutItem['inHospitalItemId'],
                    "sourceDivisionId" => (string)$payoutItem['sourceDivision']['divisionId'],
                    "targetDivisionId" => (string)$payoutItem['targetDivision']['divisionId'],
                    "payoutQuantity" => (string)$payoutItem['payoutQuantity'],
                    "quantity" => (string)$payoutItem['quantity']['quantityNum'],
                    "quantityUnit" => (string)$payoutItem['quantity']['quantityUnit'],
                    "itemUnit" => (string)$payoutItem['quantity']['itemUnit'],
                    "price" => (string)$payoutItem['price'],
                    "unitPrice" => (string)$payoutItem['unitPrice'],
                    "lotNumber" => (string)$payoutItem['lot']['lotNumber'],
                    "lotDate" => (string)$payoutItem['lot']['lotDate'],
                    "cardId" => (string)$payoutItem['card'],
                    "payoutType" => "2",
                    "payoutAmount" => (string)$payoutItem['payoutAmount']
                ];
            }
        }

        SpiralDbPayout::insert($histories);
        SpiralDbPayoutItem::insert($items);

        return $payouts;
    }
}

interface PayoutRepositoryInterface
{
    public function findByInHospitalItem(HospitalId $hospitalId, array $Payouts);
    public function saveToArray(array $Payouts);
}
