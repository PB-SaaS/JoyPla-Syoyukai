<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\Model\Price;
use Collection;
use framework\SpiralConnecter\SpiralDB;
use JoyPla\Enterprise\Models\ItemPrice;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\ItemId;
use JoyPla\Enterprise\Models\PriceId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class PriceRepository implements PriceRepositoryInterface
{
    public function findByPriceId(PriceId $priceId)
    {
        $prices = ModelRepository::getPriceInstance()
            ->Where('priceId', $priceId->value())
            ->get()
            ->all();

        $result = [];
        foreach ($prices as $d) {
            $result[] = Price::create($d);
        }

        return $result;
    }

    public function saveToArray(
        HospitalId $hospitalId,
        ItemId $itemId,
        array $price,
        array $attr = []
    ) {
        $createArray = [
            'hospitalId' => $hospitalId,
            'itemId' => $itemId,
            'distributorId' => $price['distributorId'],
            'distributorMCode' => $price['distributorMCode'],
            'quantity' => $price['quantity'],
            'quantityUnit' => $price['quantityUnit'],
            'itemUnit' => $price['itemUnit'],
            'price' => $price['price'],
            'unitPrice' => $price['unitPrice'],
            'notice' => $price['notice'],
        ];

        return ModelRepository::getPriceInstance()->create($createArray);
    }
}

interface PriceRepositoryInterface
{
    public function findByPriceId(PriceId $priceId);
    public function saveToArray(
        HospitalId $hospitalId,
        ItemId $itemId,
        array $item,
        array $attr = []
    );
}
