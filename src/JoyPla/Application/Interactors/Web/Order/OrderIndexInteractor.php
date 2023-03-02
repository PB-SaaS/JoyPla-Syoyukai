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
    use JoyPla\InterfaceAdapters\GateWays\Repository\DivisionRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\HospitalRepositoryInterface;
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
        public function __construct(
            OrderIndexOutputPortInterface $outputPort,
            OrderRepositoryInterface $orderRepository,
            DivisionRepositoryInterface $divisionRepository
        ) {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
            $this->divisionRepository = $divisionRepository;
        }

        /**
         * @param OrderIndexInputData $inputData
         */
        public function handle(OrderIndexInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $orderId = new OrderId($inputData->orderId);

            $orderstatus = [];
            if ($inputData->isUnapproved) {
                $orderstatus = [OrderStatus::UnOrdered];
            } else {
                $orderstatus = [
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

            if ($order === null) {
                throw new NotFoundException('Not Found.', 404);
            }

            if (
                $inputData->isOnlyMyDivision &&
                !$order
                    ->getDivision()
                    ->getDivisionId()
                    ->equal($inputData->user->divisionId)
            ) {
                throw new NotFoundException('Not Found.', 404);
            }

            $order = $order->toArray();

            if ($order['receivedTarget'] == '2') {
                $order['receivedDivisionName'] =
                    $order['division']['divisionName'];
            }
            if ($order['receivedTarget'] == '1') {
                $receivedDivision = $this->divisionRepository->getStorehouse(
                    $hospitalId
                );
                $order[
                    'receivedDivisionName'
                ] = $receivedDivision->getDivisionName()->value();
            }

            $this->outputPort->output(new OrderIndexOutputData($order));
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Web\Order {
    use Auth;
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
        public function __construct(
            Auth $user,
            string $orderId,
            bool $isUnapproved,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->orderId = $orderId;
            $this->isUnapproved = $isUnapproved;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
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
        public function __construct(array $order)
        {
            $this->order = $order;
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
