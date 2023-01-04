<?php

namespace JoyPla\Enterprise\Models;

use Collection;
class ItemPrice 
{
    private ItemId $itemId;
    private Quantity $quantity;
    private Price $price;
    private UnitPrice $unitPrice;

    public function __construct(
            ItemId $itemId,
            Quantity $quantity,
            Price $price,
            UnitPrice $unitPrice
        )
    {
        
        $this->ItemId = $itemId;
        $this->Quantity = $quantity;
        $this->Price = $price;
        $this->UnitPrice = $unitPrice;
    }

    public static function create( Collection $input )
    {
        return new Item(
            (new ItemId($input->itemId) ),
            (new Quantity($input->quantity) ),
            (new Price($input->price) ),
            (new UnitPrice($input->unitPrice) )
        );
    }

    public function getItemId()
    {
        return $this->itemId;
    }

    public function getQuantity()
    {
        return $this->quantity;
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
            'ItemId' => $this->itemId->value(),
            'Quantity' => $this->quantity->value(),
            'Price' => $this->price->value(),
            'UnitPrice' => $this->unitPrice->value(),
        ];
    }
}