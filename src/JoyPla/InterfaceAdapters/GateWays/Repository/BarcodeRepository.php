<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Distributor;
use App\SpiralDb\InHospitalItemView;
use App\SpiralDb\Price;
use Collection;
use JoyPla\Enterprise\Models\InHospitalItem;
use JoyPla\Enterprise\Models\HospitalId;

class BarcodeRepository implements BarcodeRepositoryInterface{

    public function searchByJanCode(HospitalId $hospitalId ,  string $jancode)
    {        
        $instance = InHospitalItemView::where('hospitalId',$hospitalId->value());
        $instance->where('itemJANCode',$jancode);
        $result = $instance->get();
        $inHospitalItems = $result->data->all();

        $price = Price::where('hospitalId' , $hospitalId->value())->value('priceId')->value('notice');

        foreach($inHospitalItems as $item)
        {
            $price->orWhere('priceId',$item->priceId);
        }

        $price = ($price->get())->data->all();

        foreach($inHospitalItems as $key => $item)
        {
            $price_fkey = array_search($item->priceId, collect_column($price, 'priceId'));
            $inHospitalItems[$key]->set('priceNotice',$price[$price_fkey]->notice);
        }

        return [$inHospitalItems , $result->count];
    }
}

interface BarcodeRepositoryInterface 
{
    public function searchByJanCode(HospitalId $hospitalId ,  string $jancode);
}