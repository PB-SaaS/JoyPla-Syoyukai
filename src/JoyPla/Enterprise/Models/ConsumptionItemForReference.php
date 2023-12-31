<?php

namespace JoyPla\Enterprise\Models;

use Collection;

class ConsumptionItemForReference
{
    private ConsumptionId $consumptionId;
    private InHospitalItemId $inHospitalItemId;
    private Item $item;
    private HospitalId $hospitalId;
    private Division $division;
    private Quantity $quantity;
    private Price $price;
    private UnitPrice $unitPrice;
    private int $consumptionQuantity;
    private string $itemImage;
    private string $priceNotice;
    private bool $lotManagement = false;

    public function __construct(
        ConsumptionId $consumptionId,
        InHospitalItemId $inHospitalItemId,
        Item $item,
        HospitalId $hospitalId,
        Division $division,
        Quantity $quantity,
        Price $price,
        UnitPrice $unitPrice,
        int $consumptionQuantity,
        $itemImage = "",
        $priceNotice = "",
        $lotManagement = ""
    ) {
        $this->consumptionId = $consumptionId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->item = $item;
        $this->hospitalId = $hospitalId;
        $this->division = $division;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->unitPrice = $unitPrice;
        $this->consumptionQuantity = $consumptionQuantity;
        $this->itemImage = ($itemImage) ? $itemImage : "";
        $this->priceNotice = ($priceNotice) ? $priceNotice : "";
        $this->lotManagement = ($lotManagement) ? $lotManagement : "";
    }

    public static function create(Collection $input)
    {
        return new ConsumptionItemForReference(
            (new ConsumptionId($input->billingNumber)),
            (new InHospitalItemId($input->inHospitalItemId)),
            (Item::create($input)),
            (new HospitalId($input->hospitalId)),
            (Division::create($input)),
            (Quantity::create($input)),
            (new Price($input->price)),
            (new UnitPrice($input->unitPrice)),
            (int)$input->billingQuantity,
            $input->inItemImage,
            $input->priceNotice,
            $input->lotManagement == '1'
        );
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

    public function price()
    {
        return $this->unitPrice->value() * $this->consumptionQuantity;
    }

    public function getConsumptionQuantity()
    {
        return $this->consumptionQuantity;
    }

    public function getInHospitalItemId()
    {
        return $this->inHospitalItemId;
    }

    public function toArray()
    {
        return [
            'consumptionId' => $this->consumptionId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'item' => $this->item->toArray(),
            'hospitalId' => $this->hospitalId->value(),
            'division' => $this->division->toArray(),
            'quantity' => $this->quantity->toArray(),
            'price' => $this->price->value(),
            'unitPrice' => $this->unitPrice->value(),
            'price' => $this->price->value(),
            'consumptionQuantity' => $this->consumptionQuantity,
            'consumptionPrice' => $this->price(),
            'itemImage' => $this->itemImage,
            'priceNotice' => $this->priceNotice,
            'lotManagement' => $this->lotManagement
        ];
    }
}
