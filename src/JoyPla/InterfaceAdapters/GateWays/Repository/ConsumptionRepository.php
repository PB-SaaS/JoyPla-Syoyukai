<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Consumption as SpiralDbConsumption;
use App\SpiralDb\ConsumptionItem as SpiralDbConsumptionItem;
use App\SpiralDb\ConsumptionItemView;
use App\SpiralDb\ConsumptionView;
use App\SpiralDb\Hospital;
use App\SpiralDb\InHospitalItemView;
use Exception;
use framework\SpiralConnecter\SpiralDB;
use JoyPla\Enterprise\Models\Consumption;
use JoyPla\Enterprise\Models\ConsumptionId;
use JoyPla\Enterprise\Models\ConsumptionItem;
use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Item;
use JoyPla\Enterprise\Models\Lot;
use JoyPla\Enterprise\Models\LotDate;
use JoyPla\Enterprise\Models\LotNumber;
use JoyPla\Enterprise\Models\Price;
use JoyPla\Enterprise\Models\Quantity;
use JoyPla\Enterprise\Models\UnitPrice;

class ConsumptionRepository implements ConsumptionRepositoryInterface{

    public function findByHospitalId( HospitalId $hospitalId )
    {
        $billingHistory = (SpiralDbConsumption::where('hospitalId',$hospitalId->value())->get())->data->all();

        return $billingHistory;
    }

    public function findByInHospitalItem( HospitalId $hospitalId , array $consumptionItems ){

        $consumptionUnitPriceUseFlag = (Hospital::where('hospitalId', $hospitalId->value())->value('billingUnitPrice')->get())->data->get(0);

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

        $division->where('hospitalId',$hospitalId->value());

        foreach($consumptionItems as $item){
            $division->orWhere('divisionId',$item->divisionId);
        }

        $division = $division->get();
        $division = $division->all();

        $inHospitalItem = InHospitalItemView::where('hospitalId',$hospitalId->value());
        foreach($consumptionItems as $item){
            $inHospitalItem->orWhere('inHospitalItemId',$item->inHospitalItemId);
        }
        $inHospitalItem = ($inHospitalItem->get())->data->all();

        $result = [];
        foreach($consumptionItems as $item){
            $division_find_key = array_search($item->divisionId, collect_column($division, 'divisionId'));
            $inHospitalItem_find_key = array_search($item->inHospitalItemId, collect_column($inHospitalItem, 'inHospitalItemId'));

            $unitprice = 0;
            if(is_numeric($inHospitalItem[$inHospitalItem_find_key]->unitPrice))
            {
                $unitprice = (float)$inHospitalItem[$inHospitalItem_find_key]->unitPrice;
            }
            
            if($consumptionUnitPriceUseFlag->billingUnitPrice !== '1')
            {
                if($inHospitalItem[$inHospitalItem_find_key]->quantity != 0 && $inHospitalItem[$inHospitalItem_find_key]->price != 0)
                {
                    $unitprice = ($inHospitalItem[$inHospitalItem_find_key]->price / $inHospitalItem[$inHospitalItem_find_key]->quantity) ;
                }
                else 
                {
                    $unitprice = 0;
                }
            }
            
            $result[] = new ConsumptionItem(
                (new ConsumptionId('') ),
                (new InHospitalItemId($inHospitalItem[$inHospitalItem_find_key]->inHospitalItemId) ),
                (Item::create($inHospitalItem[$inHospitalItem_find_key]) ),
                $hospitalId,
                (Division::create($division[$division_find_key]) ),
                (Quantity::create($inHospitalItem[$inHospitalItem_find_key]) ),
                (new Price($inHospitalItem[$inHospitalItem_find_key]->price) ),
                (new UnitPrice($unitprice) ),
                (new Lot( (new LotNumber($item->consumeLotNumber)) , ( new LotDate($item->consumeLotDate)) , ( $inHospitalItem[$inHospitalItem_find_key]->lotManagement === '1' )) ),
                (int) $item->consumeQuantity + ( $inHospitalItem[$inHospitalItem_find_key]->quantity * $item->consumeUnitQuantity ) ,
                (int) $inHospitalItem[$inHospitalItem_find_key]->lotManagement,
                $inHospitalItem[$inHospitalItem_find_key]->inItemImage,
            );
        }
        return $result;
    }

    public function saveToArray(array $consumptions)
    {
        $consumptions = array_map(function(Consumption $consumption){
            return $consumption;
        },$consumptions);

        $history = [];
        $items = [];

        foreach($consumptions as $consumption){
            $consumptionToArray = $consumption->toArray();

            $history[] = [
                "registrationTime" => ($consumption->getConsumptionDate()->isToday())? "now" : $consumptionToArray['consumptionDate'],
                "billingDate" => $consumptionToArray['consumptionDate'],
                "billingNumber" => $consumptionToArray['consumptionId'],
                "hospitalId" => $consumptionToArray['hospital']['hospitalId'],
                "divisionId" => $consumptionToArray['division']['divisionId'],
                "itemsNumber" => $consumptionToArray['itemCount'],
                "totalAmount" => $consumptionToArray['totalAmount'],
                "billingStatus" => $consumptionToArray['consumptionStatus'],
            ];

            foreach( $consumptionToArray['consumptionItems'] as $consumptionItem )
            {
                $items[] = [
                    "registrationTime" => ($consumption->getConsumptionDate()->isToday())? "now" : $consumptionToArray['consumptionDate'],
                    "inHospitalItemId" => $consumptionItem['inHospitalItemId'],
                    "billingNumber" => $consumptionToArray['consumptionId'],
                    "price" => $consumptionItem['price'],
                    "billingQuantity" => $consumptionItem['consumptionQuantity'],
                    "billingAmount" => $consumptionItem['consumptionPrice'],
                    "hospitalId" => $consumptionItem['hospitalId'],
                    "divisionId" => $consumptionItem['division']['divisionId'],
                    "quantity" => $consumptionItem['quantity']['quantityNum'],
                    "quantityUnit" => $consumptionItem['quantity']['quantityUnit'],
                    "itemUnit" => $consumptionItem['quantity']['itemUnit'],
                    "lotNumber" => $consumptionItem['lot']['lotNumber'],
                    "lotDate" => $consumptionItem['lot']['lotDate'],
                    "unitPrice" => $consumptionItem['unitPrice'],
                    "lotManagement" => $consumptionItem['lotManagement'],
                    "itemId" => $consumptionItem['item']['itemId'],
                ];
            }

        }

        SpiralDbConsumption::insert($history);
        SpiralDbConsumptionItem::insert($items);

        return $consumptions;
    } 

