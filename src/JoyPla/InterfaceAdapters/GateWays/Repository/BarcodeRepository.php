<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
use JoyPla\Enterprise\Models\Distributor;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\Hospital;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Order;
use JoyPla\Enterprise\Models\OrderAdjustment;
use JoyPla\Enterprise\Models\OrderId;
use JoyPla\Enterprise\Models\OrderItem;
use JoyPla\Enterprise\Models\OrderStatus;
use JoyPla\Enterprise\Models\TextArea512Bytes;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class BarcodeRepository implements BarcodeRepositoryInterface
{
    public function searchByJanCode(HospitalId $hospitalId, string $jancode)
    {
        $instance = ModelRepository::getInHospitalItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        $instance->where('itemJANCode', $jancode);
        $result = $instance->get();
        $inHospitalItems = $result->all();

        $price = ModelRepository::getPriceInstance()
            ->where('hospitalId', $hospitalId->value())
            ->value('priceId')
            ->value('notice');

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

        return [$inHospitalItems, $result->count()];
    }

    public function orderSearchByJanCode(
        HospitalId $hospitalId,
        string $jancode,
        $divisionId
    ) {
        $instance = ModelRepository::getOrderItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        //$instance = OrderItemView::where('hospitalId', $hospitalId->value());
        $instance->where('itemJANCode', $jancode);

        $instance->orWhere('receivingFlag', '0');
        $instance->orWhere('receivingFlag', '0', 'ISNULL');

        if ($divisionId instanceof DivisionId) {
            $instance->where('divisionId', $divisionId->value());
        }

        $result = $instance->get();
        if ($result->count() == 0) {
            return [[], 0];
        }

        $orderItems = $result->all();

        //$historys = OrderView::where('hospitalId', $hospitalId->value());
        $historys = ModelRepository::getOrderViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        $hospital = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->first();

        foreach ($orderItems as $item) {
            $historys->orWhere('orderNumber', $item->orderNumber);
        }
        $historys = $historys->get();
        $orders = [];
        foreach ($historys->all() as $history) {
            $order = new Order(
                new OrderId($history->orderNumber),
                new DateYearMonthDayHourMinutesSecond(
                    $history->registrationTime
                ),
                new DateYearMonthDayHourMinutesSecond($history->orderTime),
                [],
                Hospital::create($hospital),
                Division::create($history),
                Distributor::create($history),
                new OrderStatus($history->orderStatus),
                new OrderAdjustment($history->adjustment),
                new TextArea512Bytes($history->ordercomment),
                new TextArea512Bytes($history->distrComment),
                $history->ordererUserName,
                $history->receivingTarget != '2' ? 1 : 2 //1	大倉庫  2	発注部署
            );
            foreach ($orderItems as $item) {
                if ($order->getOrderId()->equal($item->orderNumber)) {
                    $order = $order->addOrderItem(OrderItem::create($item));
                }
            }

            $orders[] = $order;
        }

        return [$orders, count($orders)];
    }

    public function orderSearchByInHospitalItemId(
        HospitalId $hospitalId,
        InHospitalItemId $inHospitalItemId,
        $divisionId = ''
    ) {
        //$instance = OrderItemView::where('hospitalId', $hospitalId->value());
        $instance = ModelRepository::getOrderItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        $instance->where('inHospitalItemId', $inHospitalItemId->value());
        $instance->where('receivingFlag', '1', '!=');

        if ($divisionId instanceof DivisionId) {
            $instance->where('divisionId', $divisionId->value());
        }

        $result = $instance->get();
        if ($result->count() == 0) {
            return [[], 0];
        }

        $orderItems = $result->all();

        $hospital = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->first();

        //$historys = OrderView::where('hospitalId', $hospitalId->value());
        $historys = ModelRepository::getOrderViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        foreach ($orderItems as $item) {
            $historys->orWhere('orderNumber', $item->orderNumber);
        }
        $historys = $historys->get();
        $orders = [];
        foreach ($historys->all() as $history) {
            $order = new Order(
                new OrderId($history->orderNumber),
                new DateYearMonthDayHourMinutesSecond(
                    $history->registrationTime
                ),
                new DateYearMonthDayHourMinutesSecond($history->orderTime),
                [],
                Hospital::create($hospital),
                Division::create($history),
                Distributor::create($history),
                new OrderStatus($history->orderStatus),
                new OrderAdjustment($history->adjustment),
                new TextArea512Bytes($history->ordercomment),
                new TextArea512Bytes($history->distrComment),
                $history->ordererUserName,
                $history->receivingTarget != '2' ? 1 : 2 //1	大倉庫  2	発注部署
            );
            foreach ($orderItems as $item) {
                if ($order->getOrderId()->equal($item->orderNumber)) {
                    $order = $order->addOrderItem(OrderItem::create($item));
                }
            }

            $orders[] = $order;
        }

        return [$orders, count($orders)];
    }
}

interface BarcodeRepositoryInterface
{
    public function searchByJanCode(HospitalId $hospitalId, string $jancode);
    public function orderSearchByJanCode(
        HospitalId $hospitalId,
        string $jancode,
        $divisionId
    );
    public function orderSearchByInHospitalItemId(
        HospitalId $hospitalId,
        InHospitalItemId $inHospitalItemId,
        $divisionId
    );
}
