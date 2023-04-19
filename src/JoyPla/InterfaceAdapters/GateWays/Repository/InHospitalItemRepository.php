<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;
use JoyPla\Enterprise\Models\InHospitalItem;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\ItemId;
use JoyPla\Enterprise\Models\PriceId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class InHospitalItemRepository implements InHospitalItemRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId)
    {
        $inHospitalItems = ModelRepository::getInHospitalItemViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->all();

        $result = [];
        foreach ($inHospitalItems as $d) {
            $result[] = InHospitalItem::create($d);
        }

        return $result;
    }

    public function getByInHospitalItemIds(
        HospitalId $hospitalId,
        array $inHospitalItemIds
    ) {
        $inHospitalItemIds = array_map(function (
            InHospitalItemId $inHospitalItemId
        ) {
            return $inHospitalItemId;
        },
        $inHospitalItemIds);

        $inHospitalItems = ModelRepository::getInHospitalItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        foreach ($inHospitalItemIds as $inHospitalItemId) {
            $inHospitalItems->orWhere(
                'inHospitalItemId',
                $inHospitalItemId->value()
            );
        }

        $result = [];
        foreach ($inHospitalItems->get()->all() as $d) {
            $result[] = InHospitalItem::create($d);
        }

        return $result;
    }

    public function searchByJanCode(HospitalId $hospitalId, string $jancode)
    {
        $instance = ModelRepository::getInHospitalItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        $instance->where('itemJANCode', $jancode);
        $inHospitalItems = $instance->get()->all();

        $price = ModelRepository::getPriceInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        foreach ($inHospitalItems as $item) {
            $price->orWhere('priceId', $item->priceId);
        }

        $price = $price->get()->all();

        foreach ($inHospitalItems as $key => $item) {
            $price_fkey = array_search(
                $item->priceId,
                collect_column($price, 'priceId')
            );
            $inHospitalItems[$key]->set(
                'priceNotice',
                $price[$price_fkey]->notice
            );
        }

        return [$inHospitalItems, count($inHospitalItems)];
    }

    public function search(HospitalId $hospitalId, object $search)
    {
        $instance = ModelRepository::getInHospitalItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        if ($search->itemName !== '') {
            $instance->orWhere(
                'itemName',
                '%' . $search->itemName . '%',
                'LIKE'
            );
        }
        if ($search->makerName !== '') {
            $instance->orWhere(
                'makerName',
                '%' . $search->makerName . '%',
                'LIKE'
            );
        }
        if ($search->itemCode !== '') {
            $instance->orWhere(
                'itemCode',
                '%' . $search->itemCode . '%',
                'LIKE'
            );
        }
        if ($search->itemStandard !== '') {
            $instance->orWhere(
                'itemStandard',
                '%' . $search->itemStandard . '%',
                'LIKE'
            );
        }
        if ($search->itemJANCode !== '') {
            $instance->orWhere(
                'itemJANCode',
                '%' . $search->itemJANCode . '%',
                'LIKE'
            );
        }
        if (count($search->distributorIds) > 0) {
            foreach ($search->distributorIds as $distributorId) {
                $instance->orWhere('distributorId', $distributorId);
            }
        }

        if ($search->isNotUse == '1') {
            $instance->where('notUsedFlag', '1');
        } elseif ($search->isNotUse == '0') {
            $instance->where('notUsedFlag', '1', '!=');
        }

        $result = $instance
            ->orderBy('id', 'asc')
            ->page($search->currentPage)
            ->paginate($search->perPage);

        $inHospitalItems = $result->getData()->all();

        if (count($inHospitalItems) == 0) {
            foreach ($inHospitalItems as $key => $item) {
                $inHospitalItems[$key]->set('priceNotice', '');
            }
            return [$inHospitalItems, $result->getTotal()];
        }

        $price = ModelRepository::getPriceInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        foreach ($inHospitalItems as $item) {
            $price->orWhere('priceId', $item->priceId);
        }

        $price = $price->get()->all();

        foreach ($inHospitalItems as $key => $item) {
            $price_fkey = array_search(
                $item->priceId,
                collect_column($price, 'priceId')
            );
            $inHospitalItems[$key]->set(
                'priceNotice',
                $price[$price_fkey]->notice
            );
        }

        return [$inHospitalItems, $result->getTotal()];
    }

    public function saveToArray(
        HospitalId $hospitalId,
        ItemId $itemId,
        PriceId $priceId,
        array $inHP,
        array $attr = []
    ) {
        $createArray = [
            'registrationTime' => 'now',
            'updateTime' => 'now',
            'itemId' => $itemId,
            'hospitalId' => $hospitalId,
            'priceId' => $priceId,
            'distributorId' => $inHP['distributorId'],
            'distributorMCode' => $inHP['distributorMCode'],
            'quantity' => $inHP['quantity'],
            'quantityUnit' => $inHP['quantityUnit'],
            'itemUnit' => $inHP['itemUnit'],
            'price' => $inHP['price'],
            'unitPrice' => $inHP['unitPrice'],
            'medicineCategory' => $inHP['medicineCategory'],
            'homeCategory' => $inHP['homeCategory'],
            'measuringInst' => $inHP['measuringInst'],
            'notice' => $inHP['notice'],
        ];

        return ModelRepository::getItemInstance()->create($createArray);
    }
}

interface InHospitalItemRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId);
    public function search(HospitalId $hospitalId, object $search);
    public function searchByJanCode(HospitalId $hospitalId, string $jancode);
    public function saveToArray(
        HospitalId $hospitalId,
        ItemId $itemId,
        PriceId $priceId,
        array $inHP,
        array $attr = []
    );
}
