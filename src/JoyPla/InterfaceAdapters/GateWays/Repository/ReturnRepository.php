<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Hospital;
use App\SpiralDb\ReturnHistory;
use App\SpiralDb\ReturnItem as SpiralDbReturnItem;
use App\SpiralDb\ReturnItemView;
use App\SpiralDb\ReturnView;
use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\ReturnData;
use JoyPla\Enterprise\Models\ReturnItem;

class ReturnRepository implements ReturnRepositoryInterface{

    public function saveToArray( HospitalId $hospitalId , array $returns)
    {
        $returns = array_map(function(ReturnData $return){
            return $return;
        },$returns);

        $history = [];
        $items = [];

        foreach($returns as $key => $return){

            $returnToArray = $return->toArray(); 
            $history[] = [ 
                "receivingHId" => $returnToArray['receivedId'],
                "distributorId" => $returnToArray['distributor']['distributorId'],
                "orderHistoryId" => $returnToArray['orderId'],
                "returnHistoryID" => $returnToArray['returnId'],
                "hospitalId" => $returnToArray['hospital']['hospitalId'],
                "itemsNumber" => $returnToArray['itemCount'],
                "returnTotalPrice" => $returnToArray['totalAmount'],
                "divisionId" => $returnToArray['division']['divisionId'],
            ];

            foreach( $returnToArray['returnItems'] as $returnItem )
            {
                $items[] = [
                    "orderCNumber" => $returnItem['orderItemId'],
                    "receivingHId" => $returnToArray['receivedId'],
                    "inHospitalItemId"=> $returnItem['inHospitalItemId'],
                    "receivingNumber"=> $returnItem['receivedItemId'],
                    "price"=> $returnItem['price'],
                    "returnID"=> $returnItem['returnItemId'],
                    "returnCount"=> $returnItem['returnQuantity'],
                    "returnPrice"=> $returnItem['returnPrice'],
                    "hospitalId"=> $returnItem['hospitalId'],
                    "returnHistoryID"=> $returnItem['returnId'],
                    "lotNumber"=> $returnItem['lot']['lotNumber'],
                    "lotDate"=> $returnItem['lot']['lotDate'],
                    "itemId"=> $returnItem['item']['itemId'],
                ];
            }
        } 

        if(count($history) > 0) {
            ReturnHistory::getNewInstance()->upsert('returnHistoryID',$history);
        }
        if(count($items) > 0) {
            SpiralDbReturnItem::getNewInstance()->upsert('returnID',$items);
        }

        return $returns;
    }

    
    public function search( HospitalId $hospitalId , object $search)
    {
        $itemSearchFlag = false;
        $itemViewInstance = ReturnItemView::where('hospitalId',$hospitalId->value())->value('receivingHId')->value('receivingNumber');
        $historyViewInstance = ReturnView::where('hospitalId',$hospitalId->value());

        if($search->itemName)
        {
            $itemViewInstance->orWhere('itemName',"%".$search->itemName."%","LIKE");
            $itemSearchFlag = true; 
        }
        if($search->makerName)
        {
            $itemViewInstance->orWhere('makerName',"%".$search->makerName."%","LIKE");
            $itemSearchFlag = true;
        }
        if($search->itemCode)
        {
            $itemViewInstance->orWhere('itemCode',"%".$search->itemCode."%","LIKE");
            $itemSearchFlag = true;
        }
        if($search->itemStandard)
        {
            $itemViewInstance->orWhere('itemStandard',"%".$search->itemStandard."%","LIKE");
            $itemSearchFlag = true;
        }
        if($search->itemJANCode)
        {
            $itemViewInstance->orWhere('itemJANCode',"%".$search->itemJANCode."%","LIKE");
            $itemSearchFlag = true;
        }

        if($search->returnFlag === 0)
        {
            $itemViewInstance->orWhere('receivingFlag',"0","=");
            $itemViewInstance->orWhere('receivingFlag',"0","ISNULL");
            $itemSearchFlag = true;
        }
        if($search->returnFlag === 1)
        {
            $itemViewInstance->where('receivingFlag',"1","=");
            $itemSearchFlag = true;
        }
        
        $returnIds = [];
        if($itemSearchFlag) {
            $itemViewInstance = $itemViewInstance->get();
            if($itemViewInstance->count == 0 )
            {
                return [[],0];
            }
            foreach($itemViewInstance->data->all() as $item){
                $historyViewInstance = $historyViewInstance->orWhere('returnHistoryID' , $item->returnHistoryID);
                $returnIds[] = $item->returnHistoryID;
            }
        }

        if(is_array($search->distributorIds) && count($search->distributorIds) > 0)
        {   
            foreach($search->distributorIds as $distributorId){
                $historyViewInstance->orWhere('distributorId', $distributorId);
            }
        }   

        if(is_array($search->divisionIds) && count($search->divisionIds) > 0)
        {   
            foreach($search->divisionIds as $divisionId){
                $historyViewInstance->orWhere('divisionId', $divisionId);
            }
        }

        if($search->registerDate){
            $registerDate = new DateYearMonth($search->registerDate);
            $nextMonth =  $registerDate->nextMonth();

            $historyViewInstance->where('registrationTime', $registerDate->format('Y-m-01'), '>=');
            $historyViewInstance->where('registrationTime', $nextMonth->format('Y-m-01'), '<');
        }

        $historys = $historyViewInstance->sort('id','desc')->page($search->currentPage)->paginate($search->perPage);
        if($historys->count == 0)
        {
            return [[],0];
        }
        
        $itemViewInstance = ReturnItemView::getNewInstance()->where('hospitalId',$hospitalId->value());
        foreach($historys->data->all() as $history){
            $itemViewInstance = $itemViewInstance->orWhere('returnHistoryID' , $history->returnHistoryID);
        }
        foreach($returnIds as $returnId)
        {
            $itemViewInstance = $itemViewInstance->orWhere('returnID' , $returnId);
        }

        $items = $itemViewInstance->get();
        $returns = [];
        foreach($historys->data->all() as $history)
        {
            $return = ReturnData::create($history);

            foreach($items->data->all() as $item) {
                if( $return->getReturnId()->equal($item->returnHistoryID) )
                {
                    $item->set('divisionName', $history->divisionName);
                    $return = $return->addReturnItem(ReturnItem::create($item));
                }
            }

            $returns[] = $return;
        }

        return [ $returns , $historys->count ];
    }
}

interface ReturnRepositoryInterface 
{
    
    public function saveToArray( HospitalId $hospitalId , array $returns);
    public function search( HospitalId $hospitalId , object $search);
}