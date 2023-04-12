<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use Auth;
use Collection;
use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
use JoyPla\Enterprise\Models\Distributor;
use JoyPla\Enterprise\Models\DistributorId;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\DivisionName;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Item;
use JoyPla\Enterprise\Models\Price;
use JoyPla\Enterprise\Models\Quantity;
use JoyPla\Enterprise\Models\Stock;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class StockRepository implements StockRepositoryInterface
{
    public function getByDivisionId(
        HospitalId $hospitalId,
        DivisionId $divisionId
    ) {
        $stocks = ModelRepository::getStockViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('divisionId', $divisionId->value())
            ->get();

        $result = [];

        foreach ($stocks as $key => $stock) {
            $result[] = new Stock(
                $stock->id,
                new DateYearMonthDayHourMinutesSecond($stock->registrationTime),
                new DateYearMonthDayHourMinutesSecond($stock->updateTime),
                new InHospitalItemId($stock->inHospitalItemId),
                Item::create($stock),
                new Price($stock->price),
                Quantity::create($stock),
                Division::create($stock),
                new Distributor(
                    $hospitalId,
                    new DistributorId($stock->distributorId),
                    ''
                ),
                $stock->rackName,
                new DateYearMonthDayHourMinutesSecond($stock->invFinishTime),
                (int) $stock->stockQuantity,
                (int) $stock->orderWithinCount,
                (int) $stock->constantByDiv,
                $stock->inItemImage ?? ''
            );
        }

        //TODO
    }

    public function getStockByDivisionIdAndInHospitalItemIds(
        HospitalId $hospitalId,
        array $divisionIds,
        array $inHospitalItemIds
    ) {
        $inHospitalItemIds = array_map(function (
            InHospitalItemId $inHospitalItemId
        ) {
            return $inHospitalItemId;
        },
        $inHospitalItemIds);

        $inHospitalItemInstance = ModelRepository::getInHospitalItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        $division = ModelRepository::getDivisionInstance()
            ->where('hospitalId', $hospitalId->value())
            ->orWhere('deleteFlag', 'f')
            ->orWhereNull('deleteFlag');

        foreach ($divisionIds as $divisionId) {
            $division->orWhere('divisionId', $divisionId->value());
        }

        $divisions = $division->get();
        $divisionIds = [];
        foreach ($divisions as $division) {
            $divisionIds[] = new DivisionId($division->divisionId);
        }

        $stockInstance = ModelRepository::getStockViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        foreach ($divisionIds as $divisionId) {
            $stockInstance->orWhere('divisionId', $divisionId->value());
        }

        foreach ($inHospitalItemIds as $inHospitalItemId) {
            $stockInstance->orWhere(
                'inHospitalItemId',
                $inHospitalItemId->value()
            );
            $inHospitalItemInstance->orWhere(
                'inHospitalItemId',
                $inHospitalItemId->value()
            );
        }

        $stocks = $stockInstance->get();
        $inHospitalItems = $inHospitalItemInstance->get();

        $result = [];
        foreach ($stocks->all() as $stock) {
            $inHospitalItem = array_find($inHospitalItems, function (
                $inHospitalItem
            ) use ($stock) {
                return $inHospitalItem->inHospitalItemId ===
                    $stock->inHospitalItemId;
            });

            if (!$inHospitalItem) {
                continue;
            }

            $result[] = new Stock(
                $stock->id,
                new DateYearMonthDayHourMinutesSecond($stock->registrationTime),
                new DateYearMonthDayHourMinutesSecond($stock->updateTime),
                new InHospitalItemId($stock->inHospitalItemId),
                Item::create($inHospitalItem),
                new Price($inHospitalItem->price),
                Quantity::create($inHospitalItem),
                Division::create($stock),
                new Distributor(
                    $hospitalId,
                    new DistributorId($inHospitalItem->distributorId),
                    $inHospitalItem->distributorName
                ),
                $stock->rackName,
                new DateYearMonthDayHourMinutesSecond($stock->invFinishTime),
                (int) $stock->stockQuantity,
                (int) $stock->orderWithinCount,
                (int) $stock->constantByDiv,
                $inHospitalItem->inItemImage ?? ''
            );
        }

        return $result;
    }

    public function search(Auth $auth, object $search)
    {
        $inHospitalItemIds = ModelRepository::getInHospitalItemViewInstance()
            ->where('notUsedFlag', '1', '!=')
            ->where('hospitalId', $auth->hospitalId);

        if ($search->itemName !== '') {
            $inHospitalItemIds->orWhere(
                'itemName',
                '%' . $search->itemName . '%',
                'LIKE'
            );
        }
        if ($search->makerName !== '') {
            $inHospitalItemIds->orWhere(
                'makerName',
                '%' . $search->makerName . '%',
                'LIKE'
            );
        }
        if ($search->itemCode !== '') {
            $inHospitalItemIds->orWhere(
                'itemCode',
                '%' . $search->itemCode . '%',
                'LIKE'
            );
        }
        if ($search->itemStandard !== '') {
            $inHospitalItemIds->orWhere(
                'itemStandard',
                '%' . $search->itemStandard . '%',
                'LIKE'
            );
        }
        if ($search->itemJANCode !== '') {
            $inHospitalItemIds->orWhere(
                'itemJANCode',
                '%' . $search->itemJANCode . '%',
                'LIKE'
            );
        }

        $inHospitalItemIds = $inHospitalItemIds->get();

        if ((int) $inHospitalItemIds->count() === 0) {
            return [[], (int) $inHospitalItemIds->count(), 0];
        }

        $stocks = ModelRepository::getStockViewInstance()->where(
            'hospitalId',
            $auth->hospitalId
        );

        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $stocks->orWhere('divisionId', $divisionId);
            }
        }

        foreach ($inHospitalItemIds->all() as $inHospitalItemid) {
            $stocks->orWhere(
                'inHospitalItemId',
                $inHospitalItemid->inHospitalItemId
            );
        }

        $stocks = $stocks
            ->orderBy('requiredOrderNum', 'desc')
            ->page($search->currentPage)
            ->paginate($search->perPage);

        if ((int) $stocks->getData()->count() === 0) {
            return [[], (int) $stocks->getData()->count(), 0];
        }

        $inHospitalItem = ModelRepository::getInHospitalItemViewInstance()->where(
            'hospitalId',
            $auth->hospitalId
        );
        foreach ($stocks->getData()->all() as $i) {
            $inHospitalItem->orWhere('inHospitalItemId', $i->inHospitalItemId);
        }

        $inHospitalItem = $inHospitalItem->get()->all();

        $price = ModelRepository::getPriceInstance()->where(
            'hospitalId',
            $auth->hospitalId
        );

        foreach ($inHospitalItem as $item) {
            $price->orWhere('priceId', $item->priceId);
        }

        $price = $price->get()->all();

        foreach ($inHospitalItem as $key => $item) {
            $price_fkey = array_search(
                $item->priceId,
                collect_column($price, 'priceId')
            );
            $inHospitalItem[$key]->set(
                'priceNotice',
                $price[$price_fkey]->notice
            );
        }

        $result = [];
        $maxcount = $stocks->getData()->count();

        foreach ($stocks->getData()->all() as $i) {
            $fkey = array_search(
                $i->inHospitalItemId,
                collect_column($inHospitalItem, 'inHospitalItemId')
            );
            $merge = array_merge($i->all(), $inHospitalItem[$fkey]->all());
            $r = Stock::create(new Collection($merge))->toArray();
            $r['priceNotice'] = $inHospitalItem[$fkey]->priceNotice;
            $result[] = $r;
        }

        return [$result, $maxcount];
    }
}

interface StockRepositoryInterface
{
    public function search(Auth $auth, object $search);
}
