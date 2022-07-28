<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Distributor as SpiralDbDistributor;
use App\SpiralDb\Order as SpiralDbOrder;
use App\SpiralDb\OrderItem as SpiralDbOrderItem;
use App\SpiralDb\OrderItemView;
use App\SpiralDb\OrderView;
use App\SpiralDb\Division as SpiralDbDivision;
use App\SpiralDb\HospitalUser;
use App\SpiralDb\InHospitalItemView;
use Auth;
use JoyPla\Enterprise\Models\Order;
use JoyPla\Enterprise\Models\OrderId;
use JoyPla\Enterprise\Models\OrderItem;
use JoyPla\Enterprise\Models\DateYearMonth;
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

class OrderRepository implements OrderRepositoryInterface{

    public function findByHospitalId( HospitalId $hospitalId )
    {
        $orderHistory = (OrderItemView::where('hospitalId',$hospitalId->value())->get())->data->all();

        return $orderHistory;
    }

    public function findByInHospitalItem( HospitalId $hospitalId , array $orderItems ){

        $division = SpiralDbDivision::where('hospitalId',$hospitalId->value());
        foreach($orderItems as $item){
            $division = $division->orWhere('divisionId',$item->divisionId);
        }

        $division = ($division->get())->data->all();
        
        $inHospitalItem = InHospitalItemView::where('hospitalId',$hospitalId->value());
        foreach($orderItems as $item){
            $inHospitalItem->orWhere('inHospitalItemId',$item->inHospitalItemId);
        }

        $inHospitalItem = ($inHospitalItem->get())->data->all();

        foreach($orderItems as $item){
            $division_find_key = array_search($item->divisionId, collect_column($division, 'divisionId'));
            $inHospitalItem_find_key = array_search($item->inHospitalItemId, collect_column($inHospitalItem, 'inHospitalItemId'));
            $result[] = new OrderItem(
                (new OrderId('') ),
                (OrderItemId::generate() ),
                (new InHospitalItemId($inHospitalItem[$inHospitalItem_find_key]->inHospitalItemId) ),
                (Item::create($inHospitalItem[$inHospitalItem_find_key]) ),
                $hospitalId,
                (Division::create($division[$division_find_key]) ),
                (Distributor::create($inHospitalItem[$inHospitalItem_find_key]) ),
                (Quantity::create($inHospitalItem[$inHospitalItem_find_key]) ),
                (new Price($inHospitalItem[$inHospitalItem_find_key]->price) ),
                (new OrderQuantity((int)$item->orderUnitQuantity)),
                (new ReceivedQuantity((int)$item->receivingNum)),
                (int) $inHospitalItem[$inHospitalItem_find_key]->lotManagement,
            );
        }
        return $result;
    }

    public function saveToArray(HospitalId $hospitalId , array $orders)
    {
        $orders = array_map(function(Order $order){
            return $order;
        },$orders);

        $history = [];
        $items = [];

        $deleteOrderIds = [];

        foreach($orders as $oKey => $order){
            if($order->itemCount() === 0)
            {
                $deleteOrderIds[] = $order->getOrderId();
                unset($orders[$oKey]);
                continue;
            }

            $orderToArray = $order->toArray();

            $history[] = [ 
                "orderNumber" => $orderToArray['orderId'],
                "orderTime" => $orderToArray['orderDate'],
                "hospitalId" => $orderToArray['hospital']['hospitalId'],
                "divisionId" => $orderToArray['division']['divisionId'],
                "distributorId" => $orderToArray['distributor']['distributorId'],
                "itemsNumber" => $orderToArray['itemCount'],
                "totalAmount" => $orderToArray['totalAmount'],
                "orderStatus" => $orderToArray['orderStatus'],
                "adjustment" => $orderToArray['adjustment'],
                "ordererUserName" => $orderToArray['orderUserName'],
            ];

            foreach( $orderToArray['orderItems'] as $orderItem )
            {
                $items[] = [
                    "inHospitalItemId" => $orderItem['inHospitalItemId'],
                    "orderNumber" => $orderToArray['orderId'],
                    "orderCNumber" => $orderItem['orderItemId'],
                    "price" => $orderItem['price'],
                    "orderQuantity" => $orderItem['orderQuantity'],
                    "receivingNum" => $orderItem['receivedQuantity'],
                    "orderPrice" => $orderItem['orderPrice'],
                    "hospitalId" => $orderItem['hospitalId'],
                    "divisionId" => $orderItem['division']['divisionId'],
                    "distributorId" => $orderItem['distributor']['distributorId'],
                    "quantity" => $orderItem['quantity']['quantityNum'],
                    "quantityUnit" => $orderItem['quantity']['quantityUnit'],
                    "itemUnit" => $orderItem['quantity']['itemUnit'],
                    "lotManagement" => $orderItem['lotManagement'],
                    "itemId" => $orderItem['item']['itemId'],
                ];
            }
        }

        if( count($deleteOrderIds) > 0 ) {
            $instance = SpiralDbOrder::getNewInstance();
            foreach($deleteOrderIds as $orderId)
            {
                $instance->orWhere('orderNumber',$orderId->value());
            }
            $instance->delete();
        }

        if(count($items) > 0) {
            $instance = SpiralDbOrderItem::getNewInstance();
            foreach($items as $item)
            {
                $instance->orWhere('orderNumber',$item['orderNumber']);
                $instance->where('orderCNumber',$item['orderCNumber'], '!=');
            }
            $instance->delete();
        }

        if(count($history) > 0) {
            SpiralDbOrder::getNewInstance()->upsert('orderNumber',$history);
        }
        if(count($items) > 0) {
            SpiralDbOrderItem::getNewInstance()->upsert('orderCNumber',$items);
        }

        return array_values($orders);
    }

