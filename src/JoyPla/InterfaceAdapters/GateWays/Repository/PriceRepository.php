<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Distributor;
use App\SpiralDb\Price;
use Collection;
use JoyPla\Enterprise\Models\ItemPrice;
use JoyPla\Enterprise\Models\HospitalId;

class PriceRepository implements PriceRepositoryInterface{

    public function findByPriceId( PriceId $priceId )
    {
        $prices = (Price::Where('priceId', $priceId->value())->get())->data->all();

        $result = [];
        foreach($prices as $d)
        {
            $result[] = Price::create($d);
        }
 
        return $result;
    }

    public function saveToArray( HospitalId $hospitalId, ItemId $itemId, array $price, array $attr = [] ){

        $createArray = [
            "hospitalId" => $hospitalId,
            "itemId" => $itemId,
            "distributorId" => $price["distributorId"],
            "distributorMCode" => $price["distributorMCode"],
            "quantity" => $price["quantity"],
            "quantityUnit" => $price["quantityUnit"],
            "itemUnit" => $price["itemUnit"],
            "price" => $price["price"],
            "unitPrice" => $price["unitPrice"],
            "notice" => $price["notice"],
        ];

        return SpiralDb::title("NJ_PriceDB") -> create($createArray);

    }

}

interface PriceRepositoryInterface 
{
    public function findByPriceId( PriceId $priceId );
    public function saveToArray( HospitalId $hospitalId, ItemId $itemId, array $item, array $attr = []);
}