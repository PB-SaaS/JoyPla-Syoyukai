<?php

namespace JoyPla\Enterprise\Models;

use Exception;

class AcceptanceItem
{
    private AcceptanceId $acceptanceId;
    private AcceptanceItemId $acceptanceItemId;
    private InHospitalItemId $inHospitalItemId;
    private ?LotDate $lotDate;
    private ?LotNumber $lotNumber;
    private int $quantity;
    private string $quantityUnit;
    private string $itemUnit;
    private Price $price;
    private UnitPrice $unitPrice;
    private int $acceptanceCount = 0;
    private int $payoutCount = 0;
    private array $optional = [];

    public function __construct(
        AcceptanceId $acceptanceId,
        AcceptanceItemId $acceptanceItemId,
        InHospitalItemId $inHospitalItemId,
        ?LotDate $lotDate,
        ?LotNumber $lotNumber,
        int $quantity,
        string $quantityUnit,
        string $itemUnit,
        Price $price,
        UnitPrice $unitPrice,
        int $acceptanceCount,
        int $payoutCount
    ) {
        $this->acceptanceId = $acceptanceId;
        $this->acceptanceItemId = $acceptanceItemId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->lotDate = $lotDate;
        $this->lotNumber = $lotNumber;
        $this->quantity = $quantity;
        $this->quantityUnit = $quantityUnit;
        $this->itemUnit = $itemUnit;
        $this->price = $price;
        $this->unitPrice = $unitPrice;
        $this->acceptanceCount = $acceptanceCount;
        $this->payoutCount = $payoutCount;
    }

    public function getAcceptanceItemId(){
        return $this->acceptanceItemId;
    }
    public function changeAcceptanceCount(int $count){
        if($this->acceptanceCount < $count)
        {
            throw new Exception('Cannot set more than the current quantity',500);
        }
        $this->acceptanceCount = $count;
        return $this;
    }
    public function setPayoutCount(int $count){
        $this->payoutCount = $count;
        return $this;
    }
    public function addPayoutCount(int $count){
        if($this->payoutCount + $count > $this->acceptanceCount)
        {
            throw new Exception('Cannot set more than the current quantity',500);
        }
        $this->payoutCount += $count;
        return $this;
    }


    public function getLotNumber(){
        return $this->lotNumber;
    }
    public function getLotDate(){
        return $this->lotDate;
    }
    public function __set($key , $value)
    {
        $this->optional[$key] = $value;
        return $this;
    }

    public function getInHospitalItemId(){
        return $this->inHospitalItemId;
    }

    public function getAcceptanceQuantity()
    {
        return $this->acceptanceCount;
    }
    public function getPayoutQuantity()
    {
        return $this->payoutCount;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
    public function getQuantityUnit()
    {
        return $this->quantityUnit;
    }
    public function getItemUnit()
    {
        return $this->itemUnit;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }


    public function toArray()
    {
        return [
            'acceptanceId' => $this->acceptanceId->value(),
            'acceptanceItemId' => $this->acceptanceItemId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'lotDate' => $this->lotDate->value(),
            'lotNumber' => $this->lotNumber->value(),
            'quantity' => $this->quantity,
            'quantityUnit' => $this->quantityUnit,
            'itemUnit' => $this->itemUnit,
            'price' => $this->price->value(),
            'unitPrice' => $this->unitPrice->value(),
            'acceptanceCount' => $this->acceptanceCount,
            'payoutCount' => $this->payoutCount,
        ];
    }
}
