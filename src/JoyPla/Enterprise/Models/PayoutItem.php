<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class PayoutItem
{
    private PayoutHistoryId $payoutHistoryId;
    private PayoutItemId $payoutItemId;
    private InHospitalItemId $inHospitalItemId;
    private ItemId $itemId;
    private HospitalId $hospitalId;
    private int $quantity;
    private string $quantityUnit;
    private string $itemUnit;
    private Price $price;
    private UnitPrice $unitPrice;
    private PayoutQuantity $payoutQuantity;
    private LotDate $lotDate;
    private LotNumber $lotNumber;
    private bool $lotManagement;
    private CardId $cardId;
    
    private int $payoutType = 1;

    public function __construct(
        PayoutHistoryId $payoutHistoryId,
        PayoutItemId $payoutItemId,
        InHospitalItemId $inHospitalItemId,
        ItemId $itemId,
        HospitalId $hospitalId,
        int $quantity,
        string $quantityUnit,
        string $itemUnit,
        Price $price,
        UnitPrice $unitPrice,
        PayoutQuantity $payoutQuantity,
        LotDate $lotDate,
        LotNumber $lotNumber,
        bool $lotManagement,
        CardId $cardId,
        int $payoutType
    ) {
        $this->payoutHistoryId = $payoutHistoryId;
        $this->payoutItemId = $payoutItemId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->itemId = $itemId;
        $this->hospitalId = $hospitalId;
        $this->quantity = $quantity;
        $this->quantityUnit = $quantityUnit;
        $this->itemUnit = $itemUnit;
        $this->price = $price;
        $this->unitPrice = $unitPrice;
        $this->payoutQuantity = $payoutQuantity;
        $this->lotDate = $lotDate;
        $this->lotNumber = $lotNumber;
        $this->lotManagement = $lotManagement;
        $this->cardId = $cardId;
        $this->payoutType = $payoutType;
    }
    public function getItemId()
    {
        return $this->itemId;
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getPayoutItemId()
    {
        return $this->payoutItemId;
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


    public function getCardId()
    {
        return $this->cardId;
    }

    public function setPayoutId(PayoutItemId $payoutItemId)
    {
        $this->payoutItemId = $payoutItemId;
        return $this;
    }

    public function price()
    {
        return (float) $this->unitPrice->value() *
            (float) $this->payoutQuantity->value();
    }

    public function getInHospitalItemId()
    {
        return $this->inHospitalItemId;
    }

    public function getPayoutQuantity()
    {
        return $this->payoutQuantity;
    }

    public function getLotDate()
    {
        return $this->lotDate;
    }
    public function getLotNumber()
    {
        return $this->lotNumber;
    }


    public function getLotManagement(): bool
    {
        return (bool) $this->lotManagement;
    }

    public function getCard()
    {
        return $this->cardId;
    }

    public function setPayoutQuantity(PayoutQuantity $payoutQuantity)
    {
        $this->payoutQuantity = $payoutQuantity;
        return $this;
    }

    public function addPayoutQuantity(PayoutQuantity $quantity)
    {
        return $this->setPayoutQuantity($this->payoutQuantity->add($quantity));
    }

    public function toArray()
    {
        return [
            'payoutHistoryId' => $this->payoutHistoryId->value(),
            'payoutItemId' => $this->payoutItemId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'itemId' => $this->itemId->value(),
            'hospitalId' => $this->hospitalId->value(),
            'quantity' => $this->quantity,
            'quantityUnit' => $this->quantityUnit,
            'itemUnit' => $this->itemUnit,
            'price' => $this->price->value(),
            'unitPrice' => $this->unitPrice->value(),
            'payoutQuantity' => $this->payoutQuantity->value(),
            'payoutAmount' => $this->price(),
            'lotDate' => $this->lotDate->value(),
            'lotNumber' => $this->lotNumber->value(),
            'lotManagement' => $this->lotManagement,
            'cardId' => $this->cardId->value(),
            'payoutType'=>$this->payoutType
        ];
    }
}
