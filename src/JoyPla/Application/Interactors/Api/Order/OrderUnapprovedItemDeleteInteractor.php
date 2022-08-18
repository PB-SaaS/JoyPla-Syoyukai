<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {

    use ApiErrorCode\AccessFrequencyLimitExceededScopeIs;
    use App\Model\Division;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedItemDeleteInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedItemDeleteInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedItemDeleteOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedItemDeleteOutputPortInterface;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\OrderItemId;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;

    /**
     * Class OrderUnapprovedItemDeleteInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderUnapprovedItemDeleteInteractor implements OrderUnapprovedItemDeleteInputPortInterface
    {
        /** @var OrderUnapprovedItemDeleteOutputPortInterface */
        private OrderUnapprovedItemDeleteOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;

        /**
         * OrderUnapprovedItemDeleteInteractor constructor.
         * @param OrderUnapprovedItemDeleteOutputPortInterface $outputPort
         */
        public function __construct(OrderUnapprovedItemDeleteOutputPortInterface $outputPort , OrderRepositoryInterface $orderRepository)
        {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
        }

        /**
         * @param OrderUnapprovedItemDeleteInputData $inputData
         */
        public function handle(OrderUnapprovedItemDeleteInputData $inputData)
        {
            $order = $this->orderRepository->index(
                (new HospitalId($inputData->hospitalId)),
                (new OrderId($inputData->orderId)),
                [
                    OrderStatus::UnOrdered,
                ]
            );

        
            if( $order === null )
            {
                throw new Exception("Invalid value.",422);
            }

            if($inputData->isOnlyMyDivision && ! $order->getDivision()->getDivisionId()->equal($inputData->user->divisionId))
            {
                throw new NotFoundException("Not Found.",404);
            }

            if( ! $order->isExistOrderItemId((new OrderItemId($inputData->orderItemId))))
            {
                throw new Exception("Invalid value.",422);
            }

            $order = $order->deleteItem((new OrderItemId($inputData->orderItemId)));
            $orders = $this->orderRepository->saveToArray((new HospitalId($inputData->user->hospitalId)),[$order]);

            $isOrderDeleted = true;
            foreach($orders as $o)
            {

                if($o->getOrderId()->equal($order->getOrderId()->value()))
                {
                    $isOrderDeleted = false;
                }
            }

            $this->outputPort->output(new OrderUnapprovedItemDeleteOutputData($isOrderDeleted));
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
     * Class OrderUnapprovedItemDeleteInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderUnapprovedItemDeleteInputData
    {
        /**
         * OrderUnapprovedItemDeleteInputData constructor.
         */
        public function __construct(Auth $user , string $orderId , string $orderItemId , bool $isOnlyMyDivision)
        {
            $this->user = $user;
            $this->orderId = $orderId;
            $this->orderItemId = $orderItemId;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
    */
    interface OrderUnapprovedItemDeleteInputPortInterface
    {
        /**
         * @param OrderUnapprovedItemDeleteInputData $inputData
         */
        function handle(OrderUnapprovedItemDeleteInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {

    use JoyPla\Enterprise\Models\Order;

    /**
     * Class OrderUnapprovedItemDeleteOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderUnapprovedItemDeleteOutputData
    {
        /** @var string */

        /**
         * OrderUnapprovedItemDeleteOutputData constructor.
         */
        
        public function __construct(bool $isOrderDeleted)
        {
            $this->data = [
                'isOrderDeleted' => $isOrderDeleted,
            ];
        }
    }

    /**
     * Interface OrderUnapprovedItemDeleteOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
    */
    interface OrderUnapprovedItemDeleteOutputPortInterface
    {
        /**
         * @param OrderUnapprovedItemDeleteOutputData $outputData
         */
        function output(OrderUnapprovedItemDeleteOutputData $outputData);
    }
} 