<?php

namespace JoyPla\Enterprise\Models;

use Collection;
class Consumption
{
    private ConsumptionId $consumptionId;
    private DateYearMonthDay $consumptionDate;
    private array $consumptionItems;
    private Hospital $hospital;
    private Division $division;
    private ConsumptionStatus $consumptionStatus;

    public function __construct(
        ConsumptionId $consumptionId,
        DateYearMonthDay $consumptionDate,
        array $consumptionItems,
        Hospital $hospital,
        Division $division,
        ConsumptionStatus $consumptionStatus
        )
    {
        
        $this->consumptionId = $consumptionId;
        $this->consumptionDate = $consumptionDate;
        $this->consumptionItems = array_map(function(ConsumptionItem $v){
            return $v;
        },$consumptionItems);
        $this->hospital = $hospital;
        $this->division = $division;
        $this->consumptionStatus = $consumptionStatus;
    }

    public static function create( Collection $input )
    {
        return new Consumption(
            (new ConsumptionId($input->billingNumber) ),
            (new DateYearMonthDay($input->billingDate) ),
            [],
            (Hospital::create($input) ),
            (Division::create($input) ),
            (new ConsumptionStatus($input->billingStatus) )
        );
    }
    public function getConsumptionId()
    {
        return $this->consumptionId;
    }
    
    public function getConsumptionItems()
    {
        return $this->consumptionItems;
    }

    public function getDivision()
    {
        return $this->division;
    }

    public function equalDivision(Division $division)
    {
        return $this->division === $division;
    }

    public function totalAmount(){
        $num = 0;
        foreach($this->consumptionItems as $item)
        {
            $num += $item->price();
        }
        return $num;
    }

    
    public function itemCount(){
        $array = [];
        foreach($this->consumptionItems as $item)
        {
            $array[] = $item->getInHospitalItemId()->value();
        }
        return count(array_unique($array));
    }

    public function addConsumptionItem(ConsumptionItem $item)
    {
        $items = $this->consumptionItems;
        $items[] = $item;
        return $this->setConsumptionItems($items);
    }

    public function setConsumptionItems(array $consumptionItems)
    {
        $consumptionItems = array_map(function(ConsumptionItem $v){
            return $v;
        },$consumptionItems);

        return new Consumption(
            $this->consumptionId,
            $this->consumptionDate,
            $consumptionItems,
            $this->hospital,
            $this->division,
            $this->consumptionStatus,
        );
    }

    public function toArray()
    {
        return [
            'consumptionId' => $this->consumptionId->value(),
            'consumptionDate' => $this->consumptionDate->value(),
            'consumptionItems' =>  array_map(function(ConsumptionItem $v){
                return $v->toArray();
            },$this->consumptionItems),
            'hospital' => $this->hospital->toArray(),
            'division' => $this->division->toArray(),
            'consumptionStatus' => $this->consumptionStatus->value(),
            'consumptionStatusToString' => $this->consumptionStatus->toString(),
            'totalAmount' => $this->totalAmount(),
            'itemCount' => $this->itemCount(),
        ];
    } 
}