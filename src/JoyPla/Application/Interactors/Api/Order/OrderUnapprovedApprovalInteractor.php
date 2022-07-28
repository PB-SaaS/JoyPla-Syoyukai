<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {

    use ApiErrorCode\AccessFrequencyLimitExceededScopeIs;
    use App\Model\Division;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedApprovalInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedApprovalInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedApprovalOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedApprovalOutputPortInterface;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\OrderAdjustment;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\OrderItemId;
    use JoyPla\Enterprise\Models\OrderQuantity;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;

    /**
     * Class OrderUnapprovedApprovalInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderUnapprovedApprovalInteractor implements OrderUnapprovedApprovalInputPortInterface
    {
        /** @var OrderUnapprovedApprovalOutputPortInterface */
        private OrderUnapprovedApprovalOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;

        /**
         * OrderUnapprovedApprovalInteractor constructor.
         * @param OrderUnapprovedApprovalOutputPortInterface $outputPort
         */
        public function __construct(OrderUnapprovedApprovalOutputPortInterface $outputPort , OrderRepositoryInterface $orderRepository)
        {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
        }

        /**
         * @param OrderUnapprovedApprovalInputData $inputData
         */
        public function handle(OrderUnapprovedApprovalInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->hospitalId);
            $orderId = new OrderId($inputData->orderId);
            $order = $this->orderRepository->index(
                $hospitalId,
                $orderId,
                [
                    OrderStatus::UnOrdered,
                ]
            );

            if( $order === null ){
                throw new NotFoundException("Not Found.",404);
            }

            $order = $order->approval();
            
            $this->orderRepository->saveToArray($hospitalId , [$order]);
            
            $this->outputPort->output(new OrderUnapprovedApprovalOutputData($order));
        }
    }
}



/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Order {

    use stdClass;

    /**
     * Class OrderUnapprovedApprovalInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderUnapprovedApprovalInputData
    {
        /**
         * OrderUnapprovedApprovalInputData constructor.
         */
        public function __construct(string $hospitalId , string $orderId )
        {
            $this->hospitalId = $hospitalId;
            $this->orderId = $orderId;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
    */
    interface OrderUnapprovedApprovalInputPortInterface
    {
        /**
         * @param OrderUnapprovedApprovalInputData $inputData
         */
        function handle(OrderUnapprovedApprovalInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {

    use JoyPla\Enterprise\Models\Order;

    /**
     * Class OrderUnapprovedApprovalOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderUnapprovedApprovalOutputData
    {
        /** @var string */

        /**
         * OrderUnapprovedApprovalOutputData constructor.
         */
        
        public function __construct(Order $order)
        {
            $this->data = $order->toArray();
            $this->count = count($order->toArray());
        }
    }

    /**
     * Interface OrderUnapprovedApprovalOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
    */
    interface OrderUnapprovedApprovalOutputPortInterface
    {
        /**
         * @param OrderUnapprovedApprovalOutputData $outputData
         */
        function output(OrderUnapprovedApprovalOutputData $outputData);
    }
} 