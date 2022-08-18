<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class ReceivedItem 
{
    private OrderItemId $orderItemId;
    private ReceivedId $receivedId;
    private ReceivedItemId $receivedItemId;
    private InHospitalItemId $inHospitalItemId;
    private Item $item;
    private HospitalId $hospitalId;
    private Division $division;
    private Distributor $distributor;
    private Quantity $quantity;
    private Price $price;
    private float $adjustmentAmount;
    private ReceivedQuantity $receivedQuantity;
    private ReturnQuantity $returnQuantity;
    private Lot $lot;
    private Redemption $redemption;
    private string $itemImage;

    public function __construct(
        OrderItemId $orderItemId,
        ReceivedId $receivedId,
        ReceivedItemId $receivedItemId,
        InHospitalItemId $inHospitalItemId,
        Item $item,
        HospitalId $hospitalId,
        Division $division,
        Distributor $distributor,
        Quantity $quantity,
        Price $price,
        float $adjustmentAmount,
        ReceivedQuantity $receivedQuantity,
        ReturnQuantity $returnQuantity,
        Lot $lot,
        Redemption $redemption,
        $itemImage
        )
    {
        $this->orderItemId = $orderItemId;
        $this->receivedId = $receivedId;
        $this->receivedItemId = $receivedItemId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->item = $item;
        $this->hospitalId = $hospitalId;
        $this->division = $division;
        $this->distributor = $distributor;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->adjustmentAmount = $adjustmentAmount;
        $this->receivedQuantity = $receivedQuantity;
        $this->returnQuantity = $returnQuantity;
        $this->lot = $lot;
        $this->redemption = $redemption;
        $this->itemImage = $itemImage;
    }

    public static function create( Collection $input )
    {
        return new self(
            (new OrderItemId($input->orderCNumber) ),
            (new ReceivedId($input->receivingHId) ),
            (new ReceivedItemId($input->receivingNumber) ),
            (new InHospitalItemId($input->inHospitalItemId) ),
            (Item::create($input) ),
            (new HospitalId($input->hospitalId) ),
            (Division::create($input) ),
            (Distributor::create($input) ),
            (Quantity::create($input) ),
            (new Price($input->price) ),
            ((float)$input->adjAmount),
            (new ReceivedQuantity((int)$input->receivingCount)) ,
            (new ReturnQuantity((int)$input->totalReturnCount)) ,
            (Lot::create($input) ),
            (Redemption::create($input) ),
            $input->inItemImage,
        );
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }
 
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    public function getInHospitalItemId()
    {
        return $this->inHospitalItemId;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function getDivision()
    {
        return $this->division;
    }

    public function getDistributor()
    {
        return $this->distributor;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getReceivedQuantity()
    {
        return $this->receivedQuantity;
    }
    
    public function getPrice()
    {
        return $this->price;
    }
    
    public function getLot()
    {
        return $this->lot;
    }
    
    public function getItemImage()
    {
        return $this->itemImage;
    }
    
    public function getReceivedItemId()
    {
        return $this->receivedItemId;
    }

    public function price(){
        return (float)$this->price->value() * (float)$this->receivedQuantity->value();
    }

    public function getRedemption()
    {
        return $this->redemption;
    }

    public function priceAfterAdjustment()
    {
        return $this->price() + (float)$this->adjustmentAmount;
    }

    public function addReturnQuantity( ReturnQuantity $returnQuantity )
    {
        if( $this->receivedQuantity->value() < ( $this->returnQuantity->value() + $returnQuantity->value() ) )
        {
            throw new Exception('The number of returns is high compared to the number of items received.',422);
        }
        return new self(
            $this->orderItemId,
            $this->receivedId,
            $this->receivedItemId ,
            $this->inHospitalItemId,
            $this->item,
            $this->hospitalId,
            $this->division,
            $this->distributor,
            $this->quantity,
            $this->price,
            $this->adjustmentAmount,
            $this->receivedQuantity,
            ($this->returnQuantity->add($returnQuantity)),
            $this->lot,
            $this->redemption,
            $this->itemImage
        );
    }

    public function toArray()
    {
        return [
            'orderItemId' => $this->orderItemId->value(),
            'receivedId' => $this->receivedId->value(),
            'receivedItemId' => $this->receivedItemId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'item' => $this->item->toArray(),
            'hospitalId' => $this->hospitalId->value(),
            'division' => $this->division->toArray(),
            'distributor' => $this->distributor->toArray(),
            'quantity' => $this->quantity->toArray(),
            'price' => $this->price->value(),
            'receivedPrice' => $this->price(),
            'returnQuantity' => $this->returnQuantity->value(),
            'priceAfterAdjustment' => $this->priceAfterAdjustment(),
            'adjustmentAmount' => $this->adjustmentAmount,
            'receivedQuantity' => $this->receivedQuantity->value(),
            'lot' => $this->lot->toArray(),
            'redemption' => $this->redemption->toArray(),
            'itemImage' => $this->itemImage
        ];
    }
}