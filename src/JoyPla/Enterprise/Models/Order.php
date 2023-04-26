<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class Order
{
    private OrderId $orderId;
    private DateYearMonthDayHourMinutesSecond $registDate;
    private DateYearMonthDayHourMinutesSecond $orderDate;
    private array $orderItems;
    private Hospital $hospital;
    private Division $division;
    private Distributor $distributor;
    private OrderStatus $orderStatus;
    private OrderAdjustment $adjustment;
    private TextArea512Bytes $orderComment;
    private TextArea512Bytes $distributorComment;
    private string $orderUserName;
    private int $receivedTarget;
    private bool $sentFlag = false;

    public function __construct(
        OrderId $orderId,
        DateYearMonthDayHourMinutesSecond $registDate,
        DateYearMonthDayHourMinutesSecond $orderDate,
        array $orderItems,
        Hospital $hospital,
        Division $division,
        Distributor $distributor,
        OrderStatus $orderStatus,
        OrderAdjustment $adjustment,
        TextArea512Bytes $orderComment,
        TextArea512Bytes $distributorComment,
        string $orderUserName = '',
        int $receivedTarget = 1,
        bool $sentFlag = false
    ) {
        $this->orderId = $orderId;
        $this->registDate = $registDate;
        $this->orderDate = $orderDate;
        $this->orderItems = array_map(function (OrderItem $v) {
            return $v;
        }, $orderItems);
        $this->hospital = $hospital;
        $this->division = $division;
        $this->distributor = $distributor;
        $this->orderStatus = $orderStatus;
        $this->adjustment = $adjustment;
        $this->orderComment = $orderComment;
        $this->distributorComment = $distributorComment;
        $this->orderUserName = $orderUserName;
        $this->receivedTarget = $receivedTarget;
        $this->sentFlag = $sentFlag;
    }

    public static function create(Collection $input)
    {
        return new Order(
            new OrderId($input->orderNumber),
            new DateYearMonthDayHourMinutesSecond($input->registrationTime),
            new DateYearMonthDayHourMinutesSecond($input->orderTime),
            [],
            Hospital::create($input),
            Division::create($input),
            Distributor::create($input),
            new OrderStatus($input->orderStatus),
            new OrderAdjustment($input->adjustment),
            new TextArea512Bytes($input->ordercomment),
            new TextArea512Bytes($input->distrComment),
            $input->ordererUserName,
            $input->receivingTarget != '2' ? 1 : 2, //1	大倉庫  2	発注部署
            $input->sentFlag == '1'
        );
    }

    public function getHospital()
    {
        return $this->hospital;
    }

    public function isMinus()
    {
        foreach ($this->orderItems as $item) {
            if ($item->isMinus()) {
                return true;
            }
        }
        return false;
    }

    public function isPlus()
    {
        foreach ($this->orderItems as $item) {
            if ($item->isPlus()) {
                return true;
            }
        }
        return false;
    }

    public function isExistOrderItemId(OrderItemId $orderItemId)
    {
        foreach ($this->orderItems as $item) {
            if ($item->getOrderItemId()->equal($orderItemId->value())) {
                return true;
            }
        }

        return false;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function searchOrderItem(OrderItemId $orderItemId)
    {
        foreach ($this->orderItems as $item) {
            if ($item->getOrderItemId()->equal($orderItemId->value())) {
                return $item;
            }
        }

        return null;
    }

    public function getOrderItems()
    {
        return $this->orderItems;
    }
    public function getDivision()
    {
        return $this->division;
    }

    public function getDistributor()
    {
        return $this->distributor;
    }

    public function getOrderStatus()
    {
        return $this->orderStatus;
    }
    public function approval()
    {
        if ($this->orderStatus->value() !== OrderStatus::UnOrdered) {
            throw new Exception('This slip has been approved.', 422);
        }

        return new Order(
            $this->orderId,
            $this->registDate,
            new DateYearMonthDayHourMinutesSecond(date('Y-m-d H:i:s')),
            $this->orderItems,
            $this->hospital,
            $this->division,
            $this->distributor,
            new OrderStatus(OrderStatus::OrderCompletion),
            $this->adjustment,
            $this->orderComment,
            $this->distributorComment,
            $this->orderUserName,
            $this->receivedTarget,
            $this->sentFlag
        );
    }

    private function setOrderStatus(OrderStatus $orderStatus)
    {
        return new Order(
            $this->orderId,
            $this->registDate,
            $this->orderDate,
            $this->orderItems,
            $this->hospital,
            $this->division,
            $this->distributor,
            $orderStatus,
            $this->adjustment,
            $this->orderComment,
            $this->distributorComment,
            $this->orderUserName,
            $this->receivedTarget,
            $this->sentFlag
        );
    }

    public function updateOrderStatus()
    {
        $count = count($this->orderItems);
        $result = [];
        $result[OrderItemReceivedStatus::NotInStock] = 0;
        $result[OrderItemReceivedStatus::PartOfTheCollectionIsIn] = 0;
        $result[OrderItemReceivedStatus::ReceivingIsComplete] = 0;
        foreach ($this->orderItems as $item) {
            $result[$item->getOrderItemReceivedStatus()->value()]++;
        }

        if ($count === $result[OrderItemReceivedStatus::NotInStock]) {
            $status = [
                OrderStatus::UnOrdered,
                OrderStatus::OrderCompletion,
                OrderStatus::OrderFinished,
                OrderStatus::DeliveryDateReported,
            ];
            $fkey = array_search($this->orderStatus->value(), $status, true);

            if ($fkey !== false) {
                return $this->setOrderStatus(new OrderStatus($status[$fkey]));
            }

            return $this->setOrderStatus(
                new OrderStatus(OrderStatus::UnOrdered)
            );
        }

        if ($count === $result[OrderItemReceivedStatus::ReceivingIsComplete]) {
            return $this->setOrderStatus(
                new OrderStatus(OrderStatus::ReceivingIsComplete)
            );
        }

        return $this->setOrderStatus(
            new OrderStatus(OrderStatus::PartOfTheCollectionIsIn)
        );
    }

    public function equalOrderSlip(Division $division, Distributor $distributor)
    {
        return $this->division->getDivisionId()->value() ===
            $division->getDivisionId()->value() &&
            $this->distributor->getDistributorId()->value() ===
                $distributor->getDistributorId()->value();
    }

    public function totalAmount()
    {
        $num = 0;
        foreach ($this->orderItems as $item) {
            $num += $item->price();
        }
        return $num;
    }

    public function deleteItem(OrderItemId $orderItemId)
    {
        $tmp = $this->orderItems;
        foreach ($tmp as $key => $orderItem) {
            if ($orderItem->getOrderItemId()->equal($orderItemId->value())) {
                unset($tmp[$key]);
                break;
            }
        }
        return $this->setOrderItems(array_values($tmp));
    }

    public function itemCount()
    {
        $array = [];
        foreach ($this->orderItems as $item) {
            $array[] = $item->getInHospitalItemId()->value();
        }
        return count(array_unique($array));
    }

    public function addOrderItem(OrderItem $item)
    {
        $items = $this->orderItems;
        $flag = false;
        foreach ($items as $key => $orderItem) {
            if (
                $orderItem
                    ->getInHospitalItemId()
                    ->equal($item->getInHospitalItemId()->value())
            ) {
                $flag = true;
                $items[$key] = $orderItem->addOrderQuantity(
                    $item->getOrderQuantity()
                );
                break;
            }
        }
        if (!$flag) {
            $items[] = $item;
        }
        return $this->setOrderItems($items);
    }

    public function addOrderItemQuantity(
        OrderItemId $orderItemId,
        OrderQuantity $orderQuantity
    ) {
        $tmp = $this->orderItems;
        foreach ($tmp as $key => $val) {
            if ($val->getOrderItemId()->equal($orderItemId->value())) {
                $tmp[$key] = $val->addOrderQuantity($orderQuantity);
                break;
            }
        }

        return $this->setOrderItems($tmp);
    }

    public function getReceivedTarget()
    {
        return $this->receivedTarget;
    }

    public function getOrderDate()
    {
        return $this->orderDate;
    }

    public function setOrderComment(TextArea512Bytes $comment)
    {
        return new Order(
            $this->orderId,
            $this->registDate,
            $this->orderDate,
            $this->orderItems,
            $this->hospital,
            $this->division,
            $this->distributor,
            $this->orderStatus,
            $this->adjustment,
            $comment,
            $this->distributorComment,
            $this->orderUserName,
            $this->receivedTarget,
            $this->sentFlag
        );
    }

    public function setOrderItems(array $orderItems)
    {
        $orderId = $this->orderId;
        $orderItems = array_map(function (OrderItem $v) use ($orderId) {
            $tmp = $v;
            $tmp = $tmp->setOrderId($orderId);
            return $tmp;
        }, $orderItems);

        return new Order(
            $this->orderId,
            $this->registDate,
            $this->orderDate,
            $orderItems,
            $this->hospital,
            $this->division,
            $this->distributor,
            $this->orderStatus,
            $this->adjustment,
            $this->orderComment,
            $this->distributorComment,
            $this->orderUserName,
            $this->receivedTarget,
            $this->sentFlag
        );
    }

    public function setAdjustment(OrderAdjustment $adjustment)
    {
        return new Order(
            $this->orderId,
            $this->registDate,
            $this->orderDate,
            $this->orderItems,
            $this->hospital,
            $this->division,
            $this->distributor,
            $this->orderStatus,
            $adjustment,
            $this->orderComment,
            $this->distributorComment,
            $this->orderUserName,
            $this->receivedTarget,
            $this->sentFlag
        );
    }

    public function toArray()
    {
        return [
            'orderId' => $this->orderId->value(),
            'registDate' => $this->registDate->value(),
            'orderDate' => $this->orderDate->value(),
            'orderItems' => array_map(function (OrderItem $v) {
                return $v->toArray();
            }, $this->orderItems),
            'hospital' => $this->hospital->toArray(),
            'division' => $this->division->toArray(),
            'distributor' => $this->distributor->toArray(),
            'orderStatus' => $this->orderStatus->value(),
            'orderStatusToString' => $this->orderStatus->toString(),
            'totalAmount' => $this->totalAmount(),
            'itemCount' => $this->itemCount(),
            'adjustment' => $this->adjustment->value(),
            'adjustmentToString' => $this->adjustment->toString(),
            'orderComment' => $this->orderComment->value(),
            'distributorComment' => $this->distributorComment->value(),
            'orderUserName' => $this->orderUserName,
            'receivedTarget' => $this->receivedTarget,
            'sentFlag' => $this->sentFlag,
        ];
    }
}
