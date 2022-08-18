<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Web\Received {

    use App\Model\Division;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Web\Received\OrderReceivedSlipIndexInputData;
    use JoyPla\Application\InputPorts\Web\Received\OrderReceivedSlipIndexInputPortInterface;
    use JoyPla\Application\OutputPorts\Web\Received\OrderReceivedSlipIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Received\OrderReceivedSlipIndexOutputPortInterface;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\InterfaceAdapters\GateWays\Repository\orderRepositoryInterface;

    /**
     * Class OrderReceivedSlipIndexInteractor
     * @package JoyPla\Application\Interactors\Web\Received
     */
    class OrderReceivedSlipIndexInteractor implements OrderReceivedSlipIndexInputPortInterface
    {
        /** @var OrderReceivedSlipIndexOutputPortInterface */
        private OrderReceivedSlipIndexOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;

        /**
         * OrderReceivedSlipIndexInteractor constructor.
         * @param OrderReceivedSlipIndexOutputPortInterface $outputPort
         */
        public function __construct(OrderReceivedSlipIndexOutputPortInterface $outputPort , OrderRepositoryInterface $orderRepository)
        {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
        }

        /**
         * @param OrderReceivedSlipIndexInputData $inputData
         */
        public function handle(OrderReceivedSlipIndexInputData $inputData)
        {

            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $orderId = new OrderId($inputData->orderId);

            $order = $this->orderRepository->index($hospitalId,
                $orderId , 
                [
                    OrderStatus::OrderCompletion,
                    OrderStatus::OrderFinished,
                    OrderStatus::DeliveryDateReported,
                    OrderStatus::PartOfTheCollectionIsIn,
                    OrderStatus::ReceivingIsComplete,
                    OrderStatus::DeliveryIsCanceled,
                    OrderStatus::Borrowing,
                ]);

            if( $order === null )
            {
                throw new NotFoundException("Not Found.",404);
            }

            if($inputData->isOnlyMyDivision && ! $order->getDivision()->getDivisionId()->equal($inputData->user->divisionId))
            {
                throw new NotFoundException("Not Found.",404);
            }

            $this->outputPort->output(new OrderReceivedSlipIndexOutputData($order));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Web\Received {

    use Auth;
    use stdClass;

    /**
     * Class OrderReceivedSlipIndexInputData
     * @package JoyPla\Application\InputPorts\Web\Received
     */
    class OrderReceivedSlipIndexInputData
    {
        /**
         * OrderReceivedSlipIndexInputData constructor.
         */
        public function __construct(Auth $user , string $orderId , bool $isOnlyMyDivision)
        {
            $this->user = $user;
            $this->orderId= $orderId;
            $this->isOnlyMyDivision= $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Web\Received
    */
    interface OrderReceivedSlipIndexInputPortInterface
    {
        /**
         * @param OrderReceivedSlipIndexInputData $inputData
         */
        function handle(OrderReceivedSlipIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Web\Received {

    use JoyPla\Enterprise\Models\Order;

    /**
     * Class OrderReceivedSlipIndexOutputData
     * @package JoyPla\Application\OutputPorts\Web\Received;
     */
    class OrderReceivedSlipIndexOutputData
    {
        /** @var string */

        /**
         * OrderReceivedSlipIndexOutputData constructor.
         */
        public function __construct(Order $order)
        {
            $this->order = $order->toArray();
        }
    }

    /**
     * Interface OrderReceivedSlipIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Web\Received;
    */
    interface OrderReceivedSlipIndexOutputPortInterface
    {
        /**
         * @param OrderReceivedSlipIndexOutputData $outputData
         */
        function output(OrderReceivedSlipIndexOutputData $outputData);
    }
} 