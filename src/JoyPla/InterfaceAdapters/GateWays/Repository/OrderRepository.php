<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use Auth;
use JoyPla\Enterprise\Models\Order;
use JoyPla\Enterprise\Models\OrderId;
use JoyPla\Enterprise\Models\OrderItem;
use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\DateYearMonthDay;
use JoyPla\Enterprise\Models\Distributor;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Item;
use JoyPla\Enterprise\Models\OrderItemId;
use JoyPla\Enterprise\Models\OrderQuantity;
use JoyPla\Enterprise\Models\OrderStatus;
use JoyPla\Enterprise\Models\Price;
use JoyPla\Enterprise\Models\Quantity;
use JoyPla\Enterprise\Models\ReceivedQuantity;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use Model;

class OrderRepository extends ModelRepository implements
    OrderRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId)
    {
        $orderHistory = ModelRepository::getOrderItemViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->all();

        return $orderHistory;
    }

    public function findByInHospitalItem(
        HospitalId $hospitalId,
        array $orderItems
    ) {
        $division = ModelRepository::getDivisionInstance();

        foreach ($orderItems as $item) {
            $division->orWhere('divisionId', $item->divisionId);
        }

        $division = $division->get();
        $division = $division->all();

        $inHospitalItem = ModelRepository::getInHospitalItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        foreach ($orderItems as $item) {
            $inHospitalItem->orWhere(
                'inHospitalItemId',
                $item->inHospitalItemId
            );
        }

        $inHospitalItem = $inHospitalItem->get()->all();

        foreach ($orderItems as $item) {
            $division_find_key = array_search(
                $item->divisionId,
                collect_column($division, 'divisionId')
            );
            $inHospitalItem_find_key = array_search(
                $item->inHospitalItemId,
                collect_column($inHospitalItem, 'inHospitalItemId')
            );
            $result[] = new OrderItem(
                new OrderId(''),
                OrderItemId::generate(),
                new InHospitalItemId(
                    $inHospitalItem[$inHospitalItem_find_key]->inHospitalItemId
                ),
                Item::create($inHospitalItem[$inHospitalItem_find_key]),
                $hospitalId,
                Division::create($division[$division_find_key]),
                Distributor::create($inHospitalItem[$inHospitalItem_find_key]),
                Quantity::create($inHospitalItem[$inHospitalItem_find_key]),
                new Price($inHospitalItem[$inHospitalItem_find_key]->price),
                new OrderQuantity((int) $item->orderUnitQuantity),
                new ReceivedQuantity((int) $item->receivingNum),
                new DateYearMonthDay(''),
                $inHospitalItem[$inHospitalItem_find_key]->distributorMCode,
                (int) $inHospitalItem[$inHospitalItem_find_key]->lotManagement,
                (int) $inHospitalItem[$inHospitalItem_find_key]->inItemImage,
                false,
                1
            );
        }
        return $result;
    }

    public function saveToArray(
        HospitalId $hospitalId,
        array $orders,
        array $attr = []
    ) {
        $orders = array_map(function (Order $order) {
            return $order;
        }, $orders);

        $history = [];
        $items = [];

        $deleteOrderIds = [];

        foreach ($orders as $oKey => $order) {
            if ($order->itemCount() === 0) {
                $deleteOrderIds[] = $order->getOrderId();
                unset($orders[$oKey]);
                continue;
            }

            $orderToArray = $order->toArray();
            $history[] = [
                'orderNumber' => (string) $orderToArray['orderId'],
                'orderTime' => (string) $orderToArray['orderDate'],
                'hospitalId' =>
                    (string) $orderToArray['hospital']['hospitalId'],
                'divisionId' =>
                    (string) $orderToArray['division']['divisionId'],
                'distributorId' =>
                    (string) $orderToArray['distributor']['distributorId'],
                'itemsNumber' => (string) $orderToArray['itemCount'],
                'totalAmount' => (string) $orderToArray['totalAmount'],
                'orderStatus' => (string) $orderToArray['orderStatus'],
                'adjustment' => (string) $orderToArray['adjustment'],
                'ordercomment' => (string) $orderToArray['orderComment'],
                'ordererUserName' => (string) $orderToArray['orderUserName'],
            ];

            $receivingDivisionCode = '';
            if (
                $order->getOrderStatus()->value() ===
                OrderStatus::OrderCompletion
            ) {
                if ($order->getReceivedTarget() === 1) {
                    // 大倉庫

                    $division = ModelRepository::getDivisionInstance()
                        ->where('hospitalId', $hospitalId->value())
                        ->where('divisionType', '1')
                        ->get();

                    $receivingDivisionCode = $division->first()
                        ->deliveryDestCode;
                }
                if ($order->getReceivedTarget() === 2) {
                    // 部署
                    $division = ModelRepository::getDivisionInstance()
                        ->where('hospitalId', $hospitalId->value())
                        ->where(
                            'divisionId',
                            $order
                                ->getDivision()
                                ->getDivisionId()
                                ->value()
                        )
                        ->get();

                    $receivingDivisionCode = $division->first()
                        ->deliveryDestCode;
                }
            }

            foreach ($orderToArray['orderItems'] as $orderItem) {
                $item = [
                    'inHospitalItemId' =>
                        (string) $orderItem['inHospitalItemId'],
                    'orderNumber' => (string) $orderToArray['orderId'],
                    'orderCNumber' => (string) $orderItem['orderItemId'],
                    'price' => (string) $orderItem['price'],
                    'orderQuantity' => (string) $orderItem['orderQuantity'],
                    'receivingNum' => (string) $orderItem['receivedQuantity'],
                    'orderPrice' => (string) $orderItem['orderPrice'],
                    'hospitalId' => (string) $orderItem['hospitalId'],
                    'receivingFlag' => (string) $orderItem['receivedFlag'],
                    'divisionId' =>
                        (string) $orderItem['division']['divisionId'],
                    'distributorId' =>
                        (string) $orderItem['distributor']['distributorId'],
                    'quantity' =>
                        (string) $orderItem['quantity']['quantityNum'],
                    'quantityUnit' =>
                        (string) $orderItem['quantity']['quantityUnit'],
                    'itemUnit' => (string) $orderItem['quantity']['itemUnit'],
                    'lotManagement' => (string) $orderItem['lotManagement'],
                    'itemId' => (string) $orderItem['item']['itemId'],
                ];

                if (isset($attr['isReceived']) === true) {
                    $item['receivingTime'] = 'now';
                }

                if (
                    $order->getOrderStatus()->value() ===
                        OrderStatus::OrderCompletion &&
                    ((bool) $orderItem['useMedicode'])
                ) {
                    $item['deliveryDestCode'] = (string) $receivingDivisionCode;
                }
                $items[] = $item;
            }
        }

        if (count($deleteOrderIds) > 0) {
            $instance = ModelRepository::getOrderInstance();
            //$instance = SpiralDbOrder::getNewInstance();
            foreach ($deleteOrderIds as $orderId) {
                $instance->orWhere('orderNumber', $orderId->value());
            }
            $instance->delete();
        }

        if (count($items) > 0) {
            $instance = ModelRepository::getOrderItemInstance();
            //$instance = SpiralDbOrderItem::getNewInstance();
            foreach ($items as $item) {
                $instance->orWhere('orderNumber', $item['orderNumber']);
                $instance->where('orderCNumber', $item['orderCNumber'], '!=');
            }
            $instance->delete();
        }

        if (count($history) > 0) {
            ModelRepository::getOrderInstance()->upsertBulk(
                'orderNumber',
                $history
            );
        }

        if (count($items) > 0) {
            ModelRepository::getOrderItemInstance()->upsertBulk(
                'orderCNumber',
                $items
            );
        }
        return array_values($orders);
    }

    public function search(HospitalId $hospitalId, object $search)
    {
        $itemSearchFlag = false;
        $itemViewInstance = ModelRepository::getOrderItemViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->value('orderNumber')
            ->value('orderCNumber');
        $historyViewInstance = ModelRepository::getOrderViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        if ($search->orderId) {
            $itemViewInstance->orWhere(
                'orderNumber',
                '%' . $search->orderId . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemName) {
            $itemViewInstance->orWhere(
                'itemName',
                '%' . $search->itemName . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->makerName) {
            $itemViewInstance->orWhere(
                'makerName',
                '%' . $search->makerName . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemCode) {
            $itemViewInstance->orWhere(
                'itemCode',
                '%' . $search->itemCode . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemStandard) {
            $itemViewInstance->orWhere(
                'itemStandard',
                '%' . $search->itemStandard . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemJANCode) {
            $itemViewInstance->orWhere(
                'itemJANCode',
                '%' . $search->itemJANCode . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }

        if ($search->receivedFlag === 0) {
            $itemViewInstance->orWhere('receivingFlag', '0', '=');
            $itemViewInstance->orWhere('receivingFlag', '0', 'ISNULL');
            $itemSearchFlag = true;
        }
        if ($search->receivedFlag === 1) {
            $itemViewInstance->where('receivingFlag', '1', '=');
            $itemSearchFlag = true;
        }

        $orderCNumbers = [];
        if ($itemSearchFlag) {
            $itemViewInstance = $itemViewInstance->get();
            if ($itemViewInstance->count() == 0) {
                return [[], 0];
            }
            foreach ($itemViewInstance->all() as $item) {
                $historyViewInstance = $historyViewInstance->orWhere(
                    'orderNumber',
                    $item->orderNumber
                );
                $orderCNumbers[] = $item->orderCNumber;
            }
        }

        if (
            is_array($search->distributorIds) &&
            count($search->distributorIds) > 0
        ) {
            foreach ($search->distributorIds as $distributorId) {
                $historyViewInstance->orWhere('distributorId', $distributorId);
            }
        }

        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $historyViewInstance->orWhere('divisionId', $divisionId);
            }
        }

        if (is_array($search->orderStatus) && count($search->orderStatus) > 0) {
            foreach ($search->orderStatus as $orderStatus) {
                $historyViewInstance->orWhere('orderStatus', $orderStatus);
            }
        }
        if ($search->registerDate) {
            $registerDate = new DateYearMonth($search->registerDate);
            $nextMonth = $registerDate->nextMonth();

            $historyViewInstance->where(
                'registrationTime',
                $registerDate->format('Y-m-01'),
                '>='
            );

            $historyViewInstance->where(
                'registrationTime',
                $nextMonth->format('Y-m-01'),
                '<'
            );
        }

        if ($search->orderDate) {
            $yearMonth = new DateYearMonth($search->orderDate);
            $nextMonth = $yearMonth->nextMonth();

            $historyViewInstance->where(
                'orderTime',
                $yearMonth->format('Y-m-01'),
                '>='
            );
            $historyViewInstance->where(
                'orderTime',
                $nextMonth->format('Y-m-01'),
                '<'
            );
        }

        $historys = $historyViewInstance
            ->orderBy('id', 'desc')
            ->page($search->currentPage)
            ->paginate($search->perPage);

        if ($historys->getTotal() == 0) {
            return [[], 0];
        }

        $itemViewInstance = ModelRepository::getOrderItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );
        foreach ($historys->getData()->all() as $history) {
            $itemViewInstance = $itemViewInstance->orWhere(
                'orderNumber',
                $history->orderNumber
            );
        }
        foreach ($orderCNumbers as $orderCNumber) {
            $itemViewInstance = $itemViewInstance->orWhere(
                'orderCNumber',
                $orderCNumber
            );
        }

        $items = $itemViewInstance->get();
        $orders = [];
        foreach ($historys->getData()->all() as $history) {
            $order = Order::create($history);
            foreach ($items->all() as $item) {
                if ($order->getOrderId()->equal($item->orderNumber)) {
                    $order = $order->addOrderItem(OrderItem::create($item));
                }
            }

            $orders[] = $order;
        }

        return [$orders, $historys->getTotal()];
    }

    public function getOrder(
        HospitalId $hospitalId,
        array $orderStatus = [OrderStatus::UnOrdered],
        array $orderIds = []
    ) {
        $historys = ModelRepository::getOrderViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        if (count($orderStatus) > 0) {
            foreach ($orderStatus as $o) {
                $historys->orWhere('orderStatus', $o);
            }
        }

        if (count($orderIds) > 0) {
            foreach ($orderIds as $orderId) {
                $historys->orWhere('orderNumber', $orderId->value());
            }
        }

        $orders = array_map(function ($order) {
            return Order::create($order);
        }, $historys->get()->all());

        $orderIds =
            array_map(function (Order $order) {
                return $order->getOrderId()->value();
            }, $orders) ?? [];

        $orderItems = ModelRepository::getOrderItemViewInstance()
            ->whereIn('orderNumber', $orderIds)
            ->get()
            ->all();

        $orderItems =
            array_map(function ($orderItem) {
                return OrderItem::create($orderItem);
            }, $orderItems) ?? [];

        foreach ($orders as &$order) {
            foreach ($orderItems as $orderItem) {
                if (
                    $order->getOrderId()->value() ==
                    $orderItem->getOrderId()->value()
                ) {
                    $order = $order->addOrderItem($orderItem);
                }
            }
        }

        return $orders;
    }

    public function index(
        HospitalId $hospitalId,
        OrderId $orderId,
        array $orderStatus = [OrderStatus::UnOrdered]
    ) {
        $orderView = ModelRepository::getOrderViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('orderNumber', $orderId->value());

        if (count($orderStatus) > 0) {
            foreach ($orderStatus as $o) {
                $orderView->orWhere('orderStatus', $o);
            }
        }

        $orderView = $orderView->get();

        if (count($orderView->all()) === 0) {
            return null;
        }
        $orderItemView = ModelRepository::getOrderItemViewInstance()
            ->orderBy('id', 'asc')
            ->where('hospitalId', $hospitalId->value())
            ->where('orderNumber', $orderId->value())
            ->get();

        $order = Order::create($orderView->first());

        foreach ($orderItemView->all() as $item) {
            $order = $order->addOrderItem(OrderItem::create($item));
        }

        return $order;
    }

    public function delete(HospitalId $hospitalId, OrderId $orderId)
    {
        return ModelRepository::getOrderInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('orderNumber', $orderId->value())
            ->delete();
    }

    public function getOrderByOrderItemId(
        HospitalId $hospitalId,
        array $orderItemIds
    ) {
        $items = ModelRepository::getOrderItemViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->value('orderNumber');
        if (count($orderItemIds) === 0) {
            return [];
        }
        foreach ($orderItemIds as $id) {
            $items->orWhere('orderCNumber', $id);
        }

        $items = $items->get();

        $historyViewInstance = ModelRepository::getOrderViewInstance()
            ->orderBy('id', 'desc')
            ->where('hospitalId', $hospitalId->value());

        foreach ($items->all() as $item) {
            $historyViewInstance->orWhere('orderNumber', $item->orderNumber);
        }

        $historys = $historyViewInstance->get()->all();

        $itemViewInstance = ModelRepository::getOrderItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        foreach ($historys as $history) {
            $itemViewInstance = $itemViewInstance->orWhere(
                'orderNumber',
                $history->orderNumber
            );
        }

        $items = $itemViewInstance->get();

        $orders = [];
        foreach ($historys as $history) {
            $order = Order::create($history);

            foreach ($items->all() as $item) {
                if ($order->getOrderId()->equal($item->orderNumber)) {
                    $order = $order->addOrderItem(OrderItem::create($item));
                }
            }

            $orders[] = $order;
        }
        return $orders;
    }

    /**
     * getUnapprovedOrder function
     *
     * @param HospitalId $hospitalId
     * @param OrderItem[] $orderItems
     * @return void
     */
    public function getUnapprovedOrder(
        HospitalId $hospitalId,
        array $orderItems
    ) {
        $orderItems = array_map(function (OrderItem $i) {
            return $i;
        }, $orderItems);

        $historyViewInstance = ModelRepository::getOrderViewInstance()
            ->orderBy('id', 'desc')
            ->where('hospitalId', $hospitalId->value())
            ->where('orderStatus', '1');

        foreach ($orderItems as $item) {
            $historyViewInstance->where(
                'divisionId',
                $item
                    ->getDivision()
                    ->getDivisionId()
                    ->value()
            );
            $historyViewInstance->orWhere(
                'distributorId',
                $item
                    ->getDistributor()
                    ->getDistributorId()
                    ->value()
            );
        }

        $historys = $historyViewInstance->get()->all();

        $itemViewInstance = ModelRepository::getOrderItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        foreach ($historys as $history) {
            $itemViewInstance = $itemViewInstance->orWhere(
                'orderNumber',
                $history->orderNumber
            );
        }

        $items = $itemViewInstance->get();

        $orders = [];
        foreach ($historys as $history) {
            $order = Order::create($history);

            foreach ($items->all() as $item) {
                if ($order->getOrderId()->equal($item->orderNumber)) {
                    $order = $order->addOrderItem(OrderItem::create($item));
                }
            }

            $orders[] = $order;
        }

        return $orders;
    }

    public function sendUnapprovedOrderMail(array $orders, Auth $user)
    {
        $orders = array_map(function (Order $order) {
            return $order;
        }, $orders);

        $unapprovedOrderMailViewModel = [];

        $distributor = ModelRepository::getDistributorInstance();
        foreach ($orders as $order) {
            $orderToArray = $order->toArray();
            $distributor->orWhere(
                'distributorId',
                $orderToArray['distributor']['distributorId']
            );
        }

        $distributors = $distributor->get();

        foreach ($orders as $order) {
            $orderToArray = $order->toArray();
            $distributor = array_find($distributors, function (
                $distributor
            ) use ($orderToArray) {
                return $distributor->distributorId ==
                    $orderToArray['distributor']['distributorId'];
            });
            $unapprovedOrderMailViewModel[] = [
                'orderNumber' => $orderToArray['orderId'],
                'divisionName' => $orderToArray['division']['divisionName'],
                'distributorName' => $distributor->distributorName,
                'orderMethod' => $distributor->orderMethod,
                'totalAmount' => number_format_jp($orderToArray['totalAmount']),
            ];
        }

        $mailBody = view(
            'mail/Order/RegistUnapprovedOrderMail',
            [
                'name' => '%val:usr:name%',
                'hospitalName' => $orders[0]
                    ->getHospital()
                    ->getHospitalName()
                    ->value(),
                'ordererUserName' => $user->name,
                'history' => $unapprovedOrderMailViewModel,
                'url' => config('url.hospital', ''),
            ],
            false
        )->render();

        $ids = ModelRepository::getHospitalUserInstance()
            ->where('hospitalId', $user->hospitalId)
            ->whereIn('userPermission', [1, 3])
            ->get();

        $ruleId = ModelRepository::getHospitalUserMailInstance()
            ->subject('[JoyPla] 未発注書が作成されました')
            ->standby(false)
            ->reserveDate('now')
            ->bodyText($mailBody)
            ->formAddress(FROM_ADDRESS)
            ->formName(FROM_NAME)
            ->mailField('mailAddress')
            ->regist();

        $ids = array_values(array_column($ids->toArray(), 'id'));

        ModelRepository::getHospitalUserMailInstance()
            ->ruleId($ruleId)
            ->sampling($ids);
    }

    public function sendRevisedOrderMail(Order $order, Auth $user)
    {
        $order = $order->toArray();

        $distributor = ModelRepository::getDistributorInstance()
            ->where('distributorId', $order['distributor']['distributorId'])
            ->get();

        $distributor = $distributor->first();

        $useMedicode = in_array(
            true,
            array_map(function (array $orderItem) {
                return $orderItem['useMedicode'];
            }, $order['orderItems']),
            true
        );

        $mailBody = view('mail/Order/OrderRevised', [
            'name' => '%val:usr:name%',
            'hospital_name' => $order['hospital']['hospitalName'],
            'prefectures' => $order['hospital']['prefectures'],
            'postal_code' => $order['hospital']['postalCode'],
            'address' => $order['hospital']['address'],
            'distributor_name' => $distributor->distributorName,
            'division_name' => $order['division']['divisionName'],
            'order_date' => $order['orderDate'],
            'order_number' => $order['orderId'],
            'item_num' => $order['itemCount'],
            'useMedicode' => $useMedicode,
            'total_price' =>
                '￥' . number_format_jp((float) $order['totalAmount']),
            'slip_url' =>
                config('url.distributorBarcodeSearch', '') .
                '?searchValue=' .
                $order['orderId'],
            'login_url' => config('url.distributor', ''),
        ])->render();

        $ids = ModelRepository::getInvitingInstance()
            ->where('distributorId', $order['distributor']['distributorId'])
            ->where('invitingAgree', 't')
            ->value('id')
            ->get();

        $ruleId = ModelRepository::getInvitingMailInstance()
            ->subject('[JoyPla] 発注書に変更がありました')
            ->standby(false)
            ->reserveDate('now')
            ->bodyText($mailBody)
            ->formAddress(FROM_ADDRESS)
            ->formName(FROM_NAME)
            ->mailField('mailAddress')
            ->regist();

        $ids = array_values(array_column($ids->toArray(), 'id'));

        ModelRepository::getInvitingMailInstance()
            ->ruleId($ruleId)
            ->sampling($ids);
    }

    public function sendApprovalAllOrderMail(array $orders, Auth $user)
    {
        $orders = array_map(function (Order $order) {
            return $order;
        }, $orders);

        $hospital = $orders[0]->getHospital()->toArray();

        $distributors = ModelRepository::getDistributorInstance();
        $distributorUsers = ModelRepository::getInvitingInstance()->where(
            'invitingAgree',
            't'
        );

        $divisions = ModelRepository::getDivisionInstance()->where(
            'hospitalId',
            $user->hospitalId
        );

        $divisionUsers = ModelRepository::getHospitalUserInstance()
            ->where('hospitalId', $user->hospitalId)
            ->whereIn('userPermission', [2]);
        foreach ($orders as $order) {
            $distributors->orWhere(
                'distributorId',
                $order
                    ->getDistributor()
                    ->getDistributorId()
                    ->value()
            );

            $distributorUsers->orWhere(
                'distributorId',
                $order
                    ->getDistributor()
                    ->getDistributorId()
                    ->value()
            );

            $divisionUsers->orWhere(
                'divisionId',
                $order
                    ->getDivision()
                    ->getDivisionId()
                    ->value()
            );

            $divisions->orWhere(
                'divisionId',
                $order
                    ->getDivision()
                    ->getDivisionId()
                    ->value()
            );
        }

        $distributors = $distributors->get()->all();
        $distributorUsers = $distributorUsers->get()->all();
        $divisions = $divisions->get()->all();
        $divisionUsers = $divisionUsers->get()->all();

        $orders = array_map(function (Order $order) {
            return $order->toArray();
        }, $orders);

        foreach ($orders as &$order) {
            $distributor = array_find($distributors, function (
                $distributor
            ) use ($order) {
                return $distributor->distributorId ===
                    $order['distributor']['distributorId'];
            });

            $order['distributor'] = $distributor->toArray();
        }

        foreach ($distributors as $distributor) {
            $ids = array_filter($distributorUsers, function (
                $distributorUser
            ) use ($distributor) {
                return $distributorUser->distributorId ===
                    $distributor->distributorId;
            });

            $ids = array_values(array_column($ids, 'id'));

            $orderData = array_filter($orders, function ($order) use (
                $distributor
            ) {
                return $order['distributor']['distributorId'] ===
                    $distributor->distributorId;
            });

            $useMedicode = false;
            foreach ($orderData as $order) {
                $useMedicode = in_array(
                    true,
                    array_map(function ($orderItem) {
                        return $orderItem['useMedicode'];
                    }, $order['orderItems']),
                    true
                );
            }
            $mailBody = view('mail/Order/OrderFixMultiForDistributor', [
                'name' => '%val:usr:name%',
                'hospital_name' => $hospital['hospitalName'],
                'prefectures' => $hospital['prefectures'],
                'address' => $hospital['address'],
                'postal_code' => $hospital['postalCode'],
                'distributor_name' => $distributor->distributorName,
                'order_method' => $distributor->orderMethod,
                'useMedicode' => $useMedicode,
                'orders' => $orderData ?? [],
                'slip_url' =>
                    config('url.distributorBarcodeSearch', '') .
                    '?searchValue=',
                'login_url' => config('url.distributor', ''),
            ])->render();
            $ruleId = ModelRepository::getInvitingMailInstance()
                ->subject('[JoyPla] 発注が行われました')
                ->standby(false)
                ->reserveDate('now')
                ->bodyText($mailBody)
                ->formAddress(FROM_ADDRESS)
                ->formName(FROM_NAME)
                ->mailField('mailAddress')
                ->regist();
            ModelRepository::getInvitingMailInstance()
                ->ruleId($ruleId)
                ->sampling($ids);
        }

        foreach ($divisions as $division) {
            $ids = array_filter($divisionUsers, function ($divisionUser) use (
                $division
            ) {
                return $divisionUser->divisionId === $division->divisionId;
            });

            $ids = array_values(array_column($ids, 'id'));

            $orderData = array_filter($orders, function ($order) use (
                $division
            ) {
                return $order['division']['divisionId'] ===
                    $division->divisionId;
            });

            $mailBody = view('mail/Order/OrderFixMulti', [
                'name' => '%val:usr:name%',
                'hospital_name' => $hospital['hospitalName'],
                'postal_code' => $hospital['postalCode'],
                'prefectures' => $hospital['prefectures'],
                'address' => $hospital['address'],
                'orders' => $orderData ?? [],
                'login_url' => config('url.hospital', ''),
            ])->render();

            $mailId = ModelRepository::getHospitalUserMailInstance()
                ->subject('[JoyPla] 発注が行われました')
                ->standby(false)
                ->reserveDate('now')
                ->bodyText($mailBody)
                ->formAddress(FROM_ADDRESS)
                ->formName(FROM_NAME)
                ->mailField('mailAddress')
                ->regist();

            ModelRepository::getHospitalUserMailInstance()
                ->ruleId($mailId)
                ->sampling($ids);
        }

        $mailBody = view('mail/Order/OrderFixMulti', [
            'name' => '%val:usr:name%',
            'hospital_name' => $hospital['hospitalName'],
            'postal_code' => $hospital['postalCode'],
            'prefectures' => $hospital['prefectures'],
            'address' => $hospital['address'],
            'orders' => $orders ?? [],
            'login_url' => config('url.hospital', ''),
        ])->render();

        $ids = ModelRepository::getHospitalUserInstance()
            ->where('hospitalId', $user->hospitalId)
            ->whereIn('userPermission', [1, 3])
            ->get();

        $ids = array_values(array_column($ids->toArray(), 'id'));

        $mailId = ModelRepository::getHospitalUserMailInstance()
            ->subject('[JoyPla] 発注が行われました')
            ->standby(false)
            ->reserveDate('now')
            ->bodyText($mailBody)
            ->formAddress(FROM_ADDRESS)
            ->formName(FROM_NAME)
            ->mailField('mailAddress')
            ->regist();

        ModelRepository::getHospitalUserMailInstance()
            ->ruleId($mailId)
            ->sampling($ids);
    }

    public function sendApprovalOrderMail(Order $order, Auth $user)
    {
        $order = $order->toArray();

        $distributor = ModelRepository::getDistributorInstance()
            ->where('distributorId', $order['distributor']['distributorId'])
            ->get();

        $distributor = $distributor->first();

        $useMedicode = in_array(
            true,
            array_map(function (array $orderItem) {
                return $orderItem['useMedicode'];
            }, $order['orderItems']),
            true
        );

        $mailBody = view('mail/Order/OrderFixForDistributor', [
            'name' => '%val:usr:name%',
            'hospital_name' => $order['hospital']['hospitalName'],
            'prefectures' => $order['hospital']['prefectures'],
            'address' => $order['hospital']['address'],
            'postal_code' => $order['hospital']['postalCode'],
            'distributor_name' => $distributor->distributorName,
            'order_method' => $distributor->orderMethod,
            'order_items' => $order['orderItems'],
            'division_name' => $order['division']['divisionName'],
            'order_date' => $order['orderDate'],
            'order_number' => $order['orderId'],
            'item_num' => $order['itemCount'],
            'useMedicode' => $useMedicode,
            'total_price' =>
                '￥' . number_format_jp((float) $order['totalAmount']),
            'slip_url' =>
                config('url.distributorBarcodeSearch', '') .
                '?searchValue=' .
                $order['orderId'],
            'login_url' => config('url.distributor', ''),
        ])->render();

        $ids = ModelRepository::getInvitingInstance()
            ->where('distributorId', $order['distributor']['distributorId'])
            ->where('invitingAgree', 't')
            ->get();

        $ruleId = ModelRepository::getInvitingMailInstance()
            ->subject('[JoyPla] 発注が行われました')
            ->standby(false)
            ->reserveDate('now')
            ->bodyText($mailBody)
            ->formAddress(FROM_ADDRESS)
            ->formName(FROM_NAME)
            ->mailField('mailAddress')
            ->regist();

        $ids = array_values(array_column($ids->toArray(), 'id'));

        ModelRepository::getInvitingMailInstance()
            ->ruleId($ruleId)
            ->sampling($ids);

        $mailBody = view('mail/Order/OrderFix', [
            'name' => '%val:usr:name%',
            'distributor_name' => $distributor->distributorName,
            'distributor_postal_code' => $distributor->postalCode,
            'distributor_prefectures' => $distributor->prefectures,
            'distributor_address' => $distributor->address,
            'order_method' => $distributor->orderMethod,
            'hospital_name' => $order['hospital']['hospitalName'],
            'postal_code' => $order['hospital']['postalCode'],
            'prefectures' => $order['hospital']['prefectures'],
            'address' => $order['hospital']['address'],
            'division_name' => $order['division']['divisionName'],
            'order_date' => $order['orderDate'],
            'order_number' => $order['orderId'],
            'item_num' => $order['itemCount'],
            'total_price' =>
                '￥' . number_format_jp((float) $order['totalAmount']),
            'login_url' => config('url.hospital', ''),
        ])->render();

        $ids = ModelRepository::getHospitalUserInstance()
            ->where('hospitalId', $user->hospitalId)
            ->whereIn('userPermission', [1, 3])
            ->get();
        $ids = array_values(array_column($ids->toArray(), 'id'));

        $ids2 = ModelRepository::getHospitalUserInstance()
            ->where('hospitalId', $user->hospitalId)
            ->where('divisionId', $order['division']['divisionId'])
            ->whereIn('userPermission', [2])
            ->value('id')
            ->get();

        $ids = array_merge(
            $ids,
            array_values(array_column($ids2->toArray(), 'id'))
        );

        $mailId = ModelRepository::getHospitalUserMailInstance()
            ->subject('[JoyPla] 発注が行われました')
            ->standby(false)
            ->reserveDate('now')
            ->bodyText($mailBody)
            ->formAddress(FROM_ADDRESS)
            ->formName(FROM_NAME)
            ->mailField('mailAddress')
            ->regist();

        ModelRepository::getHospitalUserMailInstance()
            ->ruleId($mailId)
            ->sampling($ids);
    }

    //** メンテナンス用 */
    public function all()
    {
        $items = ModelRepository::getOrderItemViewInstance()->get();
        $historys = ModelRepository::getOrderViewInstance()
            ->orWhere('orderStatus', OrderStatus::OrderCompletion)
            ->orWhere('orderStatus', OrderStatus::OrderFinished)
            ->orWhere('orderStatus', OrderStatus::PartOfTheCollectionIsIn)
            ->orWhere('orderStatus', OrderStatus::DeliveryDateReported)
            ->get();

        foreach ($historys->all() as $history) {
            $order = Order::create($history);

            foreach ($items->all() as $item) {
                if ($order->getOrderId()->equal($item->orderNumber)) {
                    $order = $order->addOrderItem(OrderItem::create($item));
                }
            }
            $orders[] = $order;
        }
        return $orders;
    }

    //** メンテナンス用 */
    public function updateAll(array $orders)
    {
        $orders = array_map(function (Order $order) {
            return $order;
        }, $orders);

        $history = [];
        $items = [];

        $deleteOrderIds = [];

        foreach ($orders as $oKey => $order) {
            $orderToArray = $order->toArray();
            $history[] = [
                'orderNumber' => (string) $orderToArray['orderId'],
                'orderTime' => (string) $orderToArray['orderDate'],
                'hospitalId' =>
                    (string) $orderToArray['hospital']['hospitalId'],
                'divisionId' =>
                    (string) $orderToArray['division']['divisionId'],
                'distributorId' =>
                    (string) $orderToArray['distributor']['distributorId'],
                'itemsNumber' => (string) $orderToArray['itemCount'],
                'totalAmount' => (string) $orderToArray['totalAmount'],
                'orderStatus' => (string) $orderToArray['orderStatus'],
                'adjustment' => (string) $orderToArray['adjustment'],
                'ordercomment' => (string) $orderToArray['orderComment'],
                'ordererUserName' => (string) $orderToArray['orderUserName'],
            ];

            foreach ($orderToArray['orderItems'] as $orderItem) {
                $item = [
                    'inHospitalItemId' =>
                        (string) $orderItem['inHospitalItemId'],
                    'orderNumber' => (string) $orderToArray['orderId'],
                    'orderCNumber' => (string) $orderItem['orderItemId'],
                    'price' => (string) $orderItem['price'],
                    'orderQuantity' => (string) $orderItem['orderQuantity'],
                    'receivingNum' => (string) $orderItem['receivedQuantity'],
                    'orderPrice' => (string) $orderItem['orderPrice'],
                    'hospitalId' => (string) $orderItem['hospitalId'],
                    'receivingFlag' => (string) $orderItem['receivedFlag'],
                    'divisionId' =>
                        (string) $orderItem['division']['divisionId'],
                    'distributorId' =>
                        (string) $orderItem['distributor']['distributorId'],
                    'quantity' =>
                        (string) $orderItem['quantity']['quantityNum'],
                    'quantityUnit' =>
                        (string) $orderItem['quantity']['quantityUnit'],
                    'itemUnit' => (string) $orderItem['quantity']['itemUnit'],
                    'lotManagement' => (string) $orderItem['lotManagement'],
                    'itemId' => (string) $orderItem['item']['itemId'],
                ];

                $items[] = $item;
            }
        }
        if (count($history) > 0) {
            ModelRepository::getOrderInstance()->updateBulk(
                'orderNumber',
                $history
            );
        }
        if (count($items) > 0) {
            ModelRepository::getOrderItemInstance()->updateBulk(
                'orderCNumber',
                $items
            );
        }
        return array_values($orders);
    }
}

interface OrderRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId);
    public function findByInHospitalItem(
        HospitalId $hospitalId,
        array $orderItems
    );
    public function getUnapprovedOrder(
        HospitalId $hospitalId,
        array $orderItems
    );
    public function saveToArray(HospitalId $hospitalId, array $orders);

    public function search(HospitalId $hospitalId, object $search);

    public function index(
        HospitalId $hospitalId,
        OrderId $orderId,
        array $orderStatus
    );

    public function delete(HospitalId $hospitalId, OrderId $orderId);

    public function sendUnapprovedOrderMail(array $orders, Auth $user);

    public function getOrderByOrderItemId(
        HospitalId $hospitalId,
        array $orderItemIds
    );

    public function sendApprovalOrderMail(Order $order, Auth $user);
    public function sendRevisedOrderMail(Order $order, Auth $user);
}