    public function search( HospitalId $hospitalId , object $search)
    {
        $itemSearchFlag = false;
        $itemViewInstance = ConsumptionItemView::where('hospitalId',$hospitalId->value())->value('billingNumber');
        $historyViewInstance = ConsumptionView::where('hospitalId',$hospitalId->value());

        if($search->itemName !== "")
        {
            $itemViewInstance->orWhere('itemName',"%".$search->itemName."%","LIKE");
            $itemSearchFlag = true;
        }
        if($search->makerName !== "")
        {
            $itemViewInstance->orWhere('makerName',"%".$search->makerName."%","LIKE");
            $itemSearchFlag = true;
        }
        if($search->itemCode !== "")
        {
            $itemViewInstance->orWhere('itemCode',"%".$search->itemCode."%","LIKE");
            $itemSearchFlag = true;
        }
        if($search->itemStandard !== "")
        {
            $itemViewInstance->orWhere('itemStandard',"%".$search->itemStandard."%","LIKE");
            $itemSearchFlag = true;
        }
        if($search->itemJANCode !== "")
        {
            $itemViewInstance->orWhere('itemJANCode',"%".$search->itemJANCode."%","LIKE");
            $itemSearchFlag = true;
        }

        if($itemSearchFlag) {
            $itemViewInstance = $itemViewInstance->get();
            if($itemViewInstance->count == 0 )
            {
                return [[],0];
            }
            foreach($itemViewInstance->data->all() as $item){
                $historyViewInstance = $historyViewInstance->orWhere('billingNumber' , $item->billingNumber);
            }
        }

        if(is_array($search->divisionIds) && count($search->divisionIds) > 0)
        {   
            foreach($search->divisionIds as $divisionId){
                $historyViewInstance->orWhere('divisionId', $divisionId);
            }
        }   
        
        if($search->yearMonth !== ""){
            $yearMonth = new DateYearMonth($search->yearMonth);
            $nextMonth =  $yearMonth->nextMonth();

            $historyViewInstance->where('billingDate', $yearMonth->format('Y-m-01'), '>=');
            $historyViewInstance->where('billingDate', $nextMonth->format('Y-m-01'), '<');
        }


        $historys = $historyViewInstance->sort('billingDate','desc')->page($search->currentPage)->paginate($search->perPage);

        if($historys->count == 0)
        {
            return  [[],0];
        }
        
        $itemViewInstance = ConsumptionItemView::getNewInstance()->where('hospitalId',$hospitalId->value());
        
        foreach($historys->data->all() as $history){
            $itemViewInstance = $itemViewInstance->orWhere('billingNumber' , $history->billingNumber);
        }

        $items = $itemViewInstance->get();
        $consumptions = [];
        foreach($historys->data->all() as $history)
        {
           $consumption = Consumption::create($history);

            foreach($items->data->all() as $item) {
                if( $consumption->getConsumptionId()->equal($item->billingNumber) )
                {
                    $consumption = $consumption->addConsumptionItem(ConsumptionItem::create($item));
                }
            }

            $consumptions[] = $consumption;
        }

        return [ $consumptions , $historys->count ];
    }


    public function index( HospitalId $hospitalId , ConsumptionId $consumptionId)
    {
        $consumptionView = ConsumptionView::where('hospitalId',$hospitalId->value())->where('billingNumber',$consumptionId->value())->get();
        if($consumptionView->count <= 0)
        {
            return null;
        }
        $consumptionItemView = ConsumptionItemView::sort('id','asc')->where('hospitalId',$hospitalId->value())->where('billingNumber',$consumptionId->value())->get();
        
        $consumption = Consumption::create($consumptionView->data->get(0));
        
        foreach($consumptionItemView->data->all() as $item) {
            $consumption = $consumption->addConsumptionItem(ConsumptionItem::create($item));
        }

        return $consumption;
    }

    public function delete( HospitalId $hospitalId , ConsumptionId $consumptionId)
    {
        ConsumptionView::where('hospitalId',$hospitalId->value())->where('billingNumber',$consumptionId->value())->delete();
    }
}

interface ConsumptionRepositoryInterface 
{
    public function findByHospitalId( HospitalId $hospitalId );
    public function findByInHospitalItem( HospitalId $hospitalId , array $consumptionItems );
    public function saveToArray(array $consumptionItems );

    public function search( HospitalId $hospitalId , object $search);

    public function index( HospitalId $hospitalId , ConsumptionId $consumptionId);

    public function delete( HospitalId $hospitalId , ConsumptionId $consumptionId);
    
}