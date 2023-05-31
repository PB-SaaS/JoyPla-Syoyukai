<?php

namespace JoyPla\Enterprise\Models;

class Acceptance
{
    private AcceptanceId $acceptanceId;
    private DateYearMonthDay $acceptanceDate;
    private HospitalId $hospitalId;
    private DivisionId $sourceDivisionId;
    private DivisionId $targetDivisionId;
    private bool $isComplete = false;
    private array $items = [];
    private array $optional = [];

    public function __construct(
        AcceptanceId $acceptanceId,
        DateYearMonthDay $acceptanceDate = null,
        HospitalId $hospitalId,
        DivisionId $sourceDivisionId = null,
        DivisionId $targetDivisionId = null
    ) {
        $this->acceptanceId = $acceptanceId;
        $this->acceptanceDate = $acceptanceDate;
        $this->hospitalId = $hospitalId;
        $this->sourceDivisionId = $sourceDivisionId;
        $this->targetDivisionId = $targetDivisionId;
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }
    public function setItems(array $items)
    {
        $item = array_map(function(AcceptanceItem $item) {
            return $item;
        }, $items);


        $this->items = $item;
        return $this;
    }

    public function changeAcceptanceCount(AcceptanceItemId $itemId, int $count){
        foreach( $this->items as $key => $item ){
            if($item->getAcceptanceItemId()->equal($itemId->value())){
                $this->items[$key] = $item->changeAcceptanceCount($count);
            }
        }
        return $this;
    }

    public function addItem(AcceptanceItem $item)
    {
        $this->items[] = $item;
        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function __set($key , $value)
    {
        $this->optional[$key] = $value;
        return $this;
    }

    public function equalDivisions(DivisionId $sourceDivisionId , DivisionId $targetDivisionId)
    {
        return $sourceDivisionId->equal($sourceDivisionId->value()) && $targetDivisionId->equal($targetDivisionId->value());
    }

    public function getAcceptanceId()
    {
        return $this->acceptanceId;
    }

    public function getSourceDivisionId()
    {
        return $this->sourceDivisionId;
    }
    public function getTargetDivisionId()
    {
        return $this->targetDivisionId;
    }

    public function status()
    {
        $acceptanceCount = 0;
        $payoutCount = 0;

        foreach($this->items as $item){
            $acceptanceCount += $item->getAcceptanceQuantity();
            $payoutCount += $item->getPayoutQuantity();
        }
        if($acceptanceCount > 0 && $payoutCount === 0)
        {
            return 1;//未入庫
        } else if($acceptanceCount > 0 && $acceptanceCount === $payoutCount){
            return 3;//入庫
        }else if($acceptanceCount > 0 && $acceptanceCount !== $payoutCount){
            return 2;//一部入庫
        }
    }

    public function toArray()
    {
        return [
            'acceptanceId' => $this->acceptanceId->value(),
            'acceptanceDate' => $this->acceptanceDate->value(),
            'hospitalId' => $this->hospitalId->value(),
            'sourceDivisionId' => $this->sourceDivisionId->value(),
            'targetDivisionId' => $this->targetDivisionId->value(),
            'isComplete' => ($this->status() === 3)? '1' : '0',
            'items' => array_map(function(AcceptanceItem $item){
                return $item->toArray();
            },$this->items)
        ];
    }
}
