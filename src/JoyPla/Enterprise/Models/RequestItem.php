<?php

namespace JoyPla\Enterprise\Models;

use Collection;

class RequestItem
{
    private RequestId $requestId;
    private RequestHId $requestHId;
    private InHospitalItemId $inHospitalItemId;
    private Item $item;
    private HospitalId $hospitalId;
    private Division $sourceDivision;
    private Division $targetDivision;
    private RequestQuantity $requestQuantity;
    private RequestType $requestType;
    private Quantity $quantity;
    private Price $price;
    private UnitPrice $unitPrice;

    public function __construct(
        RequestId $requestId,
        RequestHId $requestHId,
        InHospitalItemId $inHospitalItemId,
        Item $item,
        HospitalId $hospitalId,
        Division $sourceDivision,
        Division $targetDivision,
        RequestQuantity $requestQuantity,
        RequestType $requestType,
        Quantity $quantity,
        Price $price,
        UnitPrice $unitPrice
    ) {
        $this->requestId = $requestId;
        $this->requestHId = $requestHId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->item = $item;
        $this->hospitalId = $hospitalId;
        $this->sourceDivision = $sourceDivision;
        $this->targetDivision = $targetDivision;
        $this->requestQuantity = $requestQuantity;
        $this->requestType = $requestType;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->unitPrice = $unitPrice;
    }

    public static function create(Collection $input)
    {
        return new RequestItem(
            (new RequestId($input->requestId)),
            (new RequestHId($input->requestHId)),
            (new InHospitalItemId($input->inHospitalItemId)),
            (Item::create($input)),
            (new HospitalId($input->hospitalId)),
            (Division::create($input->sourceDivision)),
            (Division::create($input->targetDivision)),
            (new RequestQuantity($input->requestQuantity)),
            (new RequestType($input->requestType)),
            (Quantity::create($input)),
            (new Price($input->price)),
            (new UnitPrice($input->unitPrice))
        );
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function getSourceDivision()
    {
        return $this->sourceDivision;
    }

    public function getTargetDivision()
    {
        return $this->targetDivision;
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function equalDivisions(Division $sourceDivision, Division $targetDivision)
    {
        return (($this->sourceDivision->getDivisionId()->value() === $sourceDivision->getDivisionId()->value()) &&
            ($this->targetDivision->getDivisionId()->value() === $targetDivision->getDivisionId()->value()));
    }

    public function price()
    {
        return $this->unitPrice->value() * $this->requestQuantity->value();
    }

    public function getRequestQuantity()
    {
        return $this->requestQuantity;
    }

    public function getInHospitalItemId()
    {
        return $this->inHospitalItemId;
    }

    public function toArray()
    {
        return [
            'requestId' => $this->requestId->value(),
            'requestHId' => $this->requestHId->value(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'item' => $this->item->toArray(),
            'hospitalId' => $this->hospitalId->value(),
            'sourceDivision' => $this->sourceDivision->toArray(),
            'targetDivision' => $this->targetDivision->toArray(),
            'requestQuantity' => $this->requestQuantity->value(),
            'requestType' => $this->requestType->value(),
            'quantity' => $this->quantity->toArray(),
            'price' => $this->price->value(),
            'unitPrice' => $this->unitPrice->value()
        ];
    }
}
