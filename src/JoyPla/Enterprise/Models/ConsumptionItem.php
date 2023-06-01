<?php

namespace JoyPla\Enterprise\Models;

use Collection;
class ConsumptionItem 
{
    private int $id;
    private ConsumptionId $consumptionId;
    private InHospitalItemId $inHospitalItemId;
    private Item $item;
    private HospitalId $hospitalId;
    private Division $division;
    private Quantity $quantity;
    private Price $price;
    private UnitPrice $unitPrice;
    private Lot $lot;
    private int $consumptionQuantity;
    private bool $lotManagement;
    private string $itemImage;

    public function __construct(
        int $id,
        ConsumptionId $consumptionId,
        InHospitalItemId $inHospitalItemId,
        Item $item,
        HospitalId $hospitalId,
        Division $division,
        Quantity $quantity,
        Price $price,
        UnitPrice $unitPrice,
        Lot $lot,
        int $consumptionQuantity,
        bool $lotManagement,
        $itemImage = ""
        )
    {
        $this->id = $id;
        $this->consumptionId = $consumptionId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->item = $item;
        $this->hospitalId = $hospitalId;
        $this->division = $division;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->unitPrice = $unitPrice;
        $this->lot = $lot;
        $this->consumptionQuantity = $consumptionQuantity;
        $this->lotManagement = $lotManagement;
        $this->itemImage = ($itemImage)? $itemImage : "";
    }

    public static function create( Collection $input )
    {
        return new ConsumptionItem(
            $input->id,
            (new ConsumptionId($input->billingNumber) ),
            (new InHospitalItemId($input->inHospitalItemId) ),
            (Item::create($input) ),
            (new HospitalId($input->hospitalId) ),
            (Division::create($input) ),
            (Quantity::create($input) ),
            (new Price($input->price) ),
            (new UnitPrice($input->unitPrice) ),
            (Lot::create($input) ),
            (int) $input->billingQuantity ,
            (int) $input->lotManagement ,
            $input->inItemImage,
        );
    }

    public function getId(): int {
        return $this->id;
     }

    public function getDivision()
    {
        return $this->division;
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function equalDivision(Division $division)
    {
        return $this->division === $division;
    }

    public function price(){
        return $this->unitPrice->value() * $this->consumptionQuantity;
    }

    public function getConsumptionQuantity()
    {
        return $this->consumptionQuantity;
    }
    
    public function getInHospitalItemId(){
        return $this->inHospitalItemId;
    }

    public function getLot()
    {
        return $this->lot;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'consumptionId' => $this->consumptionId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'item' => $this->item->toArray(),
            'hospitalId' => $this->hospitalId->value(),
            'division' => $this->division->toArray(),
            'quantity' => $this->quantity->toArray(),
            'price' => $this->price->value(),
            'unitPrice' => $this->unitPrice->value(),
            'price' => $this->price->value(),
            'lot' => $this->lot->toArray(),
            'consumptionQuantity' => $this->consumptionQuantity,
            'consumptionPrice' => $this->price(),
            'lotManagement' => $this->lotManagement,
            'itemImage' => $this->itemImage,
        ];
    }
}