<?php

namespace JoyPla\Enterprise\Models;

use JoyPla\Enterprise\TmpModels\TmpValueObject;
//Modelは機能単位なのでclass名はStocktakingList。

class StocktakingList
{
    private StocktakingListId $stocktakingListId;
    private HospitalId $hospitalId;
    private ?DivisionId $divisionId;
    private ?string $stocktakingListName;
    private ?string $itemsNumber;

    private array $items = [];
    private array $option = [];

    public function __construct(
        StocktakingListId $stocktakingListId,
        HospitalId $hospitalId,
        ?DivisionId $divisionId = null,
        ?string $stocktakingListName = null,
        ?string $itemsNumber
    ) {
        $this->stocktakingListId = $stocktakingListId;
        $this->hospitalId = $hospitalId;
        $this->divisionId = $divisionId;
        $this->stocktakingListName = $stocktakingListName;
        $this->itemsNumber = $itemsNumber;
    }

    public static function init(
        string $stocktakingListId,
        string $hospitalId,
        ?string $divisionId = null,
        ?string $stocktakingListName = null,
        ?string $itemsNumber = '0'
    ) {
        return new self(
            $stocktakingListId ? new StocktakingListId($stocktakingListId) : null,
            new HospitalId($hospitalId),
            $divisionId ? new DivisionId($divisionId) : null,
            $stocktakingListName,
            $itemsNumber
        );
    }

    public function getStocktakingListId()
    {
        return $this->stocktakingListId;
    }

    public function getDivisionId()
    {
        return $this->divisionId;
    }

    public function getStocktakingListName()
    {
        return $this->stocktakingListName;
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
        $this->items = array_map(function (StocktakingListRow $stocktakingListRow) {
            return $stocktakingListRow;
        }, $items);
    }

    public function setStocktakingListName(string $stocktakingListName)
    {
        return $this->stocktakingListName = $stocktakingListName;
    }

    public function setStocktakingListId(StocktakingListId $stocktakingListId)
    {
        return $this->stocktakingListId = $stocktakingListId;
    }

    public function addItem(StocktakingListRow $stocktakingListRow)
    {
        $this->items[] = $stocktakingListRow;
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
            'stocktakingListId' => $this->stocktakingListId->value(),
            'hospitalId' => $this->hospitalId->value(),
            'divisionId' => $this->divisionId ? $this->divisionId->value() : '',
            'stocktakingListName' => $this->stocktakingListName ? $this->stocktakingListName : '',
            'itemsNumber' => $this->itemsNumber ? $this->itemsNumber : '0',
            'items' => array_map(function (StocktakingListRow $stocktakingListRow) {
                return $stocktakingListRow->toArray();
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
