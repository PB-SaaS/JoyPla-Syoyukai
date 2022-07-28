<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class OrderItem 
{
    private OrderId $orderId;
    private OrderItemId $orderItemId;
    private InHospitalItemId $inHospitalItemId;
    private Item $item;
    private HospitalId $hospitalId;
    private Division $division;
    private Distributor $distributor;
    private Quantity $quantity;
    private Price $price;
    private OrderQuantity $orderQuantity;
    private ReceivedQuantity $receivedQuantity;
    private bool $lotManagement;

    public function __construct(
        OrderId $orderId,
        OrderItemId $orderItemId,
        InHospitalItemId $inHospitalItemId,
        Item $item,
        HospitalId $hospitalId,
        Division $division,
        Distributor $distributor,
        Quantity $quantity,
        Price $price,
        OrderQuantity $orderQuantity,
        ReceivedQuantity $receivedQuantity,
        bool $lotManagement,
        $itemImage = ""
        
        )
    {
        $this->orderId = $orderId;
        $this->orderItemId = $orderItemId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->item = $item;
        $this->hospitalId = $hospitalId;
        $this->division = $division;
        $this->distributor = $distributor;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->orderQuantity = $orderQuantity;
        $this->receivedQuantity = $receivedQuantity;
        $this->lotManagement = $lotManagement;
        $this->itemImage = ($itemImage)? $itemImage : "";
    }

    public static function create( Collection $input )
    {
        return new OrderItem(
            (new OrderId($input->orderNumber) ),
            (new OrderItemId($input->orderCNumber) ),
            (new InHospitalItemId($input->inHospitalItemId) ),
            (Item::create($input) ),
            (new HospitalId($input->hospitalId) ),
            (Division::create($input) ),
            (Distributor::create($input) ),
            (Quantity::create($input) ),
            (new Price($input->price) ),
            (new OrderQuantity((int)$input->orderQuantity)) ,
            (new ReceivedQuantity((int)$input->receivingNum)) ,
            (int) $input->lotManagement ,
            $input->inItemImage,
        );
    }

    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    public function getDivision()
    {
        return $this->division;
    }

    public function getDistributor()
    {
        return $this->distributor;
    }

    public function equalDivision(Division $division)
    {
        return $this->division === $division;
    }

    public function equalOrderSlip(Division $division , Distributor $distributor)
    {
        return (
            $this->division->getDivisionId()->value() === $division->getDivisionId()->value() && 
            $this->distributor->getDistributorId()->value() === $distributor->getDistributorId()->value()
        );
    }
    
    public function setOrderId(OrderId $orderId)
    {
        $this->orderId = $orderId;
    }
    
    public function isMinus()
    {
        return $this->price() < 0 ;
    }

    public function isPlus()
    {
        return $this->price() >= 0 ;
    }

    public function price(){
        return (float)$this->price->value() * (float)$this->orderQuantity->value();
    }
    
    public function getInHospitalItemId(){
        return $this->inHospitalItemId;
    }

    public function getOrderQuantity()
    {
        return $this->orderQuantity;
    }

    public function getReceivedQuantity()
    {
        return $this->receivedQuantity;
    }

    public function setOrderQuantity(OrderQuantity $orderQuantity)
    {
        return new OrderItem(
            $this->orderId,
            $this->orderItemId,
            $this->inHospitalItemId,
            $this->item,
            $this->hospitalId,
            $this->division,
            $this->distributor,
            $this->quantity,
            $this->price,
            $orderQuantity,
            $this->receivedQuantity,
            $this->lotManagement,
        );
    }

    public function addOrderQuantity(OrderQuantity $quantity)
    {
        return $this->setOrderQuantity(  $this->orderQuantity->add((int)$quantity->value()));
    }

    public function getOrderItemReceivedStatus()
    {
        if($this->receivedQuantity->value() == $this->orderQuantity->value())
        {
            return new OrderItemReceivedStatus(OrderItemReceivedStatus::ReceivingIsComplete);
        }

        if($this->receivedQuantity->value() == 0)
        {
            return new OrderItemReceivedStatus(OrderItemReceivedStatus::NotInStock);
        }
        
        return new OrderItemReceivedStatus(OrderItemReceivedStatus::PartOfTheCollectionIsIn);
    }

    public function toArray()
    {
        return [
            'orderId' => $this->orderId->value(),
            'orderItemId' => $this->orderItemId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'item' => $this->item->toArray(),
            'hospitalId' => $this->hospitalId->value(),
            'division' => $this->division->toArray(),
            'distributor' => $this->distributor->toArray(),
            'quantity' => $this->quantity->toArray(),
            'price' => $this->price->value(),
            'orderQuantity' => $this->orderQuantity->value(),
            'receivedQuantity' => $this->receivedQuantity->value(),
            'orderItemReceivedStatus' => $this->getOrderItemReceivedStatus()->value(),
            'orderItemReceivedStatusToString' => $this->getOrderItemReceivedStatus()->toString(),
            'orderPrice' => $this->price(),
            'lotManagement' => $this->lotManagement,
            'itemImage' => $this->itemImage
        ];
    }
}