<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {
    use ApiErrorCode\AccessFrequencyLimitExceededScopeIs;
    use App\Model\Division;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedUpdateInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedUpdateInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedUpdateOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedUpdateOutputPortInterface;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\OrderAdjustment;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\OrderItemId;
    use JoyPla\Enterprise\Models\OrderQuantity;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\Enterprise\Models\TextArea512Bytes;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class OrderUnapprovedUpdateInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderUnapprovedUpdateInteractor implements
        OrderUnapprovedUpdateInputPortInterface
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
         * @param OrderUnapprovedUpdateInputData $inputData
         */
        public function handle(OrderUnapprovedUpdateInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $orderId = new OrderId($inputData->orderId);
            $order = $this->repositoryProvider
                ->getOrderRepository()
                ->index($hospitalId, $orderId, [OrderStatus::UnOrdered]);

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

            $orderItems = $order->getOrderItems();
            foreach ($orderItems as $key => $item) {
                $fkey = array_search(
                    $item->getOrderItemId()->value(),
                    array_column($inputData->updateModel, 'orderItemId')
                );
                if ($fkey === null) {
                    continue;
                }
                $orderQuantity =
                    $inputData->updateModel[$fkey]['orderQuantity'];
                $orderItems[$key] = $item->setOrderQuantity(
                    new OrderQuantity($orderQuantity)
                );
            }

            $order = $order->setOrderItems($orderItems);
            $order = $order->setOrderComment(
                new TextArea512Bytes($inputData->comment)
            );
            $order = $order->setAdjustment(
                new OrderAdjustment($inputData->adjustment)
            );

            $this->repositoryProvider
                ->getOrderRepository()
                ->saveToArray($hospitalId, [$order]);

            $this->presenterProvider
                ->getOrderUnapprovedUpdatePresenter()
                ->output(new OrderUnapprovedUpdateOutputData($order));
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
     * Class OrderUnapprovedUpdateInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderUnapprovedUpdateInputData
    {
        public Auth $user;
        public string $orderId;
        public string $adjustment;
        public string $comment;
        public array $updateModel;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            array $order,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->orderId = $order['orderId'];
            $this->adjustment = $order['adjustment'];
            $this->comment = $order['comment'];
            $this->updateModel = array_map(function (array $model) {
                return [
                    'orderItemId' => $model['orderItemId'],
                    'orderQuantity' => $model['orderQuantity'],
                ];
            }, $order['updateModel']);

            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    interface OrderUnapprovedUpdateInputPortInterface
    {
        /**
         * @param OrderUnapprovedUpdateInputData $inputData
         */
        function handle(OrderUnapprovedUpdateInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {
    use JoyPla\Enterprise\Models\Order;

    /**
     * Class OrderUnapprovedUpdateOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderUnapprovedUpdateOutputData
    {
        public array $data;
        public int $count;

        public function __construct(Order $order)
        {
            $this->data = $order->toArray();
            $this->count = count($order->toArray());
        }
    }

    /**
     * Interface OrderUnapprovedUpdateOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    interface OrderUnapprovedUpdateOutputPortInterface
    {
        /**
         * @param OrderUnapprovedUpdateOutputData $outputData
         */
        function output(OrderUnapprovedUpdateOutputData $outputData);
    }
}
