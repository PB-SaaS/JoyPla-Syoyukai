<?php

namespace JoyPla\Enterprise\Models;

use JoyPla\Enterprise\TmpModels\TmpValueObject;
//Modelは機能単位なのでclass名はItemList。

class ItemList
{
    private ItemListId $itemListId;
    private HospitalId $hospitalId;
    private ?DivisionId $divisionId;
    private ?string $itemListName;
    private ?string $usableStatus;
    private ?string $itemsNumber;

    private array $items = [];
    private array $option = [];

    public function __construct(
        ItemListId $itemListId,
        HospitalId $hospitalId,
        ?DivisionId $divisionId = null,
        ?string $itemListName = null,
        ?string $usableStatus = null,
        ?string $itemsNumber
    ) {
        $this->itemListId = $itemListId;
        $this->hospitalId = $hospitalId;
        $this->divisionId = $divisionId;
        $this->itemListName = $itemListName;
        $this->usableStatus = $usableStatus;
        $this->itemsNumber = $itemsNumber;
    }

    public static function init(
        string $itemListId,
        string $hospitalId,
        ?string $divisionId = null,
        ?string $itemListName = null,
        ?string $usableStatus = null,
        ?string $itemsNumber = '0'
    ) {
        return new self(
            new ItemListId($itemListId),
            new HospitalId($hospitalId),
            $divisionId ? new DivisionId($divisionId) : null,
            $itemListName,
            $usableStatus,
            $itemsNumber
        );
    }

    public function getItemListId()
    {
        return $this->itemListId;
    }

    public function getDivisionId()
    {
        return $this->divisionId;
    }

    public function getUsableStatus()
    {
        return $this->usableStatus;
    }

    public function getItemListName()
    {
        return $this->itemListName;
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
        $this->items = array_map(function (ItemListRow $itemListRow) {
            return $itemListRow;
        }, $items);
    }

    public function setItemListName(string $itemListName)
    {
        return $this->itemListName = $itemListName;
    }

    public function addItem(ItemListRow $itemListRow)
    {
        $this->items[] = $itemListRow;
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
            'itemListId' => $this->itemListId->value(),
            'hospitalId' => $this->hospitalId->value(),
            'divisionId' => $this->divisionId ? $this->divisionId->value() : '',
            'itemListName' => $this->itemListName ? $this->itemListName : '',
            'usableStatus' => $this->usableStatus,
            'itemsNumber' => $this->itemsNumber ? $this->itemsNumber : '0',
            'items' => array_map(function (ItemListRow $itemListRow) {
                return $itemListRow->toArray();
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
