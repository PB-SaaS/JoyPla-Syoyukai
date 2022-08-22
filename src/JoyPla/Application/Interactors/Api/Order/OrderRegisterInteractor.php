<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {

    use App\Model\Division;
    use Exception;
    use JoyPla\Application\InputPorts\Api\Order\OrderRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderRegisterInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\OrderRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\DateYearMonthDay;
    use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\OrderDate;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\Enterprise\Models\DivisionId;
    use JoyPla\Enterprise\Models\Hospital;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\HospitalName;
    use JoyPla\Enterprise\Models\OrderAdjustment;
    use JoyPla\Enterprise\Models\TextArea512Bytes;
    use JoyPla\InterfaceAdapters\GateWays\Repository\HospitalRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;

    /**
     * Class OrderRegisterInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderRegisterInteractor implements OrderRegisterInputPortInterface
    {
        /** @var OrderRegisterOutputPortInterface */
        private OrderRegisterOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;

        /**
         * OrderRegisterInteractor constructor.
         * @param OrderRegisterOutputPortInterface $outputPort
         */
        public function __construct(OrderRegisterOutputPortInterface $outputPort , OrderRepositoryInterface $orderRepository , HospitalRepositoryInterface $hospitalRepository)
        {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
            $this->hospitalRepository = $hospitalRepository;
        }

        /**
         * @param OrderRegisterInputData $inputData
         */
        public function handle(OrderRegisterInputData $inputData , $adjustment)
        {

            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $hospital = $this->hospitalRepository->find($hospitalId);

            $inputData->orderItems = array_map(function($v) use ($inputData){
                if($inputData->isOnlyMyDivision && $inputData->user->divisionId !== $v->divisionId)
                {
                    throw new Exception('Illegal request',403);
                }
                return $v;
            },$inputData->orderItems);

            $orderItems = $this->orderRepository->findByInHospitalItem( $hospitalId , $inputData->orderItems );
            $historyOrders = [];
            if($inputData->integrate)
            {
                $historyOrders = $this->orderRepository->getUnapprovedOrder($hospitalId , $orderItems);
            }
            $ids = [];
            $result = [];
            foreach($orderItems as $i)
            {
                $exist = false;
                if($inputData->integrate){
                    foreach($historyOrders as $key => $h)
                    {
                        if( 
                            $h->equalOrderSlip($i->getDivision() , $i->getDistributor()) 
                            && ( $h->isPlus() === $i->isPlus() || $h->isMinus() === $i->isMinus() )
                        )
                        { 
                            $exist = true;
                            $result[] = $h->addOrderItem($i);
                            break;
                        }
                    }
                }
                if(!$exist){
                    foreach($result as $key => $r)
                    {
                        if( 
                            $r->equalOrderSlip($i->getDivision() , $i->getDistributor()) 
                            && ( $r->isPlus() === $i->isPlus() || $r->isMinus() === $i->isMinus() )
                        )
                        { 
                            $exist = true;
                            $result[ $key ] = $r->addOrderItem($i);
                            break;
                        }
                    }
                }
                if($exist){ continue; }
                $id = OrderId::generate();
                //登録時には病院名は必要ないので、いったんhogeでいい
                $result[] = new Order( 
                    $id , 
                    ( new DateYearMonthDayHourMinutesSecond("") ), 
                    ( new DateYearMonthDayHourMinutesSecond("") ), 
                    [$i] , 
                    $hospital , 
                    $i->getDivision() , 
                    $i->getDistributor(), 
                    ( new OrderStatus(OrderStatus::UnOrdered) ) , 
                    ( new OrderAdjustment($adjustment)) ,
                    ( new TextArea512Bytes("")),
                    ( new TextArea512Bytes("")),
                    $inputData->user->name,
                    1
                );
            }


            $this->orderRepository->saveToArray( $hospitalId , $result);
            
            $unapprovedOrderMailViewModel = [];
            
            $unapprovedOrderDataModel = [
                'hospitalName' => $hospital->getHospitalName()->value() ,
                'ordererUserName'=> $inputData->user->name
            ];

            foreach($result as $order){
                $orderToArray = $order->toArray();
                $unapprovedOrderMailViewModel[] = [
                    'orderNumber' => $orderToArray['orderId'],
                    'divisionName' => $orderToArray['division']['divisionName'],
                    'distributorName' => $orderToArray['distributor']['distributorName'],
                    'totalAmount' => $orderToArray['totalAmount'],
                ];
            }
            
            $this->orderRepository->sendUnapprovedOrderMail($unapprovedOrderDataModel , $unapprovedOrderMailViewModel, $inputData->user);

            $ids = [];

            foreach($result as $order)
            {
                $ids[] = $order->getOrderId()->value();
            }

            $this->outputPort->output(new OrderRegisterOutputData($ids));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Order {

    use Auth;
    use stdClass;

    /**
     * Class OrderRegisterInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderRegisterInputData
    {
        /**
         * OrderRegisterInputData constructor.
         */
        public function __construct(Auth $user , array $orderItems , bool $integrate , bool $isOnlyMyDivision)
        {
            $this->user = $user;

            $this->orderItems = array_map(function($v){
                $object = new stdClass();
                $object->inHospitalItemId = $v['inHospitalItemId'];
                $object->orderUnitQuantity = $v['orderUnitQuantity'];
                $object->divisionId= $v['divisionId'];
                return $object;
            },$orderItems);

            $this->integrate = $integrate;

            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }
 
    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
    */
    interface OrderRegisterInputPortInterface
    {
        /**
         * @param OrderRegisterInputData $inputData
         */
        function handle(OrderRegisterInputData $inputData , $adjustment);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {

    /**
     * Class OrderRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderRegisterOutputData
    {
        /** @var string */

        /**
         * OrderRegisterOutputData constructor.
         */
        public function __construct(array $ids)
        {
            $this->ids = $ids;
        }
    }

    /**
     * Interface OrderRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
    */
    interface OrderRegisterOutputPortInterface
    {
        /**
         * @param OrderRegisterOutputData $outputData
         */
        function output(OrderRegisterOutputData $outputData);
    }
} 