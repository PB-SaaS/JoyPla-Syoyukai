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
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\Lot;
    use JoyPla\Enterprise\Models\LotDate;
    use JoyPla\Enterprise\Models\LotNumber;
    use JoyPla\Enterprise\Models\OrderAdjustment;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\OrderItemId;
    use JoyPla\Enterprise\Models\OrderQuantity;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\InterfaceAdapters\GateWays\Repository\DivisionRepository;
    use JoyPla\InterfaceAdapters\GateWays\Repository\DivisionRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InventoryCalculationRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class OrderUnapprovedApprovalInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderUnapprovedApprovalInteractor implements
        OrderUnapprovedApprovalInputPortInterface
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
         * @param OrderUnapprovedApprovalInputData $inputData
         */
        public function handle(OrderUnapprovedApprovalInputData $inputData)
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

            $order = $order->approval();

            $this->repositoryProvider
                ->getOrderRepository()
                ->saveToArray($hospitalId, [$order]);

            $inventoryCalculations = [];

            if ($order->getReceivedTarget() === 1) {
                // 大倉庫
                $division = $this->repositoryProvider
                    ->getDivisionRepository()
                    ->getStorehouse($hospitalId);
                foreach ($order->getOrderItems() as $item) {
                    $inventoryCalculations[] = new InventoryCalculation(
                        $item->getHospitalId(),
                        $division->getDivisionId(),
                        $item->getInHospitalItemId(),
                        $item->getOrderQuantity()->value() *
                            $item->getQuantity()->getQuantityNum(),
                        2,
                        new Lot(new LotNumber(''), new LotDate('')),
                        0
                    );
                }
            }
            if ($order->getReceivedTarget() === 2) {
                // 部署
                foreach ($order->getOrderItems() as $item) {
                    $inventoryCalculations[] = new InventoryCalculation(
                        $item->getHospitalId(),
                        $item->getDivision()->getDivisionId(),
                        $item->getInHospitalItemId(),
                        $item->getOrderQuantity()->value() *
                            $item->getQuantity()->getQuantityNum(),
                        2,
                        new Lot(new LotNumber(''), new LotDate('')),
                        0
                    );
                }
            }

            $this->repositoryProvider
                ->getOrderRepository()
                ->sendApprovalOrderMail($order, $inputData->user);

            $this->repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);

            $this->presenterProvider
                ->getOrderUnapprovedApprovalPresenter()
                ->output(new OrderUnapprovedApprovalOutputData($order));
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
     * Class OrderUnapprovedApprovalInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderUnapprovedApprovalInputData
    {
        public Auth $user;
        public string $orderId;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            string $orderId,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->orderId = $orderId;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
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
        public array $data;
        public int $count;

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
