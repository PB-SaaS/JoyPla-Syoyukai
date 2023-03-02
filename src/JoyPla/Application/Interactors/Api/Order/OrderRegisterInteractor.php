<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {
    use App\Model\Division;
    use Exception;
    use JoyPla\Application\InputPorts\Api\Order\OrderRegisterInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderRegisterInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\OrderRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\DateYearMonthDay;
    use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\OrderDate;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\Enterprise\Models\DivisionId;
    use JoyPla\Enterprise\Models\Hospital;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\HospitalName;
    use JoyPla\Enterprise\Models\OrderAdjustment;
    use JoyPla\Enterprise\Models\TextArea512Bytes;
    use JoyPla\InterfaceAdapters\GateWays\Repository\HospitalRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class OrderRegisterInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderRegisterInteractor implements OrderRegisterInputPortInterface
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
         * @param OrderRegisterInputData $inputData
         */
        public function handle(OrderRegisterInputData $inputData, $adjustment)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $hospital = $this->repositoryProvider
                ->getHospitalRepository()
                ->find($hospitalId);

            $inputData->orderItems = array_map(function ($v) use ($inputData) {
                if (
                    $inputData->isOnlyMyDivision &&
                    $inputData->user->divisionId !== $v->divisionId
                ) {
                    throw new Exception('Illegal request', 403);
                }
                return $v;
            }, $inputData->orderItems);

            $orderItems = $this->repositoryProvider
                ->getOrderRepository()
                ->findByInHospitalItem($hospitalId, $inputData->orderItems);
            $historyOrders = [];
            if ($inputData->integrate) {
                $historyOrders = $this->repositoryProvider
                    ->getOrderRepository()
                    ->getUnapprovedOrder($hospitalId, $orderItems);
            }
            $ids = [];
            $orders = [];
            foreach ($orderItems as $i) {
                $exist = false;
                if ($inputData->integrate) {
                    foreach ($historyOrders as $key => $h) {
                        if (
                            $h->equalOrderSlip(
                                $i->getDivision(),
                                $i->getDistributor()
                            ) &&
                            ($h->isPlus() === $i->isPlus() ||
                                $h->isMinus() === $i->isMinus())
                        ) {
                            $exist = true;
                            $orders[] = $h->addOrderItem($i);
                            break;
                        }
                    }
                }
                if (!$exist) {
                    foreach ($orders as $key => $r) {
                        if (
                            $r->equalOrderSlip(
                                $i->getDivision(),
                                $i->getDistributor()
                            ) &&
                            ($r->isPlus() === $i->isPlus() ||
                                $r->isMinus() === $i->isMinus())
                        ) {
                            $exist = true;
                            $orders[$key] = $r->addOrderItem($i);
                            break;
                        }
                    }
                }
                if ($exist) {
                    continue;
                }
                $id = OrderId::generate();
                //登録時には病院名は必要ないので、いったんhogeでいい
                $orders[] = new Order(
                    $id,
                    new DateYearMonthDayHourMinutesSecond(''),
                    new DateYearMonthDayHourMinutesSecond(''),
                    [$i],
                    $hospital,
                    $i->getDivision(),
                    $i->getDistributor(),
                    new OrderStatus(OrderStatus::UnOrdered),
                    new OrderAdjustment($adjustment),
                    new TextArea512Bytes(''),
                    new TextArea512Bytes(''),
                    $inputData->user->name,
                    1
                );
            }

            $this->repositoryProvider
                ->getOrderRepository()
                ->saveToArray($hospitalId, $orders);

            $this->repositoryProvider
                ->getOrderRepository()
                ->sendUnapprovedOrderMail($orders, $inputData->user);

            $ids = [];

            foreach ($orders as $order) {
                $ids[] = $order->getOrderId()->value();
            }

            $this->presenterProvider
                ->getOrderRegisterPresenter()
                ->output(new OrderRegisterOutputData($ids));
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
     * Class OrderRegisterInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderRegisterInputData
    {
        public Auth $user;
        public array $orderItems;
        public bool $integrate;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            array $orderItems,
            bool $integrate,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;

            $this->orderItems = array_map(function ($v) {
                $object = new stdClass();
                $object->inHospitalItemId = $v['inHospitalItemId'];
                $object->orderUnitQuantity = $v['orderUnitQuantity'];
                $object->divisionId = $v['divisionId'];
                return $object;
            }, $orderItems);

            $this->integrate = $integrate;

            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    interface OrderRegisterInputPortInterface
    {
        /**
         * @param OrderRegisterInputData $inputData
         */
        function handle(OrderRegisterInputData $inputData, $adjustment);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {
    /**
     * Class OrderRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderRegisterOutputData
    {
        public array $ids;
        /**
         * OrderRegisterOutputData constructor.
         */
        public function __construct(array $ids)
        {
            $this->ids = $ids;
        }
    }

    /**
     * Interface OrderRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    interface OrderRegisterOutputPortInterface
    {
        /**
         * @param OrderRegisterOutputData $outputData
         */
        function output(OrderRegisterOutputData $outputData);
    }
}