    public function search( HospitalId $hospitalId , object $search)
    {
        $itemSearchFlag = false;
        $itemViewInstance = OrderItemView::where('hospitalId',$hospitalId->value())->value('orderNumber');
        $historyViewInstance = OrderView::where('hospitalId',$hospitalId->value());

        if($search->itemName !== "")
        {
            $itemViewInstance->orWhere('itemName',"%".$search->itemName."%","LIKE");
            $itemSearchFlag = true; 
        }
        if($search->makerName !== "")
        {
            $itemViewInstance->orWhere('makerName',"%".$search->makerName."%","LIKE");
            $itemSearchFlag = true;
        }
        if($search->itemCode !== "")
        {
            $itemViewInstance->orWhere('itemCode',"%".$search->itemCode."%","LIKE");
            $itemSearchFlag = true;
        }
        if($search->itemStandard !== "")
        {
            $itemViewInstance->orWhere('itemStandard',"%".$search->itemStandard."%","LIKE");
            $itemSearchFlag = true;
        }
        if($search->itemJANCode !== "")
        {
            $itemViewInstance->orWhere('itemJANCode',"%".$search->itemJANCode."%","LIKE");
            $itemSearchFlag = true;
        }
        
        if($itemSearchFlag) {
            $itemViewInstance = $itemViewInstance->get();
            if($itemViewInstance->count == 0 )
            {
                return [[],0];
            }
            foreach($itemViewInstance->data->all() as $item){
                $historyViewInstance = $historyViewInstance->orWhere('orderNumber' , $item->orderNumber);
            }
        }

        if(is_array($search->divisionIds) && count($search->divisionIds) > 0)
        {   
            foreach($search->divisionIds as $divisionId){
                $historyViewInstance->orWhere('divisionId', $divisionId);
            }
        }   

        if(is_array($search->orderStatus) && count($search->orderStatus) > 0)
        {   
            foreach($search->orderStatus as $orderStatus){
                $historyViewInstance->orWhere('orderStatus', $orderStatus);
            }
        }
        
        if($search->registerDate !== ""){
            $registerDate = new DateYearMonth($search->registerDate);
            $nextMonth =  $registerDate->nextMonth();

            $historyViewInstance->where('registrationTime', $registerDate->format('Y-m-01'), '>=');
            $historyViewInstance->where('registrationTime', $nextMonth->format('Y-m-01'), '<');
        }


        if($search->orderDate !== ""){
            $yearMonth = new DateYearMonth($search->orderDate);
            $nextMonth =  $yearMonth->nextMonth();

            $historyViewInstance->where('orderTime', $yearMonth->format('Y-m-01'), '>=');
            $historyViewInstance->where('orderTime', $nextMonth->format('Y-m-01'), '<');
        }

        $historys = $historyViewInstance->sort('id','desc')->page($search->currentPage)->paginate($search->perPage);
        if($historys->count == 0)
        {
            return [[],0];
        }
        
        $itemViewInstance = OrderItemView::getNewInstance()->where('hospitalId',$hospitalId->value());
        foreach($historys->data->all() as $history){
            $itemViewInstance = $itemViewInstance->orWhere('orderNumber' , $history->orderNumber);
        }

        $items = $itemViewInstance->get();
        $orders = [];
        foreach($historys->data->all() as $history)
        {
           $order = Order::create($history);

            foreach($items->data->all() as $item) {
                if( $order->getOrderId()->equal($item->orderNumber) )
                {
                    $order = $order->addOrderItem(OrderItem::create($item));
                }
            }

            $orders[] = $order;
        }

        return [ $orders , $historys->count ];
    }


