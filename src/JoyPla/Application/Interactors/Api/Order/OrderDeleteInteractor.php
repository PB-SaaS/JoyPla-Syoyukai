<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {
    use ApiResponse;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Order\OrderDeleteInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderDeleteInputPortInterface;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\Lot;
    use JoyPla\Enterprise\Models\LotDate;
    use JoyPla\Enterprise\Models\LotNumber;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class OrderDeleteInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderDeleteInteractor implements OrderDeleteInputPortInterface
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
         * @param OrderDeleteInputData $inputData
         */
        public function handle(OrderDeleteInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $orderId = new OrderId($inputData->orderId);

            $order = $this->repositoryProvider
                ->getOrderRepository()
                ->index($hospitalId, $orderId, [OrderStatus::OrderCompletion]);

            if ($order === null) {
                throw new NotFoundException('Not Found.', 404);
            }

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
                            $item->getQuantity()->getQuantityNum() *
                            -1,
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
                            $item->getQuantity()->getQuantityNum() *
                            -1,
                        2,
                        new Lot(new LotNumber(''), new LotDate('')),
                        0
                    );
                }
            }

            $this->repositoryProvider
                ->getInventoryCalculationRepository()
                ->saveToArray($inventoryCalculations);

            $deleteCount = $this->repositoryProvider
                ->getOrderRepository()
                ->delete($hospitalId, $orderId);
            /*
            $this->presenterProvider
                ->getOrderDeletePresenter()
                ->output(new OrderDeleteOutputData($deleteCount));
                */
            echo (new ApiResponse([], 1, 200, 'success', []))->toJson();
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
     * Class OrderDeleteInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderDeleteInputData
    {
        public Auth $user;
        public string $orderId;

        public function __construct(Auth $user, string $orderId)
        {
            $this->user = $user;
            $this->orderId = $orderId;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    interface OrderDeleteInputPortInterface
    {
        /**
         * @param OrderDeleteInputData $inputData
         */
        function handle(OrderDeleteInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {
    use JoyPla\Enterprise\Models\Order;

    /**
     * Class OrderDeleteOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderDeleteOutputData
    {
        public int $count;

        public function __construct(int $deleteCount)
        {
            $this->count = $deleteCount;
        }
    }

    /**
     * Interface OrderDeleteOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    interface OrderDeleteOutputPortInterface
    {
        /**
         * @param OrderDeleteOutputData $outputData
         */
        function output(OrderDeleteOutputData $outputData);
    }
}
