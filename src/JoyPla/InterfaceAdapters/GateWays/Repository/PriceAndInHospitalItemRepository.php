<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class PriceAndInHospitalItemRepository implements
    PriceAndInHospitalItemRepositoryInterface
{
    public function saveToArray(
        $hospitalId,
        $itemId,
        array $input,
        array $attr = []
    ) {
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

        $priceCreateData = ModelRepository::getPriceInstance()->create(
            $priceCreateArray
        );
        $priceData = ModelRepository::getPriceInstance()->find(
            (int) $priceCreateData->get('id')
        );

        $inHPItemCreateArray = [
            'registrationTime' => 'now',
            'updateTime' => 'now',
            'itemId' => $itemId,
            'hospitalId' => $hospitalId,
            'priceId' => $priceData->get('priceId'),
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

        return ['price' => $priceData, 'inHP' => $inHPItemData];
    }
}

interface PriceAndInHospitalItemRepositoryInterface
{
    public function saveToArray(
        $hospitalId,
        $itemId,
        array $input,
        array $attr = []
    );
}