    public function index( HospitalId $hospitalId , OrderId $orderId , array $orderStatus = [OrderStatus::UnOrdered])
    {
        $orderView = OrderView::where('hospitalId',$hospitalId->value())->where('orderNumber',$orderId->value());

        if(count($orderStatus) > 0)
        {
            foreach($orderStatus as $o)
            {
                $orderView->orWhere('orderStatus', $o );
            }
        }
        
        $orderView = $orderView->get();

        if((int)$orderView->count === 0)
        {
            return null;
        }
        $orderItemView = OrderItemView::sort('id','asc')->where('hospitalId',$hospitalId->value())->where('orderNumber',$orderId->value())->get();
        
        $order = Order::create($orderView->data->get(0));
        
        foreach($orderItemView->data->all() as $item) {
            $order = $order->addOrderItem(OrderItem::create($item));
        }

        return $order;
    }

    public function delete( HospitalId $hospitalId , OrderId $orderId)
    {
        $result = SpiralDbOrder::where('hospitalId',$hospitalId->value())->where('orderNumber',$orderId->value())->delete();
        return $result->count;
    }
    

    /**
     * getUnapprovedOrder function
     *
     * @param HospitalId $hospitalId
     * @param OrderItem[] $orderItems
     * @return void
     */
    public function getUnapprovedOrder( HospitalId $hospitalId , array $orderItems )
    {
        $orderItems = array_map(
            function(OrderItem $i){
                return $i;
            },$orderItems
        );

        $historyViewInstance = OrderView::sort('id','desc')->where('hospitalId',$hospitalId->value())->where('orderStatus','1');

        foreach($orderItems as $item)
        {
            $historyViewInstance->where('divisionId',$item->getDivision()->getDivisionId()->value());
            $historyViewInstance->orWhere('distributorId',$item->getDistributor()->getDistributorId()->value());
        }

        $historys = ($historyViewInstance->get())->data->all();

        $itemViewInstance = OrderItemView::where('hospitalId',$hospitalId->value());

        foreach($historys as $history){
            $itemViewInstance = $itemViewInstance->orWhere('orderNumber' , $history->orderNumber);
        }

        $items = $itemViewInstance->get();
        $orders = [];
        foreach($historys as $history)
        {
           $order = Order::create($history);

            foreach($items->data->all() as $item) {
                if( $order->getOrderId()->equal($item->orderNumber) )
                {
                    $order = $order->addOrderItem(OrderItem::create($item));
                }
            }

            $orders[] = $order;
        }

        return $orders;
    }

    public function sendUnapprovedOrderMail(array $unapprovedOrderDataModel , array $unapprovedOrderItemDataModel , Auth $user)
    {
        $mail_body = view('mail/Order/RegistUnapprovedOrderMail', [
            'name' => '%val:usr:name%',
            'hospitalName' => $unapprovedOrderDataModel['hospitalName'],
            'ordererUserName' => $unapprovedOrderDataModel['ordererUserName'],
            'history' => $unapprovedOrderItemDataModel,
            'url' => LOGIN_URL,
        ] , false)->render();
        $hospitalUser = HospitalUser::getNewInstance();
        $selectName = SelectName::generate($user->hospitalId);
        $hospitalUser::selectName($selectName->value())
            ->rule(['name'=>'hospitalId','label'=>'name_'.($user->hospitalId),'value1'=>($user->hospitalId),'condition'=>'matches'])
            ->rule(['name'=>'userPermission','label'=>'permission_admin2','value1'=>'1,3','condition'=>'contains'])
            ->filterCreate();
        $hospitalUser::selectRule($selectName->value())
            ->body($mail_body)
            ->subject('[JoyPla] 未発注書が作成されました')
            ->from(FROM_ADDRESS,FROM_NAME)
            ->send();
    }
}

interface OrderRepositoryInterface 
{
    public function findByHospitalId( HospitalId $hospitalId );
    public function findByInHospitalItem( HospitalId $hospitalId , array $orderItems );
    public function getUnapprovedOrder( HospitalId $hospitalId , array $orderItems );
    public function saveToArray(HospitalId $hospitalId , array $orders);

    public function search( HospitalId $hospitalId , object $search);

    public function index( HospitalId $hospitalId , OrderId $orderId ,  array $orderStatus);

    public function delete( HospitalId $hospitalId , OrderId $orderId);

    public function sendUnapprovedOrderMail(array $unapprovedOrderDataModel , array $unapprovedOrderItemDataModel , Auth $user);
    
}