<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Distributor;
use App\SpiralDb\InHospitalItemView;
use App\SpiralDb\Price;
use Collection;
use JoyPla\Enterprise\Models\InHospitalItem;
use JoyPla\Enterprise\Models\HospitalId;

class InHospitalItemRepository implements InHospitalItemRepositoryInterface{

    public function findByHospitalId( HospitalId $hospitalId )
    {
        $inHospitalItems = (InHospitalItemView::where('hospitalId',$hospitalId->value())->get())->data->all();

        $result = [];
        foreach($inHospitalItems as $d)
        {
            $result[] = InHospitalItem::create($d);
        }
 
        return $result;
    }

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

    public function search( HospitalId $hospitalId ,  object $search)
    {
        $instance = InHospitalItemView::where('hospitalId',$hospitalId->value());
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
        if(count($search->distributorIds) > 0)
        {
            foreach( $search->distributorIds as $distributorId)
            {
                $instance->orWhere('distributorId',$distributorId);
            }
        }

        if($search->isNotUse == '1')
        {
            $instance->where('notUsedFlag','1');
        } else if($search->isNotUse == '0')
        {
            $instance->where('notUsedFlag','1','!=');
        }

        $result = $instance->page($search->currentPage)->paginate($search->perPage);

        $inHospitalItems = $result->data->all();

        if($result->count == 0)
        {
            foreach($inHospitalItems as $key => $item)
            {
                $inHospitalItems[$key]->set('priceNotice',"");
            }
            return [$inHospitalItems , $result->count];
        }

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

interface InHospitalItemRepositoryInterface 
{
    public function findByHospitalId( HospitalId $hospitalId );
    public function search( HospitalId $hospitalId , object $search);
    public function searchByJanCode(HospitalId $hospitalId ,  string $jancode);
}