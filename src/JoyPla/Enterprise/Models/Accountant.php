<?php

namespace JoyPla\Enterprise\Models;

use JoyPla\Enterprise\TmpModels\TmpValueObject;

class Accountant
{
    private ?DateYearMonthDay $accountantDate;
    private AccountantId $accountantId;
    private HospitalId $hospitalId;
    private ?DivisionId $divisionId;
    private ?DistributorId $distributorId;
    private ?string $orderId;
    private ?string $receivedId;
    private array $items = [];
    private array $option = [];

    public function __construct(
        AccountantId $accountantId,
        ?DateYearMonthDay $accountantDate = null,
        HospitalId $hospitalId,
        ?DivisionId $divisionId = null,
        ?DistributorId $distributorId = null,
        ?string $orderId = null,
        ?string $receivedId = null
    ) {
        $this->accountantId = $accountantId;
        $this->accountantDate = $accountantDate;
        $this->hospitalId = $hospitalId;
        $this->divisionId = $divisionId;
        $this->distributorId = $distributorId;
        $this->orderId = $orderId;
        $this->receivedId = $receivedId;
    }

    public static function init(
        string $accountantDate = null,
        string $hospitalId,
        ?string $divisionId = null,
        ?string $distributorId = null,
        ?string $orderId = null,
        ?string $receivedId = null
    ) {
        return new self(
            AccountantId::generate(),
            $accountantDate ? new DateYearMonthDay($accountantDate) : null,
            new HospitalId($hospitalId),
            $divisionId ? new DivisionId($divisionId) : null,
            $distributorId ? new DistributorId($distributorId) : null,
            $orderId,
            $receivedId
        );
    }

    public function getAccountantId()
    {
        return $this->accountantId;
    }

    public function getDivisionId()
    {
        return $this->divisionId;
    }

    public function __get($field)
    {
        return $this->option[$field];
    }

    public function __set($field, $value)
    {
        return $this->option[$field] = $value;
    }

    public function setItems(array $items)
    {
        $this->items = array_map(function (AccountantItem $item) {
            return $item;
        }, $items);
    }

    public function addItem(AccountantItem $item)
    {
        $this->items[] = $item;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function totalAmount()
    {
        $num = 0;

        foreach ($this->items as $item) {
            $num += $item->subTotal();
        }

        return $num;
    }

    public function toArray()
    {
        $result = [
            'accountantDate' => $this->accountantDate->value(),
            'accountantId' => $this->accountantId->value(),
            'hospitalId' => $this->hospitalId->value(),
            'divisionId' => $this->divisionId ? $this->divisionId->value() : '',
            'distributorId' => $this->distributorId
                ? $this->distributorId->value()
                : '',
            'orderId' => $this->orderId ?? '',
            'receivedId' => $this->receivedId ?? '',
            'totalAmount' => $this->totalAmount(),
            'items' => array_map(function (AccountantItem $item) {
                return $item->toArray();
            }, $this->items),
        ];

        foreach ($this->option as $field => $value) {
            if (is_object($value)) {
                $result[$field] = (array) $value;
            } else {
                $result[$field] = $value;
            }
        }

        return $result;
    }
}
