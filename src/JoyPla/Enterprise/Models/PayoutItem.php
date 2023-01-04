<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class PayoutItem
{
    private PayoutHId $payoutHId;
    private string $payoutId;
    private InHospitalItemId $inHospitalItemId;
    private Item $item;
    private HospitalId $hospitalId;
    private Division $sourceDivision;
    private Division $targetDivision;
    private Quantity $quantity;
    private Price $price;
    private UnitPrice $unitPrice;
    private PayoutQuantity $PayoutQuantity;
    private Lot $lot;

    public function __construct(
        PayoutHId $payoutHId,
        string $payoutId,
        InHospitalItemId $inHospitalItemId,
        Item $item,
        HospitalId $hospitalId,
        Division $sourceDivision,
        Division $targetDivision,
        Quantity $quantity,
        Price $price,
        UnitPrice $unitPrice,
        PayoutQuantity $PayoutQuantity,
        Lot $lot
    ) {
        $this->payoutHId = $payoutHId;
        $this->payoutId = $payoutId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->item = $item;
        $this->hospitalId = $hospitalId;
        $this->sourceDivision = $sourceDivision;
        $this->targetDivision = $targetDivision;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->unitPrice = $unitPrice;
        $this->payoutQuantity = $PayoutQuantity;
        $this->lot = $lot;
    }

    public static function create(Collection $input)
    {
        return new PayoutItem(
            (new PayoutHId($input->payoutHId)),
            $input->payoutId,
            (new InHospitalItemId($input->inHospitalItemId)),
            (Item::create($input)),
            (new HospitalId($input->hospitalId)),
            (Division::create($input->sourceDivision)),
            (Division::create($input->targetDivision)),
            (Quantity::create($input)),
            (new Price($input->price)),
            (new UnitPrice($input->unitPrice)),
            (new PayoutQuantity((int)$input->payoutQuantity)),
            (Lot::create($input))
        );
    }

    public function getItem()
    {
        return $this->item;
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getPrice()
    {
        return $this->price;
    }


    public function getPayoutId()
    {
        return $this->payoutId;
    }

    public function getSourceDivision()
    {
        return $this->sourceDivision;
    }

    public function getTargetDivision()
    {
        return $this->targetDivision;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function equalDivisions(Division $sourceDivision, Division $targetDivision)
    {
        return (($this->sourceDivision->getDivisionId()->value() === $sourceDivision->getDivisionId()->value()) &&
            ($this->targetDivision->getDivisionId()->value() === $targetDivision->getDivisionId()->value()));
    }

    public function setPayoutId(string $payoutId)
    {
        return new PayoutItem(
            $this->payoutHId,
            $payoutId,
            $this->inHospitalItemId,
            $this->item,
            $this->hospitalId,
            $this->sourceDivision,
            $this->targetDivision,
            $this->quantity,
            $this->price,
            $this->unitPprice,
            $this->payoutQuantity,
            $this->lot
        );
    }


    public function price()
    {
        return (float)$this->unitPrice->value() * (float)$this->payoutQuantity->value();
    }

    public function getInHospitalItemId()
    {
        return $this->inHospitalItemId;
    }

    public function getPayoutQuantity()
    {
        return $this->PayoutQuantity;
    }


    public function setPayoutQuantity(PayoutQuantity $payoutQuantity)
    {
        return new PayoutItem(
            $this->payoutHId,
            $this->payoutId,
            $this->inHospitalItemId,
            $this->item,
            $this->hospitalId,
            $this->sourceDivision,
            $this->targetDivision,
            $this->quantity,
            $this->price,
            $this->unitPprice,
            $payoutQuantity,
            $this->lot
        );
    }

    public function addPayoutQuantity(PayoutQuantity $quantity)
    {
        return $this->setPayoutQuantity($this->payoutQuantity->add((int)$quantity->value()));
    }


    public function toArray()
    {
        return [
            'PayoutId' => $this->PayoutId->value(),
            'PayoutItemId' => $this->PayoutItemId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'item' => $this->item->toArray(),
            'hospitalId' => $this->hospitalId->value(),
            'division' => $this->division->toArray(),
            'distributor' => $this->distributor->toArray(),
            'quantity' => $this->quantity->toArray(),
            'price' => $this->price->value(),
            'PayoutQuantity' => $this->PayoutQuantity->value(),
            'PayoutPrice' => $this->price(),
        ];
    }
}
