<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Distributor;
use App\SpiralDb\Item;
use Collection;

class ItemRepository implements ItemRepositoryInterface{

    public function findByItemId( ItemId $itemId )
    {
        $items = (SpiralDbItem::where('itemId', $itemId->value())->get())->data->all();

        $result = [];
        foreach($items as $d)
        {
            $result[] = Item::create($d);
        }
 
        return $result;
    }

    public function searchByJanCode(HospitalId $hospitalId ,  string $jancode)
    {        
        $instance = Item::where('hospitalId',$hospitalId->value());
        $instance->where('itemJANCode',$jancode);
        $result = $instance->get();
        $items = $result->data->all();

        return [$items , $result->count];
    }

    public function search( HospitalId $hospitalId ,  object $search)
    {
        $instance = Item::where('hospitalId',$hospitalId->value());
        if($search->itemName !== "")
        {
            $instance->orWhere('itemName',"%".$search->itemName."%","LIKE");
        }
        if($search->makerName !== "")
        {
            $instance->orWhere('makerName',"%".$search->makerName."%","LIKE");
        }
        if($search->itemCode !== "")
        {
            $instance->orWhere('itemCode',"%".$search->itemCode."%","LIKE");
        }
        if($search->itemStandard !== "")
        {
            $instance->orWhere('itemStandard',"%".$search->itemStandard."%","LIKE");
        }
        if($search->itemJANCode !== "")
        {
            $instance->orWhere('itemJANCode',"%".$search->itemJANCode."%","LIKE");
        }

        $result = $instance->sort('id','asc')->page($search->currentPage)->paginate($search->perPage);

        $items = $result->data->all();

        return [$items , $result->count];
    }

    public function saveToArray(TenantId $tenantId, HospitalId $hospitalId, array $item, array $attr = []){

        $createArray = [
            "registrationTime" => "now",
            "updateTime" => "now",
            "tenantId" => $tenantId,
            "hospitalId" => $hospitalId,
            "itemName" => $item["itemName"],
            "category" => $item["category"],
            "smallCategory" => $item["smallCategory"],
            "itemCode" => $item["itemCode"],
            "itemStandard" => $item["itemStandard"],
            "itemJANCode" => $item["itemJANCode"],
            "makerName" => $item["makerName"],
            "catalogNo" => $item["catalogNo"],
            "serialNo" => $item["serialNo"],
            "lotManagement" => $item["lotManagement"],
            "officialFlag" => $item["officialFlag"],
            "officialprice" => $item["officialprice"],
            "officialpriceOld" => $item["officialpriceOld"],
            "quantity" => $item["quantity"],
            "quantityUnit" => $item["quantityUnit"],
            "itemUnit" => $item["itemUnit"],
            "minPrice" => $item["minPrice"],
        ];

        return SpiralDb::title("NJ_itemDB") -> create($createArray);

    }

}

interface ItemRepositoryInterface 
{
    public function findByItemId( ItemId $ItemId );
    public function searchByJanCode(HospitalId $hospitalId ,  string $jancode);
    public function saveToArray( TenantId $tenantId, HospitalId $hospitalId, array $item, array $attr = []);
}