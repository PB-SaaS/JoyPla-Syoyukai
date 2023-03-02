<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {
    use App\Model\Division;
    use JoyPla\Application\InputPorts\Api\Order\OrderShowInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\OrderShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderShowOutputPortInterface;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class OrderShowInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderShowInteractor implements OrderShowInputPortInterface
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
         * @param OrderShowInputData $inputData
         */
        public function handle(OrderShowInputData $inputData)
        {
            [
                $orders,
                $count,
            ] = $this->repositoryProvider
                ->getOrderRepository()
                ->search(
                    new HospitalId($inputData->user->hospitalId),
                    $inputData->search
                );
            $this->presenterProvider
                ->getOrderShowPresenter()
                ->output(new OrderShowOutputData($orders, $count));
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
     * Class OrderShowInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderShowInputData
    {
        public Auth $user;
        public stdClass $search;

        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->orderId = $search['orderId'];
            $this->search->itemName = $search['itemName'];
            $this->search->makerName = $search['makerName'];
            $this->search->itemCode = $search['itemCode'];
            $this->search->itemStandard = $search['itemStandard'];
            $this->search->itemJANCode = $search['itemJANCode'];
            $this->search->registerDate = $search['registerDate'];
            $this->search->orderDate = $search['orderDate'];
            $this->search->divisionIds = $search['divisionIds'];
            $this->search->orderStatus = $search['orderStatus'];
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    interface OrderShowInputPortInterface
    {
        /**
         * @param OrderShowInputData $inputData
         */
        function handle(OrderShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {
    use JoyPla\Enterprise\Models\Order;

    /**
     * Class OrderShowOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderShowOutputData
    {
        public int $count;
        public array $orders;

        public function __construct(array $orders, int $count)
        {
            $this->count = $count;
            $this->orders = array_map(function (Order $order) {
                return $order->toArray();
            }, $orders);
        }
    }

    /**
     * Interface OrderShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    interface OrderShowOutputPortInterface
    {
        /**
         * @param OrderShowOutputData $outputData
         */
        function output(OrderShowOutputData $outputData);
    }
}
