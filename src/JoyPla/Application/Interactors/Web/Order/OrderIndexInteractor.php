<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Web\Order {

    use App\Model\Division;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Web\Order\OrderIndexInputData;
    use JoyPla\Application\InputPorts\Web\Order\OrderIndexInputPortInterface;
    use JoyPla\Application\OutputPorts\Web\Order\OrderIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Order\OrderIndexOutputPortInterface;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\InterfaceAdapters\GateWays\Repository\orderRepositoryInterface;

    /**
     * Class OrderIndexInteractor
     * @package JoyPla\Application\Interactors\Web\Order
     */
    class OrderIndexInteractor implements OrderIndexInputPortInterface
    {
        /** @var OrderIndexOutputPortInterface */
        private OrderIndexOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;

        /**
         * OrderIndexInteractor constructor.
         * @param OrderIndexOutputPortInterface $outputPort
         */
        public function __construct(OrderIndexOutputPortInterface $outputPort , OrderRepositoryInterface $orderRepository)
        {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
        }

        /**
         * @param OrderIndexInputData $inputData
         */
        public function handle(OrderIndexInputData $inputData)
        {

            $hospitalId = new HospitalId($inputData->hospitalId);
            $orderId = new OrderId($inputData->orderId);

            $orderstatus = [];
            if($inputData->isUnapproved)
            {
                $orderstatus = 
                [
                    OrderStatus::UnOrdered,
                ];
            } else 
            {
                $orderstatus = 
                [
                    OrderStatus::OrderCompletion,
                    OrderStatus::OrderFinished,
                    OrderStatus::DeliveryDateReported,
                    OrderStatus::PartOfTheCollectionIsIn,
                    OrderStatus::ReceivingIsComplete,
                    OrderStatus::DeliveryIsCanceled,
                    OrderStatus::Borrowing,
                ];
            }
            $order = $this->orderRepository->index(
                $hospitalId,
                $orderId,
                $orderstatus
            );

            if( $order === null )
            {
                throw new NotFoundException("Not Found.",404);
            }

            $this->outputPort->output(new OrderIndexOutputData($order));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Web\Order {

    use stdClass;

    /**
     * Class OrderIndexInputData
     * @package JoyPla\Application\InputPorts\Web\Order
     */
    class OrderIndexInputData
    {
        /**
         * OrderIndexInputData constructor.
         */
        public function __construct(string $hospitalId , string $orderId , bool $isUnapproved)
        {
            $this->hospitalId = $hospitalId;
            $this->orderId= $orderId;
            $this->isUnapproved = $isUnapproved;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Web\Order
    */
    interface OrderIndexInputPortInterface
    {
        /**
         * @param OrderIndexInputData $inputData
         */
        function handle(OrderIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Web\Order {

    use JoyPla\Enterprise\Models\Order;

    /**
     * Class OrderIndexOutputData
     * @package JoyPla\Application\OutputPorts\Web\Order;
     */
    class OrderIndexOutputData
    {
        /** @var string */

        /**
         * OrderIndexOutputData constructor.
         */
        public function __construct(Order $order)
        {
            $this->order = $order->toArray();
        }
    }

    /**
     * Interface OrderIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Web\Order;
    */
    interface OrderIndexOutputPortInterface
    {
        /**
         * @param OrderIndexOutputData $outputData
         */
        function output(OrderIndexOutputData $outputData);
    }
} 