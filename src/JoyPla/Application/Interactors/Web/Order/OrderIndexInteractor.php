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
    use JoyPla\Service\Presenter\Web\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class OrderIndexInteractor
     * @package JoyPla\Application\Interactors\Web\Order
     */
    abstract class OrderIndexInteractor implements OrderIndexInputPortInterface
    {
        private RepositoryProvider $repositoryProvider;
        private OrderIndexOutputPortInterface $presenter;

        public function __construct(
            OrderIndexOutputPortInterface $presenter,
            RepositoryProvider $repositoryProvider
        ) {
            $this->presenter = $presenter;
            $this->repositoryProvider = $repositoryProvider;
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
            $order = $this->repositoryProvider
                ->getOrderRepository()
                ->index($hospitalId, $orderId, $orderstatus);

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

            $inHospitalItemIds = [];
            foreach ($order->getOrderItems() as $orderItem) {
                $inHospitalItemIds[] = $orderItem->getInHospitalItemId();
            }

            $stocks = $this->repositoryProvider
                ->getStockRepository()
                ->getStockByDivisionIdAndInHospitalItemIds(
                    $hospitalId,
                    [$order->getDivision()->getDivisionId()],
                    $inHospitalItemIds
                );

            $order = $order->toArray();

            foreach ($order['orderItems'] as &$orderItem) {
                $inHospitalItemId = $orderItem['inHospitalItemId'];
                $stock = array_find($stocks, function ($stock) use (
                    $inHospitalItemId
                ) {
                    return $stock->getInHospitalItemId()->value() ===
                        $inHospitalItemId;
                });

                $orderItem['stockCount'] = $stock
                    ? $stock->getInventoryQuantity()
                    : 0;
            }

            if ($order['receivedTarget'] == '2') {
                $order['receivedDivisionName'] =
                    $order['division']['divisionName'];
                $order['receivedDivisionId'] = $order['division']['divisionId'];
            }
            if ($order['receivedTarget'] == '1') {
                $receivedDivision = $this->repositoryProvider
                    ->getDivisionRepository()
                    ->getStorehouse($hospitalId);
                $order[
                    'receivedDivisionName'
                ] = $receivedDivision->getDivisionName()->value();
                $order[
                    'receivedDivisionId'
                ] = $receivedDivision->getDivisionId()->value();
            }

            $this->presenter->output(new OrderIndexOutputData($order));
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
        public Auth $user;
        public string $orderId;
        public bool $isUnapproved;
        public bool $isOnlyMyDivision;

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
    /**
     * Class OrderIndexOutputData
     * @package JoyPla\Application\OutputPorts\Web\Order;
     */
    class OrderIndexOutputData
    {
        public array $order;

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
