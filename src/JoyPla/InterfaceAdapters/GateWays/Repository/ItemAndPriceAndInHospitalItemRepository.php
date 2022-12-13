<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Distributor;
use App\SpiralDb\Item;
use App\SpiralDb\Price;
use App\SpiralDb\InHospitalItem;
use Collection;

class ItemAndPriceAndInHospitalItemRepository implements ItemAndPriceAndInHospitalItemRepositoryInterface{

    public function saveToArray(TenantId $tenantId, HospitalId $hospitalId, array $input, array $attr = []){
        $itemCreateArray = [
            "registrationTime" => "now",
            "updateTime" => "now",
            "tenantId" => $tenantId,
            "hospitalId" => $hospitalId,
            "itemName" => $input["itemName"],
            "category" => $input["category"],
            "smallCategory" => $input["smallCategory"],
            "itemCode" => $input["itemCode"],
            "itemStandard" => $input["itemStandard"],
            "itemJANCode" => $input["itemJANCode"],
            "makerName" => $input["makerName"],
            "catalogNo" => $input["catalogNo"],
            "serialNo" => $input["serialNo"],
            "lotManagement" => $input["lotManagement"],
            "officialFlag" => $input["officialFlag"],
            "officialprice" => $input["officialprice"],
            "officialpriceOld" => $input["officialpriceOld"],
            "quantity" => $input["quantity"],
            "quantityUnit" => $input["quantityUnit"],
            "itemUnit" => $input["itemUnit"],
            "minPrice" => $input["minPrice"],
        ];

        $itemData = SpiralDb::title("NJ_itemDB") -> create($itemCreateArray);

        $priceCreateArray = [
            "hospitalId" => $hospitalId,
            "itemId" => $itemData->get("itemId"),
            "distributorId" => $input["distributorId"],
            "distributorMCode" => $input["distributorMCode"],
            "quantity" => $input["quantity"],
            "quantityUnit" => $input["quantityUnit"],
            "itemUnit" => $input["itemUnit"],
            "price" => $input["price"],
            "unitPrice" => $input["unitPrice"],
            "notice" => $input["notice"],
        ];

        $priceData = SpiralDb::title("NJ_PriceDB") -> create($priceCreateArray);

        $inHPItemCreateArray = [
            "registrationTime" => "now",
            "updateTime" => "now",
            "itemId" => $itemData->get("itemId"),
            "hospitalId" => $hospitalId,
            "priceId" => $priceData->get("priceId"),
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

        $inHPItemData = SpiralDb::title("NJ_inHPItemDB") -> create($inHPItemCreateArray);

        return ["item" => $itemData, "price" => $priceData, "inHP" => $inHPItemData];

    }

}

interface ItemAndPriceAndInHospitalItemRepositoryInterface 
{
    public function saveToArray( TenantId $tenantId, HospitalId $hospitalId, array $input, array $attr = []);
}