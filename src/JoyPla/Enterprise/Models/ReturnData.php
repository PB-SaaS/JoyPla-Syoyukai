<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class ReturnData
{
    private OrderId $orderId;
    private ReceivedId $receivedId;
    private ReturnId $returnId;
    private DateYearMonthDayHourMinutesSecond $registDate;
    private array $returnItems;
    private Hospital $hospital;
    private Division $division;
    private Distributor $distributor;

    public function __construct(
        OrderId $orderId,
        ReceivedId $receivedId,
        ReturnId $returnId,
        DateYearMonthDayHourMinutesSecond $registDate,
        array $returnItems,
        Hospital $hospital,
        Division $division,
        Distributor $distributor
        )
    {
        $this->orderId = $orderId;
        $this->receivedId = $receivedId;
        $this->returnId = $returnId;
        $this->registDate = $registDate;
        $this->returnItems = $returnItems;
        $this->hospital = $hospital;
        $this->division = $division;
        $this->distributor = $distributor;
    }

    public static function create( Collection $input)
    {
        return new self(
            (new OrderId($input->orderHistoryId) ),
            (new ReceivedId($input->receivingHId) ),
            (new ReturnId($input->returnHistoryID) ),
            (new DateYearMonthDayHourMinutesSecond($input->registrationTime) ),
            [],
            (Hospital::create($input) ),
            (Division::create($input) ),
            (Distributor::create($input) ),
        );
    }

    public function getReturnId()
    {
        return $this->returnId;
    }

    public function setReturnItems(array $items)
    {
        $items = array_map(function(ReturnItem $item)
        {
            return $item;
        },$items);

        return new self(
            $this->orderId,
            $this->receivedId,
            $this->returnId,
            $this->registDate,
            $items,
            $this->hospital,
            $this->division,  
            $this->distributor, 
        );
    }

    public function addReturnItem(ReturnItem $returnItem)
    {
        $tmp = $this->returnItems;
        $tmp[] = $returnItem;
        return $this->setReturnItems($tmp);
    }

    public function totalAmount(){
        $num = 0;
        foreach($this->returnItems as $item)
        {
            $num += $item->price();
        }
        return $num;
    }

    public function itemCount(){
        $array = [];
        foreach($this->returnItems as $item)
        {
            $array[] = $item->getInHospitalItemId()->value();
        }
        return count(array_unique($array));
    }
    public function toArray() 
    {
        return [
            'orderId' => $this->orderId->value(),
            'receivedId' => $this->receivedId->value(),
            'returnId' => $this->returnId->value(),
            'registDate' => $this->registDate->value(),
            'returnItems' =>  array_map(function(ReturnItem $v){
                return $v->toArray();
            },$this->returnItems),
            'hospital' => $this->hospital->toArray(),
            'itemCount' => $this->itemCount(),
            'totalAmount' => $this->totalAmount(),
            'division' => $this->division->toArray(),
            'distributor' => $this->distributor->toArray(),
        ];
    } 
}