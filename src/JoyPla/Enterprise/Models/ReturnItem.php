<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class ReturnItem 
{
    private ReturnId $returnId;
    private OrderItemId $orderItemId;
    private ReceivedItemId $receivedItemId;
    private ReturnItemId $returnItemId;
    private InHospitalItemId $inHospitalItemId;
    private Item $item;
    private HospitalId $hospitalId;
    private Division $division;
    private Distributor $distributor;
    private Quantity $quantity;
    private Price $price;
    private ReturnQuantity $returnQuantity;
    private Lot $lot;
    private string $itemImage;

    public function __construct(
        ReturnId $returnId,
        OrderItemId $orderItemId,
        ReceivedItemId $receivedItemId,
        ReturnItemId $returnItemId,
        InHospitalItemId $inHospitalItemId,
        Item $item,
        HospitalId $hospitalId,
        Division $division,
        Distributor $distributor,
        Quantity $quantity,
        Price $price,
        ReturnQuantity $returnQuantity,
        Lot $lot,
        $itemImage
        )
    {
        $this->returnId = $returnId;
        $this->orderItemId = $orderItemId;
        $this->receivedItemId = $receivedItemId;
        $this->returnItemId = $returnItemId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->item = $item;
        $this->hospitalId = $hospitalId;
        $this->division = $division;
        $this->distributor = $distributor;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->returnQuantity = $returnQuantity;
        $this->lot = $lot;
        $this->itemImage = $itemImage;
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getDivision()
    {
        return $this->division;
    }

    public function getReturnQuantity()
    {
        return $this->returnQuantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getLot()
    {
        return $this->lot;
    }

    public function price()
    {
        return $this->price->value() * $this->returnQuantity->value();
    }

    public static function create( Collection $input )
    {
        return new self(
            (new ReturnId($input->returnHistoryID) ),
            (new OrderItemId($input->orderCNumber) ),
            (new ReceivedItemId($input->receivingNumber) ),
            (new ReturnItemId($input->returnID) ),
            (new InHospitalItemId($input->inHospitalItemId) ),
            (Item::create($input) ),
            (new HospitalId($input->hospitalId) ),
            (Division::create($input) ),
            (Distributor::create($input) ),
            (Quantity::create($input) ),
            (new Price($input->price) ),
            (new ReturnQuantity((int)$input->totalReturnCount)) ,
            (Lot::create($input) ),
            $input->inItemImage,
        );
    }

    public function getInHospitalItemId()
    {
        return $this->inHospitalItemId;
    }

    public function toArray()
    {
        return [
            'returnId' => $this->returnId->value(),
            'orderItemId' => $this->orderItemId->value(),
            'receivedItemId' => $this->receivedItemId->value(),
            'returnItemId' => $this->returnItemId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'item' => $this->item->toArray(),
            'hospitalId' => $this->hospitalId->value(),
            'division' => $this->division->toArray(),
            'distributor' => $this->distributor->toArray(),
            'quantity' => $this->quantity->toArray(),
            'price' => $this->price->value(),
            'returnPrice' => $this->price(),
            'returnQuantity' => $this->returnQuantity->value(),
            'lot' => $this->lot->toArray(),
            'itemImage' => $this->itemImage
        ];
    }
}