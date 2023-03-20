<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {
    use Exception;
    use JoyPla\Application\InputPorts\Api\Order\OrderItemBulkUpdateInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderItemBulkUpdateInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\OrderItemBulkUpdateOutputData;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\OrderQuantity;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class OrderItemBulkUpdateInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderItemBulkUpdateInteractor implements
        OrderItemBulkUpdateInputPortInterface
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
         * @param OrderItemBulkUpdateInputData $inputData
         */
        public function handle(OrderItemBulkUpdateInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $orderItemIds = array_map(function ($item) {
                return $item['orderItemId'];
            }, $inputData->orderItems);
            $orders = $this->repositoryProvider
                ->getOrderRepository()
                ->getOrderByOrderItemId($hospitalId, $orderItemIds);

            foreach ($orders as $order) {
                if (
                    $order->getOrderStatus()->value() !== OrderStatus::UnOrdered
                ) {
                    throw new Exception('is not UnOrdered Data.', 400);
                }
            }

            foreach ($inputData->orderItems as $orderItem) {
                foreach ($orders as &$order) {
                    $orderItems = $order->getOrderItems();

                    foreach ($orderItems as &$item) {
                        if (
                            $item->getOrderItemId()->value() ===
                            $orderItem['orderItemId']
                        ) {
                            if ((int) $orderItem['orderQuantity'] === 0) {
                                $item = null;
                                continue;
                            }
                            $item = $item->setOrderQuantity(
                                new OrderQuantity(
                                    (int) $orderItem['orderQuantity']
                                )
                            );
                        }
                    }
                    $orderItems = array_filter($orderItems, function ($item) {
                        return $item !== null;
                    });
                    $order = $order->setOrderItems($orderItems);
                }
            }

            $this->repositoryProvider
                ->getOrderRepository()
                ->saveToArray($hospitalId, $orders);

            $this->presenterProvider->getOrderItemBulkUpdatePresenter()->output(
                new OrderItemBulkUpdateOutputData(
                    array_map(function ($order) {
                        return $order->toArray();
                    }, $orders),
                    count($orders)
                )
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
     * Class OrderItemBulkUpdateInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderItemBulkUpdateInputData
    {
        public Auth $user;
        public array $orderItems;

        public function __construct(Auth $user, array $orderItems)
        {
            $this->user = $user;
            $this->orderItems = array_map(function ($item) {
                return [
                    'orderItemId' => $item['orderItemId'],
                    'orderQuantity' => $item['orderQuantity'],
                ];
            }, $orderItems);
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    interface OrderItemBulkUpdateInputPortInterface
    {
        /**
         * @param OrderItemBulkUpdateInputData $inputData
         */
        function handle(OrderItemBulkUpdateInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {
    /**
     * Class OrderItemBulkUpdateOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderItemBulkUpdateOutputData
    {
        public int $count;
        public array $orders;

        public function __construct(array $orders, int $count)
        {
            $this->count = $count;
            $this->orders = $orders;
        }
    }

    /**
     * Interface OrderItemBulkUpdateOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    interface OrderItemBulkUpdateOutputPortInterface
    {
        /**
         * @param OrderItemBulkUpdateOutputData $outputData
         */
        function output(OrderItemBulkUpdateOutputData $outputData);
    }
}
