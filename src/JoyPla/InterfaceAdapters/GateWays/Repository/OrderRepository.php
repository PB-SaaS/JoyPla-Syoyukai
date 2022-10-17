<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Distributor as SpiralDbDistributor;
use App\SpiralDb\DistributorAffiliationView;
use App\SpiralDb\Order as SpiralDbOrder;
use App\SpiralDb\OrderItem as SpiralDbOrderItem;
use App\SpiralDb\OrderItemView;
use App\SpiralDb\OrderView;
use App\SpiralDb\HospitalUser;
use App\SpiralDb\InHospitalItemView;
use Auth;
use framework\SpiralConnecter\SpiralDB;
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
use JoyPla\Enterprise\Models\SelectName;

class OrderRepository implements OrderRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId)
    {
        $orderHistory = (OrderItemView::where('hospitalId', $hospitalId->value())->get())->data->all();

        return $orderHistory;
    }

    public function findByInHospitalItem(HospitalId $hospitalId, array $orderItems)
    {
        $division = SpiralDB::title('NJ_divisionDB')->value([
            "registrationTime",
            "divisionId",
            "hospitalId",
            "divisionName",
            "divisionType",
            "deleteFlag",
            "authkey",
            "deliveryDestCode"
        ]);

        foreach ($orderItems as $item) {
            $division->orWhere('divisionId', $item->divisionId);
        }

        $division = $division->get();
        $division = $division->all();

        $inHospitalItem = InHospitalItemView::where('hospitalId', $hospitalId->value());
        foreach ($orderItems as $item) {
            $inHospitalItem->orWhere('inHospitalItemId', $item->inHospitalItemId);
        }

        $inHospitalItem = ($inHospitalItem->get())->data->all();

        foreach ($orderItems as $item) {
            $division_find_key = array_search($item->divisionId, collect_column($division, 'divisionId'));
            $inHospitalItem_find_key = array_search($item->inHospitalItemId, collect_column($inHospitalItem, 'inHospitalItemId'));
            $result[] = new OrderItem(
                (new OrderId('') ),
                (OrderItemId::generate()),
                (new InHospitalItemId($inHospitalItem[$inHospitalItem_find_key]->inHospitalItemId) ),
                (Item::create($inHospitalItem[$inHospitalItem_find_key])),
                $hospitalId,
                (Division::create($division[$division_find_key])),
                (Distributor::create($inHospitalItem[$inHospitalItem_find_key])),
                (Quantity::create($inHospitalItem[$inHospitalItem_find_key])),
                (new Price($inHospitalItem[$inHospitalItem_find_key]->price) ),
                (new OrderQuantity((int)$item->orderUnitQuantity)),
                (new ReceivedQuantity((int)$item->receivingNum)),
                (new DateYearMonthDay('')),
                $inHospitalItem[$inHospitalItem_find_key]->distributorMCode,
                (int) $inHospitalItem[$inHospitalItem_find_key]->lotManagement,
                (int) $inHospitalItem[$inHospitalItem_find_key]->inItemImage,
                false,
                1,
            );
        }
        return $result;
    }

    public function saveToArray(HospitalId $hospitalId, array $orders, array $attr = [])
    {
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
                "orderNumber" => (string)$orderToArray['orderId'],
                "orderTime" => (string)$orderToArray['orderDate'],
                "hospitalId" => (string)$orderToArray['hospital']['hospitalId'],
                "divisionId" => (string)$orderToArray['division']['divisionId'],
                "distributorId" => (string)$orderToArray['distributor']['distributorId'],
                "itemsNumber" => (string)$orderToArray['itemCount'],
                "totalAmount" => (string)$orderToArray['totalAmount'],
                "orderStatus" => (string)$orderToArray['orderStatus'],
                "adjustment" => (string)$orderToArray['adjustment'],
                "ordercomment" => (string)$orderToArray['orderComment'],
                "ordererUserName" => (string)$orderToArray['orderUserName'],
            ];

            $receivingDivisionCode = '';
            if ($order->getOrderStatus()->value() === OrderStatus::OrderCompletion) {
                if ($order->getReceivedTarget() === 1) { // 大倉庫
                    
                    $division = SpiralDB::title('NJ_divisionDB')->value(["deliveryDestCode"])
                        ->where('hospitalId', $hospitalId->value())
                        ->where('divisionType', '1')->get();
                    
                    $receivingDivisionCode = $division->first()->deliveryDestCode;
                    //$receivingDivisionCode = (SpiralDbDivision::where('hospitalId', $hospitalId->value())->where('divisionType', '1')->get())->data->get(0)->deliveryDestCode;
                }
                if ($order->getReceivedTarget() === 2) { // 部署
                    $division = SpiralDB::title('NJ_divisionDB')->value(["deliveryDestCode"])
                        ->where('hospitalId', $hospitalId->value())
                        ->where('divisionId', $order->getDivision()->getDivisionId()->value())->get();
                        
                    $receivingDivisionCode = $division->first()->deliveryDestCode;
                    //$receivingDivisionCode =(SpiralDbDivision::where('hospitalId', $hospitalId->value())->where('divisionId', $order->getDivision()->getDivisionId()->value())->get())->data->get(0)->deliveryDestCode;
                }
            }

            foreach ($orderToArray['orderItems'] as $orderItem) {
                $item = [
                    "inHospitalItemId" => (string)$orderItem['inHospitalItemId'],
                    "orderNumber" => (string)$orderToArray['orderId'],
                    "orderCNumber" => (string)$orderItem['orderItemId'],
                    "price" => (string)$orderItem['price'],
                    "orderQuantity" => (string)$orderItem['orderQuantity'],
                    "receivingNum" => (string)$orderItem['receivedQuantity'],
                    "orderPrice" => (string)$orderItem['orderPrice'],
                    "hospitalId" => (string)$orderItem['hospitalId'],
                    "receivingFlag" => (string)$orderItem['receivedFlag'],
                    "divisionId" => (string)$orderItem['division']['divisionId'],
                    "distributorId" => (string)$orderItem['distributor']['distributorId'],
                    "quantity" => (string)$orderItem['quantity']['quantityNum'],
                    "quantityUnit" => (string)$orderItem['quantity']['quantityUnit'],
                    "itemUnit" => (string)$orderItem['quantity']['itemUnit'],
                    "lotManagement" => (string)$orderItem['lotManagement'],
                    "itemId" => (string)$orderItem['item']['itemId']
                ];

                if (isset($attr['isReceived']) === true) {
                    $item['receivingTime'] = 'now';
                }

                if (($order->getOrderStatus()->value() === OrderStatus::OrderCompletion) &&
                   ((bool)$orderItem['useMedicode'])) {
                    $item['deliveryDestCode'] = (string)$receivingDivisionCode;
                }
                $items[] = $item;
            }
        }

        if (count($deleteOrderIds) > 0) {
            $instance = SpiralDbOrder::getNewInstance();
            foreach ($deleteOrderIds as $orderId) {
                $instance->orWhere('orderNumber', $orderId->value());
            }
            $instance->delete();
        }

        if (count($items) > 0) {
            $instance = SpiralDbOrderItem::getNewInstance();
            foreach ($items as $item) {
                $instance->orWhere('orderNumber', $item['orderNumber']);
                $instance->where('orderCNumber', $item['orderCNumber'], '!=');
            }
            $instance->delete();
        }

        if (count($history) > 0) {
            SpiralDbOrder::getNewInstance()->upsert('orderNumber', $history);
        }
        if (count($items) > 0) {
            SpiralDbOrderItem::getNewInstance()->upsert('orderCNumber', $items);
        }
        return array_values($orders);
    }

    public function search(HospitalId $hospitalId, object $search)
    {
        $itemSearchFlag = false;
        $itemViewInstance = OrderItemView::where('hospitalId', $hospitalId->value())->value('orderNumber')->value('orderCNumber');
        $historyViewInstance = OrderView::where('hospitalId', $hospitalId->value());

        if ($search->itemName) {
            $itemViewInstance->orWhere('itemName', "%".$search->itemName."%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->makerName) {
            $itemViewInstance->orWhere('makerName', "%".$search->makerName."%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->itemCode) {
            $itemViewInstance->orWhere('itemCode', "%".$search->itemCode."%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->itemStandard) {
            $itemViewInstance->orWhere('itemStandard', "%".$search->itemStandard."%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->itemJANCode) {
            $itemViewInstance->orWhere('itemJANCode', "%".$search->itemJANCode."%", "LIKE");
            $itemSearchFlag = true;
        }

        if ($search->receivedFlag === 0) {
            $itemViewInstance->orWhere('receivingFlag', "0", "=");
            $itemViewInstance->orWhere('receivingFlag', "0", "ISNULL");
            $itemSearchFlag = true;
        }
        if ($search->receivedFlag === 1) {
            $itemViewInstance->where('receivingFlag', "1", "=");
            $itemSearchFlag = true;
        }

        $orderCNumbers = [];
        if ($itemSearchFlag) {
            $itemViewInstance = $itemViewInstance->get();
            if ($itemViewInstance->count == 0) {
                return [[],0];
            }
            foreach ($itemViewInstance->data->all() as $item) {
                $historyViewInstance = $historyViewInstance->orWhere('orderNumber', $item->orderNumber);
                $orderCNumbers[] = $item->orderCNumber;
            }
        }

        if (is_array($search->distributorIds) && count($search->distributorIds) > 0) {
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
            $nextMonth =  $registerDate->nextMonth();

            $historyViewInstance->where('registrationTime', $registerDate->format('Y-m-01'), '>=');
            $historyViewInstance->where('registrationTime', $nextMonth->format('Y-m-01'), '<');
        }


        if ($search->orderDate) {
            $yearMonth = new DateYearMonth($search->orderDate);
            $nextMonth =  $yearMonth->nextMonth();

            $historyViewInstance->where('orderTime', $yearMonth->format('Y-m-01'), '>=');
            $historyViewInstance->where('orderTime', $nextMonth->format('Y-m-01'), '<');
        }

        $historys = $historyViewInstance->sort('id', 'desc')->page($search->currentPage)->paginate($search->perPage);
        if ($historys->count == 0) {
            return [[],0];
        }

        $itemViewInstance = OrderItemView::getNewInstance()->where('hospitalId', $hospitalId->value());
        foreach ($historys->data->all() as $history) {
            $itemViewInstance = $itemViewInstance->orWhere('orderNumber', $history->orderNumber);
        }
        foreach ($orderCNumbers as $orderCNumber) {
            $itemViewInstance = $itemViewInstance->orWhere('orderCNumber', $orderCNumber);
        }

        $items = $itemViewInstance->get();
        $orders = [];
        foreach ($historys->data->all() as $history) {
            $order = Order::create($history);

            foreach ($items->data->all() as $item) {
                if ($order->getOrderId()->equal($item->orderNumber)) {
                    $order = $order->addOrderItem(OrderItem::create($item));
                }
            }

            $orders[] = $order;
        }

        return [ $orders , $historys->count ];
    }


    public function index(HospitalId $hospitalId, OrderId $orderId, array $orderStatus = [OrderStatus::UnOrdered])
    {
        $orderView = OrderView::where('hospitalId', $hospitalId->value())->where('orderNumber', $orderId->value());

        if (count($orderStatus) > 0) {
            foreach ($orderStatus as $o) {
                $orderView->orWhere('orderStatus', $o);
            }
        }

        $orderView = $orderView->get();

        if ((int)$orderView->count === 0) {
            return null;
        }
        $orderItemView = OrderItemView::sort('id', 'asc')->where('hospitalId', $hospitalId->value())->where('orderNumber', $orderId->value())->get();

        $order = Order::create($orderView->data->get(0));

        foreach ($orderItemView->data->all() as $item) {
            $order = $order->addOrderItem(OrderItem::create($item));
        }

        return $order;
    }

    public function delete(HospitalId $hospitalId, OrderId $orderId)
    {
        $result = SpiralDbOrder::where('hospitalId', $hospitalId->value())->where('orderNumber', $orderId->value())->delete();
        return $result->count;
    }

    public function getOrderByOrderItemId(HospitalId $hospitalId, array $orderItemIds)
    {
        $items = OrderItemView::getNewInstance()->where('hospitalId', $hospitalId->value())->value('orderNumber');
        if (count($orderItemIds) === 0) {
            return [];
        }
        foreach ($orderItemIds as $id) {
            $items->orWhere('orderCNumber', $id);
        }

        $items = $items->get();

        $historyViewInstance = OrderView::sort('id', 'desc')->where('hospitalId', $hospitalId->value());

        foreach ($items->data->all() as $item) {
            $historyViewInstance->orWhere('orderNumber', $item->orderNumber);
        }

        $historys = ($historyViewInstance->get())->data->all();

        $itemViewInstance = OrderItemView::getNewInstance()->where('hospitalId', $hospitalId->value());

        foreach ($historys as $history) {
            $itemViewInstance = $itemViewInstance->orWhere('orderNumber', $history->orderNumber);
        }

        $items = $itemViewInstance->get();
        $orders = [];
        foreach ($historys as $history) {
            $order = Order::create($history);

            foreach ($items->data->all() as $item) {
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
    public function getUnapprovedOrder(HospitalId $hospitalId, array $orderItems)
    {
        $orderItems = array_map(
            function (OrderItem $i) {
                return $i;
            },
            $orderItems
        );

        $historyViewInstance = OrderView::sort('id', 'desc')->where('hospitalId', $hospitalId->value())->where('orderStatus', '1');

        foreach ($orderItems as $item) {
            $historyViewInstance->where('divisionId', $item->getDivision()->getDivisionId()->value());
            $historyViewInstance->orWhere('distributorId', $item->getDistributor()->getDistributorId()->value());
        }

        $historys = ($historyViewInstance->get())->data->all();

        $itemViewInstance = OrderItemView::where('hospitalId', $hospitalId->value());

        foreach ($historys as $history) {
            $itemViewInstance = $itemViewInstance->orWhere('orderNumber', $history->orderNumber);
        }

        $items = $itemViewInstance->get();
        $orders = [];
        foreach ($historys as $history) {
            $order = Order::create($history);

            foreach ($items->data->all() as $item) {
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

        foreach ($orders as $order) {
            $orderToArray = $order->toArray();
            $unapprovedOrderMailViewModel[] = [
                'orderNumber' => $orderToArray['orderId'],
                'divisionName' => $orderToArray['division']['divisionName'],
                'distributorName' => $orderToArray['distributor']['distributorName'],
                'totalAmount' => number_format_jp($orderToArray['totalAmount']),
            ];
        }

        $mailBody = view('mail/Order/RegistUnapprovedOrderMail', [
            'name' => '%val:usr:name%',
            'hospitalName' => $orders[0]->getHospital()->getHospitalName()->value(),
            'ordererUserName' =>  $user->name,
            'history' => $unapprovedOrderMailViewModel,
            'url' => config('url.hospital',''),
        ], false)->render();

        $ids = SpiralDb::title('NJ_HUserDB')->where('hospitalId', $user->hospitalId)->value('id')->whereIn('userPermission' , [1,3])->get();
        
        $mailId = SpiralDb::mail('NJ_HUserDB')->subject('[JoyPla] 未発注書が作成されました')
            ->standby(false)->reserveDate('now')->bodyText($mailBody)->formAddress(FROM_ADDRESS)->formName(FROM_NAME)->mailField('mailAddress')->regist();

        $ids = array_values(array_column($ids->toArray(),'id'));

        SpiralDb::mail('NJ_HUserDB')->ruleId($mailId)->sampling($ids);

    }

    public function sendRevisedOrderMail(Order $order, Auth $user)
    {
        $order = $order->toArray();

        $distributor = SpiralDb::title('NJ_distributorDB')->where('distributorId', $order['distributor']['distributorId'])
            ->value([
                'distributorId',
                'distributorName',
                'postalCode',
                'prefectures',
                'address',
            ])->get();
        
        $distributor = $distributor->first();

        $useMedicode = in_array(true, array_map(function (array $orderItem) {
            return $orderItem['useMedicode'];
        }, $order['orderItems']), true);


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
            'total_price' => '￥'.number_format_jp((float)$order['totalAmount']),
            'slip_url' => config('url.distributorBarcodeSearch','')."?searchValue=".$order['orderId'],
            'login_url' => config('url.distributor',''),
        ])->render();

        $ids = SpiralDb::title('invitingDB')->where('distributorId', $order['distributor']['distributorId'])->where('invitingAgree', 't')->value('id')->get();
        
        $mailId = SpiralDb::mail('invitingDB')->subject('[JoyPla] 発注書に変更がありました')
            ->standby(false)->reserveDate('now')->bodyText($mailBody)->formAddress(FROM_ADDRESS)->formName(FROM_NAME)->mailField('mailAddress')->regist();

        $ids = array_values(array_column($ids->toArray(),'id'));

        SpiralDb::mail('invitingDB')->ruleId($mailId)->sampling($ids);

    }

    public function sendApprovalOrderMail(Order $order, Auth $user)
    {
        $order = $order->toArray();


        $distributor = SpiralDb::title('NJ_distributorDB')->where('distributorId', $order['distributor']['distributorId'])
            ->value([
                'distributorId',
                'distributorName',
                'postalCode',
                'prefectures',
                'address',
            ])->get();
        
        $distributor = $distributor->first();

        $useMedicode = in_array(true, array_map(function (array $orderItem) {
            return $orderItem['useMedicode'];
        }, $order['orderItems']), true);

        $mailBody = view('mail/Order/OrderFixForDistributor', [
            'name' => '%val:usr:name%',
            'hospital_name' => $order['hospital']['hospitalName'],
            'prefectures' => $order['hospital']['prefectures'],
            'address' => $order['hospital']['address'],
            'postal_code' => $order['hospital']['postalCode'],
            'distributor_name' => $distributor->distributorName,
            'division_name' => $order['division']['divisionName'],
            'order_date' => $order['orderDate'],
            'order_number' => $order['orderId'],
            'item_num' => $order['itemCount'],
            'useMedicode' => $useMedicode,
            'total_price' => '￥'.number_format_jp((float)$order['totalAmount']),
            'slip_url' => config('url.distributorBarcodeSearch','')."?searchValue=".$order['orderId'],
            'login_url' => config('url.distributor',''),
        ])->render();
        

        $ids = SpiralDb::title('invitingDB')->where('distributorId', $order['distributor']['distributorId'])->where('invitingAgree', 't')->value('id')->get();
                
        $mailId = SpiralDb::mail('invitingDB')->subject('[JoyPla] 発注が行われました')
            ->standby(false)->reserveDate('now')->bodyText($mailBody)->formAddress(FROM_ADDRESS)->formName(FROM_NAME)->mailField('mailAddress')->regist();

        $ids = array_values(array_column($ids->toArray(),'id'));

        SpiralDb::mail('invitingDB')->ruleId($mailId)->sampling($ids);

        $mailBody = view('mail/Order/OrderFix', [
            'name' => '%val:usr:name%',
            'distributor_name' => $distributor->distributorName,
            'distributor_postal_code' => $distributor->postalCode,
            'distributor_prefectures' => $distributor->prefectures,
            'distributor_address' => $distributor->address,
            'hospital_name' => $order['hospital']['hospitalName'],
            'postal_code' => $order['hospital']['postalCode'],
            'prefectures' => $order['hospital']['prefectures'],
            'address' =>  $order['hospital']['address'],
            'division_name' => $order['division']['divisionName'],
            'order_date' => $order['orderDate'],
            'order_number' => $order['orderId'],
            'item_num' => $order['itemCount'],
            'total_price' => '￥'.number_format_jp((float)$order['totalAmount']),
            'login_url' => config('url.hospital',''),
        ])->render();

        $ids = SpiralDb::title('NJ_HUserDB')->where('hospitalId', $user->hospitalId)->whereIn('userPermission' , [1,3])->value('id')->get();
        $ids = array_values(array_column($ids->toArray(),'id'));
        
        $ids2 = SpiralDb::title('NJ_HUserDB')->where('hospitalId', $user->hospitalId)->where('divisionId', $order['division']['divisionId'])->whereIn('userPermission' , [2])->value('id')->get();
        
        $ids = array_merge($ids , array_values(array_column($ids2->toArray(),'id')));

        $mailId = SpiralDb::mail('NJ_HUserDB')->subject('[JoyPla] 発注が行われました')
            ->standby(false)->reserveDate('now')->bodyText($mailBody)->formAddress(FROM_ADDRESS)->formName(FROM_NAME)->mailField('mailAddress')->regist();

        SpiralDb::mail('NJ_HUserDB')->ruleId($mailId)->sampling($ids);

    }

    //** メンテナンス用 */
    public function all()
    {
        $items = OrderItemView::get();
        $historys = OrderView::orWhere('orderStatus', OrderStatus::OrderCompletion)->orWhere('orderStatus', OrderStatus::OrderFinished)->orWhere('orderStatus', OrderStatus::PartOfTheCollectionIsIn)->orWhere('orderStatus', OrderStatus::DeliveryDateReported)->get();

        foreach ($historys->data->all() as $history) {
            $order = Order::create($history);

            foreach ($items->data->all() as $item) {
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
                "orderNumber" => (string)$orderToArray['orderId'],
                "orderTime" => (string)$orderToArray['orderDate'],
                "hospitalId" => (string)$orderToArray['hospital']['hospitalId'],
                "divisionId" => (string)$orderToArray['division']['divisionId'],
                "distributorId" => (string)$orderToArray['distributor']['distributorId'],
                "itemsNumber" => (string)$orderToArray['itemCount'],
                "totalAmount" => (string)$orderToArray['totalAmount'],
                "orderStatus" => (string)$orderToArray['orderStatus'],
                "adjustment" => (string)$orderToArray['adjustment'],
                "ordercomment" => (string)$orderToArray['orderComment'],
                "ordererUserName" => (string)$orderToArray['orderUserName'],
            ];

            foreach ($orderToArray['orderItems'] as $orderItem) {
                $item = [
                    "inHospitalItemId" => (string)$orderItem['inHospitalItemId'],
                    "orderNumber" => (string)$orderToArray['orderId'],
                    "orderCNumber" => (string)$orderItem['orderItemId'],
                    "price" => (string)$orderItem['price'],
                    "orderQuantity" => (string)$orderItem['orderQuantity'],
                    "receivingNum" => (string)$orderItem['receivedQuantity'],
                    "orderPrice" => (string)$orderItem['orderPrice'],
                    "hospitalId" => (string)$orderItem['hospitalId'],
                    "receivingFlag" => (string)$orderItem['receivedFlag'],
                    "divisionId" => (string)$orderItem['division']['divisionId'],
                    "distributorId" => (string)$orderItem['distributor']['distributorId'],
                    "quantity" => (string)$orderItem['quantity']['quantityNum'],
                    "quantityUnit" => (string)$orderItem['quantity']['quantityUnit'],
                    "itemUnit" => (string)$orderItem['quantity']['itemUnit'],
                    "lotManagement" => (string)$orderItem['lotManagement'],
                    "itemId" => (string)$orderItem['item']['itemId']
                ];

                $items[] = $item;
            }
        }
        if (count($history) > 0) {
            SpiralDB::title('NJ_OrderHDB')->updateBulk('orderNumber', $history);
        }
        if (count($items) > 0) {
            SpiralDB::title('NJ_OrderDB')->updateBulk('orderCNumber', $items);
        }
        return array_values($orders);
    }
}

interface OrderRepositoryInterface
{
    public function findByHospitalId(HospitalId $hospitalId);
    public function findByInHospitalItem(HospitalId $hospitalId, array $orderItems);
    public function getUnapprovedOrder(HospitalId $hospitalId, array $orderItems);
    public function saveToArray(HospitalId $hospitalId, array $orders);

    public function search(HospitalId $hospitalId, object $search);

    public function index(HospitalId $hospitalId, OrderId $orderId, array $orderStatus);

    public function delete(HospitalId $hospitalId, OrderId $orderId);

    public function sendUnapprovedOrderMail(array $orders, Auth $user);

    public function getOrderByOrderItemId(HospitalId $hospitalId, array $orderItemIds);
    
    public function sendApprovalOrderMail(Order $order, Auth $user);
    public function sendRevisedOrderMail(Order $order, Auth $user);
}
