<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Distributor;
use App\SpiralDb\Item;
use App\SpiralDb\Price;
use App\SpiralDb\InHospitalItem;
use Collection;
use framework\SpiralConnecter\SpiralDB;

class PriceAndInHospitalItemRepository implements PriceAndInHospitalItemRepositoryInterface{

    public function saveToArray($hospitalId, $itemId, array $input, array $attr = []){
        $priceCreateArray = [
            "hospitalId" => $hospitalId,
            "itemId" => $itemId,
            "distributorId" => $input["distributorId"],
            "distributorMCode" => $input["distributorMCode"],
            "quantity" => $input["quantity"],
            "quantityUnit" => $input["quantityUnit"],
            "itemUnit" => $input["itemUnit"],
            "price" => $input["price"],
            "unitPrice" => $input["unitPrice"],
            "notice" => $input["notice"],
        ];

        $priceCreateData = SpiralDb::title("NJ_PriceDB") -> create($priceCreateArray);
        $id = $priceCreateData->get("id");
        $priceData = SpiralDb::title("NJ_PriceDB") -> value(["priceId"]) ->find((int)$id);
        $priceId = $priceData->get("priceId");

        $inHPItemCreateArray = [
            "registrationTime" => "now",
            "updateTime" => "now",
            "itemId" => $itemId,
            "hospitalId" => $hospitalId,
            "priceId" => $priceId,
            "distributorId" => $input["distributorId"],
            "distributorMCode" => $input["distributorMCode"],
            "quantity" => $input["quantity"],
            "quantityUnit" => $input["quantityUnit"],
            "itemUnit" => $input["itemUnit"],
            "price" => $input["price"],
            "unitPrice" => $input["unitPrice"],
            "medicineCategory" => $input["medicineCategory"],
            "homeCategory" => $input["homeCategory"],
            "measuringInst" => $input["measuringInst"],
            "notice" => $input["notice"],
        ];

        $inHPItemCreateData = SpiralDb::title("NJ_inHPItemDB") -> create($inHPItemCreateArray);
        $id = $inHPItemCreateData->get("id");
        $inHPItemData = SpiralDb::title("NJ_inHPItemDB") -> value(["inHospitalItemId"]) ->find((int)$id);

        return ["price" => $priceData, "inHP" => $inHPItemData];

    }

}

interface PriceAndInHospitalItemRepositoryInterface 
{
    public function saveToArray($hospitalId, $itemId, array $input, array $attr = []);
}