<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Distributor;
use App\SpiralDb\InHospitalItemView;
use App\SpiralDb\Order as SpiralDbOrder;
use App\SpiralDb\OrderItemView;
use App\SpiralDb\OrderView;
use App\SpiralDb\Price;
use Collection;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Order;
use JoyPla\Enterprise\Models\OrderItem;

class BarcodeRepository implements BarcodeRepositoryInterface{

    public function searchByJanCode(HospitalId $hospitalId ,  string $jancode )
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

    public function orderSearchByJanCode(HospitalId $hospitalId ,  string $jancode , $divisionId)
    {        
        $instance = OrderItemView::where('hospitalId',$hospitalId->value());
        $instance->where('itemJANCode',$jancode);

        $instance->where('receivingFlag','1' , '!=');

        if($divisionId instanceof DivisionId)
        {
            $instance->where('divisionId',$divisionId->value());
        }
        
        
        $result = $instance->get();
        if($result->count == 0)
        {
            return [[] ,0];
        }

        $orderItems = $result->data->all();

        $historys = OrderView::where('hospitalId',$hospitalId->value());
        foreach($orderItems as $item)
        {
            $historys->orWhere('orderNumber',$item->orderNumber);
        }
        $historys = $historys->get();
        $orders = [];
        foreach($historys->data->all() as $history)
        {
           $order = Order::create($history);

            foreach($orderItems as $item) {
                if( $order->getOrderId()->equal($item->orderNumber) )
                {
                    $order = $order->addOrderItem(OrderItem::create($item));
                }
            }

            $orders[] = $order;
        }

        return [$orders , count($orders)];
    }

    public function orderSearchByInHospitalItemId(HospitalId $hospitalId ,  InHospitalItemId $inHospitalItemId , $divisionId = "")
    {        
        $instance = OrderItemView::where('hospitalId',$hospitalId->value());
        $instance->where('inHospitalItemId',$inHospitalItemId->value());
        $instance->where('receivingFlag','1' , '!=');

        if($divisionId instanceof DivisionId)
        {
            $instance->where('divisionId',$divisionId->value());
        }
        
        $result = $instance->get();
        if($result->count == 0)
        {
            return [[] ,0];
        }

        $orderItems = $result->data->all();

        $historys = OrderView::where('hospitalId',$hospitalId->value());
        foreach($orderItems as $item)
        {
            $historys->orWhere('orderNumber',$item->orderNumber);
        }
        $historys = $historys->get();
        $orders = [];
        foreach($historys->data->all() as $history)
        {
           $order = Order::create($history);

            foreach($orderItems as $item) {
                if( $order->getOrderId()->equal($item->orderNumber) )
                {
                    $order = $order->addOrderItem(OrderItem::create($item));
                }
            }

            $orders[] = $order;
        }

        return [$orders , count($orders)];
    }
}

interface BarcodeRepositoryInterface 
{
    public function searchByJanCode(HospitalId $hospitalId ,  string $jancode);
    public function orderSearchByJanCode(HospitalId $hospitalId ,  string $jancode , $divisionId);
    public function orderSearchByInHospitalItemId(HospitalId $hospitalId ,  InHospitalItemId $inHospitalItemId , $divisionId);
}