<?php

namespace JoyPla\Enterprise\Models;

use Collection;
class Item 
{

    private ItemId $itemId;
    private ItemName $itemName;
    private ItemCode $itemCode;
    private ItemStandard $itemStandard;
    private Jancode $itemJANCode;
    private MakerName $makerName;
    private SerialNo $serialNo;
    private CatalogNo $catalogNo;

    public function __construct(
        ItemId $itemId,
        ItemName $itemName,
        ItemCode $itemCode,
        ItemStandard $itemStandard,
        Jancode $itemJANCode,
        MakerName $makerName,
        SerialNo $serialNo,
        CatalogNo $catalogNo
        )
    {
        
        $this->itemId = $itemId;
        $this->itemName = $itemName;
        $this->itemCode = $itemCode;
        $this->itemStandard = $itemStandard;
        $this->itemJANCode = $itemJANCode;
        $this->makerName = $makerName;
        $this->serialNo = $serialNo;
        $this->catalogNo = $catalogNo;
    }

    public static function create( Collection $input )
    {
        return new Item(
            (new ItemId($input->itemId) ),
            (new ItemName($input->itemName) ),
            (new ItemCode($input->itemCode) ),
            (new ItemStandard($input->itemStandard) ),
            (new Jancode($input->itemJANCode) ),
            (new MakerName($input->makerName) ),
            (new SerialNo($input->serialNo) ),
            (new CatalogNo($input->catalogNo) ),
        );
    }

    public function getItemId()
    {
        return $this->itemId;
    }

    public function getItemName()
    {
        return $this->itemName;
    }
    
    public function getItemCode()
    {
        return $this->itemCode;
    }
    
    public function getItemStandard()
    {
        return $this->itemStandard;
    }
    
    public function getItemJANCode()
    {
        return $this->itemJANCode;
    }
    
    public function getMakerName()
    {
        return $this->makerName;
    }

    public function getSerialNo()
    {
        return $this->serialNo;
    }
    
    public function getCatalogNo()
    {
        return $this->catalogNo;
    }

    public function toArray()
    {
        return [
            'itemId' => $this->itemId->value(),
            'itemName' => $this->itemName->value(),
            'itemCode' => $this->itemCode->value(),
            'itemStandard' => $this->itemStandard->value(),
            'itemJANCode' => $this->itemJANCode->value(),
            'makerName' => $this->makerName->value(),
            'serialNo' => $this->serialNo->value(),
            'catalogNo' => $this->catalogNo->value(),
        ];
    }
}