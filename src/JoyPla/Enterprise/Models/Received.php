<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class Received
{
    private OrderId $orderId;
    private ReceivedId $receivedId;
    private DateYearMonthDayHourMinutesSecond $registDate;
    private array $receivedItems;
    private Hospital $hospital;
    private Division $division;
    private Distributor $distributor;
    private ReceivedStatus $receivedStatus;

    public function __construct(
        OrderId $orderId,
        ReceivedId $receivedId,
        DateYearMonthDayHourMinutesSecond $registDate,
        array $receivedItems,
        Hospital $hospital,
        Division $division,
        Distributor $distributor,
        ReceivedStatus $receivedStatus
        )
    {
        
        $this->orderId = $orderId;
        $this->receivedId = $receivedId;
        $this->registDate = $registDate;
        $this->receivedItems = array_map(function(ReceivedItem $v){
            return $v;
        },$receivedItems);
        $this->hospital = $hospital;
        $this->division = $division;
        $this->distributor = $distributor;
        $this->receivedStatus = $receivedStatus;
    }

    public static function create( Collection $input)
    {
        return new self(
            (new OrderId($input->orderHistoryId) ),
            (new ReceivedId($input->receivingHId) ),
            (new DateYearMonthDayHourMinutesSecond($input->registrationTime) ),
            [],
            (Hospital::create($input) ),
            (Division::create($input) ),
            (Distributor::create($input) ),
            (new ReceivedStatus($input->recevingStatus) ),
        );
    }

    public function getOrderId()
    {
        return $this->orderId;
    }
    
    public function getHospital()
    {
        return $this->hospital;
    }

    public function getDivision()
    {
        return $this->division;
    }
    
    public function getDistributor()
    {
        return $this->distributor;
    }
    public function getReceivedItems()
    {
        return $this->receivedItems;
    }

    public function getReceivedId()
    {
        return $this->receivedId;
    }

    public function itemCount(){
        $array = [];
        foreach($this->receivedItems as $item)
        {
            $array[] = $item->getInHospitalItemId()->value();
        }
        return count(array_unique($array));
    }

    public function addReceivedItem(ReceivedItem $receivedItem)
    {
        $tmp = $this->receivedItems;
        $tmp[] = $receivedItem;
        return $this->setReceivedItems($tmp);
    }
    

    public function totalAmount(){
        $num = 0;
        foreach($this->receivedItems as $item)
        {
            $num += $item->price();
        }
        return $num;
    }

    public function setReceivedItems(array $receivedItems)
    {
        return new self(
            $this->orderId,
            $this->receivedId,
            $this->registDate,
            $receivedItems,
            $this->hospital,
            $this->division,
            $this->distributor,
            $this->receivedStatus,
        );
    }

    public function slipCategory()
    {
        if($this->totalAmount() >= 0)
        {
            return 0;
        }

        return 1;
    }

    public function toArray()
    {
        return [
            'orderId' => $this->orderId->value(),
            'receivedId' => $this->receivedId->value(),
            'registDate' => $this->registDate->value(),
            'receivedItems' =>  array_map(function(ReceivedItem $v){
                return $v->toArray();
            },$this->receivedItems),
            'itemCount' => $this->itemCount(),
            'slipCategory' => $this->slipCategory(),
            'totalAmount' => $this->totalAmount(),
            'hospital' => $this->hospital->toArray(),
            'division' => $this->division->toArray(),
            'distributor' => $this->distributor->toArray(),
            'receivedStatus' => $this->receivedStatus->value(),
            'receivedStatusToString' => $this->receivedStatus->toString(),
        ];
    } 
}