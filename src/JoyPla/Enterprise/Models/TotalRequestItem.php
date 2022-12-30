<?php

namespace JoyPla\Enterprise\Models;

use Collection;

class TotalRequestItem
{
    private string $recordId;
    private Division $division;
    private InHospitalItemId $inHospitalItemId;
    private Item $item;
    private Quantity $quantity;
    private bool $lotManagement;
    private int $stockQunatity;
    private RequestQuantity $requestQuantity;
    private array $totalRequests;

    public function __construct(
        string $recordId,
        Division $division,
        InHospitalItemId $inHospitalItemId,
        Item $item,
        Quantity $quantity,
        bool $lotManagement,
        int $stockQunatity,
        RequestQuantity $requestQuantity,
        array $totalRequests
    ) {
        $this->recordId = $recordId;
        $this->division = $division;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->item = $item;
        $this->quantity = $quantity;
        $this->lotManagement = $lotManagement;
        $this->stockQuantity = $stockQunatity;
        $this->requestQuantity = $requestQuantity;
        $this->totalRequests = array_map(function (TotalRequest $v) {
            return $v;
        }, $totalRequests);
    }

    public static function create(Collection $input)
    {
        return new TotalRequestItem(
            ((string) $input->recordId),
            (Division::create($input)),
            (new InHospitalItemId($input->inHospitalItemId)),
            (Item::create($input)),
            (Quantity::create($input)),
            (($input->lotManagement === 1)),
            (int)$input->stockQuantity,
            (new RequestQuantity($input->requestQuantity)),
            []
        );
    }

    public function getRecordId()
    {
        return $this->recordId;
    }

    public function getDivision()
    {
        return $this->division;
    }

    public function getInHospitalItemId()
    {
        return $this->inHospitalItemId;
    }

    public function getStockQunatity()
    {
        return $this->stockQunatity;
    }

    public function getRequestQuantity()
    {
        return $this->requestQuantity;
    }

    public function getTotalRequests()
    {
        return $this->totalRequests;
    }

    public function countTotalRequests()
    {
        return count($this->totalRequests);
    }

    public function addTotalRequest(TotalRequest $totalRequest)
    {
        $totalRequests = $this->totalRequests;
        $totalRequests[] = $totalRequest;
        return $this->setTotalRequest($totalRequests);
    }

    public function setTotalRequest(array $totalRequests)
    {
        $totalRequests = array_map(function (TotalRequest $v) {
            return $v;
        }, $totalRequests);

        return new TotalRequestItem(
            $this->recordId,
            $this->division,
            $this->inHospitalItemId,
            $this->item,
            $this->quantity,
            $this->lotManagement,
            $this->stockQuantity,
            $this->requestQuantity,
            $totalRequests
        );
    }

    public function toArray()
    {
        return [
            'recordId' => $this->recordId,
            'division' => $this->division->toArray(),
            'inHospitalItemId' => $this->inHospitalItemId->value(),
            'item' => $this->item->toArray(),
            'quantity' => $this->quantity->toArray(),
            'stockQuantity' => $this->stockQuantity,
            'lotManagement' => (bool)$this->lotManagement,
            'requestQuantity' => (int)$this->requestQuantity->value(),
            'totalRequests' => array_map(function (TotalRequest $v) {
                return $v->toArray();
            }, $this->totalRequests),
            'countTotalRequests' => $this->countTotalRequests()
        ];
    }
}
