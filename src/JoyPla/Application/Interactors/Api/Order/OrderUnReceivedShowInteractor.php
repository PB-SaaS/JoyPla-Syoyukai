<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {

    use JoyPla\Application\InputPorts\Api\Order\OrderUnReceivedShowInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderUnReceivedShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnReceivedShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnReceivedShowOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;

    /**
     * Class OrderUnReceivedShowInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderUnReceivedShowInteractor implements OrderUnReceivedShowInputPortInterface
    {
        /** @var OrderUnReceivedShowOutputPortInterface */
        private OrderUnReceivedShowOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;

        /**
         * OrderUnReceivedShowInteractor constructor.
         * @param OrderUnReceivedShowOutputPortInterface $outputPort
         */
        public function __construct(OrderUnReceivedShowOutputPortInterface $outputPort , OrderRepositoryInterface $orderRepository)
        {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
        }

        /**
         * @param OrderUnReceivedShowInputData $inputData
         */
        public function handle(OrderUnReceivedShowInputData $inputData)
        {
            [ $orders , $count ] = $this->orderRepository->search(
                (new HospitalId($inputData->user->hospitalId)) ,
                $inputData->search
            );
            $this->outputPort->output(new OrderUnReceivedShowOutputData($orders, $count));
        }
    } 
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Order {

    use Auth;
    use JoyPla\Enterprise\Models\OrderStatus;
    use stdClass;

    /**
     * Class OrderUnReceivedShowInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderUnReceivedShowInputData
    {
        /**
         * OrderUnReceivedShowInputData constructor.
         */
        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->registerDate = $search['registerDate'];
            $this->search->orderDate = $search['orderDate'];
            $this->search->itemName = $search['itemName'];
            $this->search->makerName = $search['makerName'];
            $this->search->itemCode = $search['itemCode'];
            $this->search->itemStandard = $search['itemStandard'];
            $this->search->itemJANCode = $search['itemJANCode'];
            $this->search->distributorIds= $search['distributorIds'];
            $this->search->divisionIds = $search['divisionIds'];
            $this->search->perPage= $search['perPage'];
            $this->search->currentPage= $search['currentPage'];
            $this->search->receivedFlag = 0; // null("") is not search
            $this->search->orderStatus = [
                OrderStatus::OrderCompletion,
                OrderStatus::OrderFinished,
                OrderStatus::PartOfTheCollectionIsIn,
                OrderStatus::DeliveryDateReported,
            ];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
    */
    interface OrderUnReceivedShowInputPortInterface
    {
        /**
         * @param OrderUnReceivedShowInputData $inputData
         */
        function handle(OrderUnReceivedShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {

    use Collection;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\OrderUnReceived;

    /**
     * Class OrderUnReceivedShowOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderUnReceivedShowOutputData
    {
        /**
         * OrderUnReceivedShowOutputData constructor.
         */
        public function __construct(array $orders , int $count)
        {
            $this->orders = array_map(function(Order $order){
                return $order->toArray();
            },$orders);
            $this->count = $count;
        }
    }

    /**
     * Interface OrderUnReceivedShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
    */
    interface OrderUnReceivedShowOutputPortInterface
    {
        /**
         * @param OrderUnReceivedShowOutputData $outputData
         */
        function output(OrderUnReceivedShowOutputData $outputData);
    }
}