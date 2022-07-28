<?php

namespace JoyPla\Enterprise\Models;

use Collection;

class InHospitalItem {

    private InHospitalItemId $inHospitalItemId;
    private Item $item;
    private HospitalId $hospitalId;
    private DistributorId $distributorId;
    private Quantity $quantity;
    private UnitPrice $unitPrice;
    private Price $price;
    private PriceId $priceId;
    private bool $lotManagement;
    private string $itemImage;
    

    public function __construct(
        InHospitalItemId $inHospitalItemId,
        Item $item,
        HospitalId $hospitalId,
        DistributorId $distributorId,
        Quantity $quantity,
        UnitPrice $unitPrice,
        Price $price,
        PriceId $priceId,
        bool $lotManagement,
        string $itemImage = ""
        )
    {
        $this->inHospitalItemId = $inHospitalItemId;
        $this->item = $item;
        $this->hospitalId = $hospitalId;
        $this->distributorId = $distributorId;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->price = $price;
        $this->priceId = $priceId;
        $this->lotManagement = $lotManagement;
        $this->itemImage = $itemImage;
    }

    public static function create( Collection $input )
    {
       return new InHospitalItem(
            ( new InHospitalItemId($input->inHospitalItemId) ),
            ( Item::create($input) ),
            ( new HospitalId($input->hospitalId) ),
            ( new DistributorId($input->distributorId) ),
            ( Quantity::create($input) ),
            ( new UnitPrice($input->unitPrice) ),
            ( new Price($input->price) ),
            ( new PriceId($input->priceId) ),
            ( ($input->lotManagement === 1) ),
            ( $input->inItemImage ),
       );
    }

    public function getInHospitalItemId()
    {
        return $this->inHospitalItemId;
    }

    public function getDistributorId()
    {
        return $this->distributorId;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
    
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }
    
    public function getPrice()
    {
        return $this->price;
    }
    
    public function getPriceId()
    {
        return $this->priceId;
    }


    public function isLotManagement() : bool
    {
        return $this->lotManagement;
    }

    public function toArray()
    {
        return [
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'item' => $this->item->toArray(),
            'hospitalId' => $this->hospitalId->value(),
            'quantity' => $this->quantity->toArray(),
            'itemImage' => $this->itemImage,
        ];
    }
}