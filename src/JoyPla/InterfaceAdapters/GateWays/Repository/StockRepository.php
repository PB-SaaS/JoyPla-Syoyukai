<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\Model\InHospitalItem;
use App\SpiralDb\InHospitalItemView;
use App\SpiralDb\Price;
use App\SpiralDb\StockView;
use Auth;
use Collection;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\Stock;

class StockRepository implements StockRepositoryInterface
{
    public function search(Auth $auth, object $search)
    {
        $inHospitalItemIds = InHospitalItemView::where('notUsedFlag', '1', '!=')
            ->where('hospitalId', $auth->hospitalId)
            ->value('inHospitalItemId');

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

        if ((int) $inHospitalItemIds->count === 0) {
            return [[], (int) $inHospitalItemIds->count, 0];
        }

        $stocks = StockView::where('hospitalId', $auth->hospitalId);

        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $stocks->orWhere('divisionId', $divisionId);
            }
        }

        foreach ($inHospitalItemIds->data->all() as $inHospitalItemid) {
            $stocks->orWhere(
                'inHospitalItemId',
                $inHospitalItemid->inHospitalItemId
            );
        }

        $stocks = $stocks
            ->sort('requiredOrderNum', 'desc')
            ->page($search->currentPage)
            ->paginate($search->perPage);

        if ((int) $stocks->count === 0) {
            return [[], (int) $stocks->count, 0];
        }

        $inHospitalItem = InHospitalItemView::getNewInstance()->where(
            'hospitalId',
            $auth->hospitalId
        );
        foreach ($stocks->data->all() as $i) {
            $inHospitalItem->orWhere('inHospitalItemId', $i->inHospitalItemId);
        }

        $inHospitalItem = $inHospitalItem->get()->data->all();

        $price = Price::where('hospitalId', $auth->hospitalId)
            ->value('priceId')
            ->value('notice');

        foreach ($inHospitalItem as $item) {
            $price->orWhere('priceId', $item->priceId);
        }

        $price = $price->get()->data->all();

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
        $maxcount = $stocks->count;

        foreach ($stocks->data->all() as $i) {
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
