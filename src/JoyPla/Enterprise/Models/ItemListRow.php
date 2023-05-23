<?php

namespace JoyPla\Enterprise\Models;

//Modelは機能単位なのでclass名はItemListRow。itemとかinHospitalItemだと並び順に対応できないので。
class ItemListRow
{
    private int $index = 0;
    private ItemListId $itemListId;
    private ?ItemListRowId $itemListRowId;
    private ?ItemId $itemId;
    private InHospitalItemId $inHospitalItemId;
    private ?MakerName $makerName = null;
    private ?ItemName $itemName;
    private ?ItemCode $itemCode = null;
    private ?ItemStandard $itemStandard = null;
    private ?Jancode $itemJANCode = null;
    private int $quantity = 0;
    private string $quantityUnit = '';
    private string $itemUnit = '';
    private string $itemLabelBarcode = '';
    private ?DistributorId $distributorId;
    private ?HospitalId $hospitalId;
    private string $distributorName = '';

    private array $option = [];

    public function __construct(
        int $index,
        ItemListId $itemListId,
        ?ItemListRowId $itemListRowId,
        ItemId $itemId = null,
        InHospitalItemId $inHospitalItemId = null,
        ?MakerName $makerName = null,
        ?ItemName $itemName,
        ?ItemCode $itemCode = null,
        ?ItemStandard $itemStandard = null,
        ?Jancode $itemJANCode = null,
        ?int $quantity = 0,
        ?string $quantityUnit = '',
        ?string $itemUnit = '',
        ?string $itemLabelBarcode = null,
        ?DistributorId $distributorId,
        ?HospitalId $hospitalId,
        ?string $distributorName = ''
    ) {
        $this->index = $index;
        $this->itemListId = $itemListId;
        $this->itemListRowId = $itemListRowId;
        $this->itemId = $itemId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->makerName = $makerName;
        $this->itemName = $itemName;
        $this->itemCode = $itemCode;
        $this->itemStandard = $itemStandard;
        $this->itemJANCode = $itemJANCode;
        $this->quantity = $quantity;
        $this->quantityUnit = $quantityUnit;
        $this->itemUnit = $itemUnit;
        $this->itemLabelBarcode = $itemLabelBarcode;
        $this->distributorId = $distributorId;
        $this->hospitalId = $hospitalId;
        $this->distributorName = $distributorName;
    }

    public static function init(
        int $index,
        string $itemListId,
        ?string $itemListRowId = null,
        string $itemId = null,
        string $inHospitalItemId = null,
        ?string $makerName = null,
        ?string $itemName = null,
        ?string $itemCode = null,
        ?string $itemStandard = null,
        ?string $itemJANCode = null,
        ?int $quantity = 0,
        ?string $quantityUnit = '',
        ?string $itemUnit = '',
        ?string $itemLabelBarcode = null,
        ?string $distributorId,
        ?string $hospitalId,
        ?string $distributorName = ''
    ) {
        return new self(
            $index,
            new ItemListId($itemListId),
            $itemListRowId ? new ItemListRowId($itemListRowId) : null,
            $itemId ? new ItemId($itemId) : null,
            $inHospitalItemId ? new InHospitalItemId($inHospitalItemId) : null,
            $makerName ? new MakerName($makerName) : null,
            new ItemName($itemName),
            $itemCode ? new ItemCode($itemCode) : null,
            $itemStandard ? new ItemStandard($itemStandard) : null,
            $itemJANCode ? new Jancode($itemJANCode) : null,
            $quantity,
            $quantityUnit,
            $itemUnit,
            $itemLabelBarcode,
            $distributorId ? new DistributorId($distributorId) : null,
            $hospitalId ? new HospitalId($hospitalId) : null,
            $distributorName
        );
    }

    public function getItemListId()
    {
        return $this->itemListId;
    }

    public function __get($field)
    {
        return $this->option[$field];
    }

    public function __set($field, $value)
    {
        return $this->option[$field] = $value;
    }

    public function toArray()
    {
        $result = [];
        $result['index'] = $this->index;
        $result['itemListId'] = $this->itemListId->value();
        $result['itemListRowId'] = $this->itemListRowId ? $this->itemListRowId->value() : null;
        $result['itemId'] = $this->itemId ? $this->itemId->value() : null;
        $result['inHospitalItemId'] = $this->inHospitalItemId ? $this->inHospitalItemId->value() : null;
        $result['makerName'] = $this->makerName
            ? $this->makerName->value()
            : null;
        $result['itemName'] = $this->itemName ? $this->itemName->value() : null;
        $result['itemCode'] = $this->itemCode ? $this->itemCode->value() : null;
        $result['itemStandard'] = $this->itemStandard
            ? $this->itemStandard->value()
            : null;
        $result['itemJANCode'] = $this->itemJANCode
            ? $this->itemJANCode->value()
            : null;
        $result['quantity'] = $this->quantity;
        $result['quantityUnit'] = $this->quantityUnit;
        $result['itemUnit'] = $this->itemUnit;
        $result['itemLabelBarcode'] = $this->itemLabelBarcode;
        $result['distributorId'] = $this->distributorId ? $this->distributorId->value() : null;
        $result['hospitalId'] = $this->hospitalId ? $this->hospitalId->value() : null;
        $result['distributorName'] = $this->distributorName;

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
