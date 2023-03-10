<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedApprovalAllInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedApprovalAllInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedApprovalAllOutputData;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\Lot;
    use JoyPla\Enterprise\Models\LotDate;
    use JoyPla\Enterprise\Models\LotNumber;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class OrderUnapprovedApprovalAllInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderUnapprovedApprovalAllInteractor implements
        OrderUnapprovedApprovalAllInputPortInterface
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
         * @param OrderUnapprovedApprovalAllInputData $inputData
         */
        public function handle(OrderUnapprovedApprovalAllInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);

            $orders = $this->repositoryProvider
                ->getOrderRepository()
                ->getOrder($hospitalId, [OrderStatus::UnOrdered]);

            if (empty($orders)) {
                throw new NotFoundException('Not Found.', 404);
            }

            foreach ($orders as $order) {
                if (
                    $inputData->isOnlyMyDivision &&
                    !$order
                        ->getDivision()
                        ->getDivisionId()
                        ->equal($inputData->user->divisionId)
                ) {
                    throw new NotFoundException('Not Found.', 404);
                }
            }
            $tmp = [];
            foreach ($orders as $order) {
                $tmp[] = $order->approval();
            }

            $orders = $tmp;

            $inventoryCalculations = [];

            foreach ($orders as $order) {
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
            }

            $this->repositoryProvider
                ->getOrderRepository()
                ->saveToArray($hospitalId, $orders);

            $this->repositoryProvider
                ->getOrderRepository()
                ->sendApprovalAllOrderMail($orders, $inputData->user);

            $this->repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);

            $this->presenterProvider
                ->getOrderUnapprovedApprovalAllPresenter()
                ->output(new OrderUnapprovedApprovalAllOutputData($orders));
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
     * Class OrderUnapprovedApprovalAllInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderUnapprovedApprovalAllInputData
    {
        public Auth $user;
        public bool $isOnlyMyDivision;

        public function __construct(Auth $user, bool $isOnlyMyDivision)
        {
            $this->user = $user;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    interface OrderUnapprovedApprovalAllInputPortInterface
    {
        /**
         * @param OrderUnapprovedApprovalAllInputData $inputData
         */
        function handle(OrderUnapprovedApprovalAllInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {
    use JoyPla\Enterprise\Models\Order;

    /**
     * Class OrderUnapprovedApprovalAllOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderUnapprovedApprovalAllOutputData
    {
        public array $data;
        public int $count;

        public function __construct(array $orders)
        {
            $this->data = array_map(function (Order $order) {
                return $order->toArray()['orderId'];
            }, $orders);
            $this->count = count($orders);
        }
    }

    /**
     * Interface OrderUnapprovedApprovalAllOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    interface OrderUnapprovedApprovalAllOutputPortInterface
    {
        /**
         * @param OrderUnapprovedApprovalAllOutputData $outputData
         */
        function output(OrderUnapprovedApprovalAllOutputData $outputData);
    }
}
