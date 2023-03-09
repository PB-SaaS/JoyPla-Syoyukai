<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class Stock
{
    private int $id;
    private DateYearMonthDayHourMinutesSecond $registDate;
    private DateYearMonthDayHourMinutesSecond $updateDate;
    private InHospitalItemId $inHospitalItemId;
    private Item $item;
    private Price $price;
    private Quantity $quantity;
    private Division $division;
    private Distributor $distributor;
    private string $rackName;
    private DateYearMonthDayHourMinutesSecond $lastInventoryDate;
    private int $inventory;
    private int $orderedQuantity;
    private int $constant;
    private string $itemImage;

    public function __construct(
        int $id,
        DateYearMonthDayHourMinutesSecond $registDate,
        DateYearMonthDayHourMinutesSecond $updateDate,
        InHospitalItemId $inHospitalItemId,
        Item $item,
        Price $price,
        Quantity $quantity,
        Division $division,
        Distributor $distributor,
        string $rackName,
        DateYearMonthDayHourMinutesSecond $lastInventoryDate,
        int $inventory,
        int $orderedQuantity,
        int $constant,
        $itemImage
    ) {
        $this->id = $id;
        $this->registDate = $registDate;
        $this->updateDate = $updateDate;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->item = $item;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->division = $division;
        $this->distributor = $distributor;
        $this->rackName = $rackName;
        $this->lastInventoryDate = $lastInventoryDate;
        $this->inventory = $inventory;
        $this->orderedQuantity = $orderedQuantity;
        $this->constant = $constant;
        $this->itemImage = $itemImage;
    }

    public static function create(Collection $input)
    {
        return new Stock(
            (int) $input->recordId,
            new DateYearMonthDayHourMinutesSecond($input->registrationTime),
            new DateYearMonthDayHourMinutesSecond($input->updateTime),
            new InHospitalItemId($input->inHospitalItemId),
            Item::create($input),
            new Price($input->price),
            Quantity::create($input),
            Division::create($input),
            Distributor::create($input),
            ((string) $input->rackName),
            new DateYearMonthDayHourMinutesSecond($input->invFinishTime),
            (int) $input->stockQuantity,
            (int) $input->orderWithinCount,
            (int) $input->constantByDiv,
            $input->inItemImage
        );
    }

    public function calcNumberOfOrdersRequired()
    {
        if ($this->constant - $this->calcPlannedInventory() < 0) {
            return 0;
        }

        return $this->constant - $this->calcPlannedInventory();
    }

    public function calcPlannedInventory()
    {
        if ($this->inventory + $this->orderedQuantity < 0) {
            return 0;
        }
        return $this->inventory + $this->orderedQuantity;
    }

    public function calcOrderQuantity()
    {
        if (
            $this->quantity->getQuantityNum() == 0 ||
            $this->calcNumberOfOrdersRequired() < 0
        ) {
            return 0;
        }
        return ceil(
            $this->calcNumberOfOrdersRequired() /
                $this->quantity->getQuantityNum()
        );
    }

    public function getInHospitalItemId()
    {
        return $this->inHospitalItemId;
    }

    public function getInventoryQuantity()
    {
        return $this->inventory;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'registDate' => $this->registDate->value(),
            'updateDate' => $this->updateDate->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'item' => $this->item->toArray(),
            'price' => $this->price->value(),
            'quantity' => $this->quantity->toArray(),
            'division' => $this->division->toArray(),
            'distributor' => $this->distributor->toArray(),
            'rackName' => $this->rackName,
            'lastInventoryDate' => $this->lastInventoryDate->value(),
            'inventory' => $this->inventory,
            'orderedQuantity' => $this->orderedQuantity,
            'constant' => $this->constant,
            'itemImage' => $this->itemImage,
            'orderQuantity' => $this->calcOrderQuantity(),
            'plannedInventory' => $this->calcPlannedInventory(),
            'numberOfOrdersRequired' => $this->calcNumberOfOrdersRequired(),
        ];
    }
}
