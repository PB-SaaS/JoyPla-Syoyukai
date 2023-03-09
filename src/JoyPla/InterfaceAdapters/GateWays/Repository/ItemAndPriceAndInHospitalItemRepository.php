<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use framework\SpiralConnecter\SpiralDB;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class ItemAndPriceAndInHospitalItemRepository implements
    ItemAndPriceAndInHospitalItemRepositoryInterface
{
    public function saveToArray(
        $tenantId,
        $hospitalId,
        array $input,
        array $attr = []
    ) {
        $itemCreateArray = [
            'registrationTime' => 'now',
            'updateTime' => 'now',
            'tenantId' => $tenantId,
            'hospitalId' => $hospitalId,
            'itemName' => $input['itemName'],
            'category' => $input['category'],
            'smallCategory' => $input['smallCategory'],
            'itemCode' => $input['itemCode'],
            'itemStandard' => $input['itemStandard'],
            'itemJANCode' => $input['itemJANCode'],
            'makerName' => $input['makerName'],
            'catalogNo' => $input['catalogNo'],
            'serialNo' => $input['serialNo'],
            'lotManagement' => $input['lotManagement'],
            'officialFlag' => $input['officialFlag'],
            'officialprice' => $input['officialprice'],
            'officialpriceOld' => $input['officialpriceOld'],
            'quantity' => $input['quantity'],
            'quantityUnit' => $input['quantityUnit'],
            'itemUnit' => $input['itemUnit'],
            'minPrice' => $input['minPrice'],
        ];

        $itemCreateData = ModelRepository::getItemInstance()->create(
            $itemCreateArray
        );
        $itemData = ModelRepository::getItemInstance()->find(
            (int) $itemCreateData->get('id')
        );

        $itemId = $itemData->get('itemId');

        $priceCreateArray = [
            'hospitalId' => $hospitalId,
            'itemId' => $itemId,
            'distributorId' => $input['distributorId'],
            'distributorMCode' => $input['distributorMCode'],
            'quantity' => $input['quantity'],
            'quantityUnit' => $input['quantityUnit'],
            'itemUnit' => $input['itemUnit'],
            'price' => $input['price'],
            'unitPrice' => $input['unitPrice'],
            'notice' => $input['notice'],
        ];

        $priceCreateData = SpiralDb::title('NJ_PriceDB')->create(
            $priceCreateArray
        );
        $priceData = ModelRepository::getPriceInstance()->find(
            (int) $priceCreateData->get('id')
        );
        $priceId = $priceData->get('priceId');

        $inHPItemCreateArray = [
            'registrationTime' => 'now',
            'updateTime' => 'now',
            'itemId' => $itemId,
            'hospitalId' => $hospitalId,
            'priceId' => $priceId,
            'distributorId' => $input['distributorId'],
            'distributorMCode' => $input['distributorMCode'],
            'quantity' => $input['quantity'],
            'quantityUnit' => $input['quantityUnit'],
            'itemUnit' => $input['itemUnit'],
            'price' => $input['price'],
            'unitPrice' => $input['unitPrice'],
            'medicineCategory' => $input['medicineCategory'],
            'homeCategory' => $input['homeCategory'],
            'measuringInst' => $input['measuringInst'],
            'notice' => $input['notice'],
        ];

        $inHPItemCreateData = ModelRepository::getInHospitalItemInstance()->create(
            $inHPItemCreateArray
        );
        $inHPItemData = ModelRepository::getInHospitalItemInstance()->find(
            (int) $inHPItemCreateData->get('id')
        );

        return [
            'item' => $itemData,
            'price' => $priceData,
            'inHP' => $inHPItemData,
        ];
    }
}

interface ItemAndPriceAndInHospitalItemRepositoryInterface
{
    public function saveToArray(
        $tenantId,
        $hospitalId,
        array $input,
        array $attr = []
    );
}
