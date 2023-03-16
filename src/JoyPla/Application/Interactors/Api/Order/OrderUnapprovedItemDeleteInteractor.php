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
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class OrderUnapprovedItemDeleteInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderUnapprovedItemDeleteInteractor implements
        OrderUnapprovedItemDeleteInputPortInterface
    {
        private PresenterProvider $presenterProvider;
        private RepositoryProvider $repositoryProvider;

        public function __construct(
            PresenterProvider $presenterProvider,
            RepositoryProvider $repositoryProvider
        ) {
            $this->presenterProvider = $presenterProvider;
            $this->repositoryProvider = $repositoryProvider;
        }

        /**
         * @param OrderUnapprovedItemDeleteInputData $inputData
         */
        public function handle(OrderUnapprovedItemDeleteInputData $inputData)
        {
            $order = $this->repositoryProvider
                ->getOrderRepository()
                ->index(
                    new HospitalId($inputData->user->hospitalId),
                    new OrderId($inputData->orderId),
                    [OrderStatus::UnOrdered]
                );

            if ($order === null) {
                throw new Exception('Invalid value.', 422);
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

            if (
                !$order->isExistOrderItemId(
                    new OrderItemId($inputData->orderItemId)
                )
            ) {
                throw new Exception('Invalid value.', 422);
            }

            $order = $order->deleteItem(
                new OrderItemId($inputData->orderItemId)
            );
            $orders = $this->repositoryProvider
                ->getOrderRepository()
                ->saveToArray(new HospitalId($inputData->user->hospitalId), [
                    $order,
                ]);

            $isOrderDeleted = true;
            foreach ($orders as $o) {
                if ($o->getOrderId()->equal($order->getOrderId()->value())) {
                    $isOrderDeleted = false;
                }
            }

            $this->presenterProvider
                ->getOrderUnapprovedItemDeletePresenter()
                ->output(
                    new OrderUnapprovedItemDeleteOutputData($isOrderDeleted)
                );
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
        public Auth $user;
        public string $orderId;
        public string $orderItemId;
        public string $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            string $orderId,
            string $orderItemId,
            bool $isOnlyMyDivision
        ) {
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
        public array $data;

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
