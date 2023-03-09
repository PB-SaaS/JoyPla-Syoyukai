<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Web\Received {
    use App\Model\Division;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Web\Received\OrderReceivedSlipIndexInputData;
    use JoyPla\Application\InputPorts\Web\Received\OrderReceivedSlipIndexInputPortInterface;
    use JoyPla\Application\OutputPorts\Web\Received\OrderReceivedSlipIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Received\OrderReceivedSlipIndexOutputPortInterface;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\InterfaceAdapters\GateWays\Repository\orderRepositoryInterface;
    use JoyPla\Service\Presenter\Web\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class OrderReceivedSlipIndexInteractor
     * @package JoyPla\Application\Interactors\Web\Received
     */
    class OrderReceivedSlipIndexInteractor implements
        OrderReceivedSlipIndexInputPortInterface
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
         * @param OrderReceivedSlipIndexInputData $inputData
         */
        public function handle(OrderReceivedSlipIndexInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $orderId = new OrderId($inputData->orderId);

            $order = $this->repositoryProvider
                ->getOrderRepository()
                ->index($hospitalId, $orderId, [
                    OrderStatus::OrderCompletion,
                    OrderStatus::OrderFinished,
                    OrderStatus::DeliveryDateReported,
                    OrderStatus::PartOfTheCollectionIsIn,
                    OrderStatus::ReceivingIsComplete,
                    OrderStatus::DeliveryIsCanceled,
                    OrderStatus::Borrowing,
                ]);

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

            $this->presenterProvider
                ->getOrderReceivedSlipIndexPresenter()
                ->output(new OrderReceivedSlipIndexOutputData($order));
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Web\Received {
    use Auth;
    use stdClass;

    /**
     * Class OrderReceivedSlipIndexInputData
     * @package JoyPla\Application\InputPorts\Web\Received
     */
    class OrderReceivedSlipIndexInputData
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
     * @package JoyPla\Application\InputPorts\Web\Received
     */
    interface OrderReceivedSlipIndexInputPortInterface
    {
        /**
         * @param OrderReceivedSlipIndexInputData $inputData
         */
        function handle(OrderReceivedSlipIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Web\Received {
    use JoyPla\Enterprise\Models\Order;

    /**
     * Class OrderReceivedSlipIndexOutputData
     * @package JoyPla\Application\OutputPorts\Web\Received;
     */
    class OrderReceivedSlipIndexOutputData
    {
        public array $order;

        public function __construct(array $order)
        {
            $this->order = $order;
        }
    }

    /**
     * Interface OrderReceivedSlipIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Web\Received;
     */
    interface OrderReceivedSlipIndexOutputPortInterface
    {
        /**
         * @param OrderReceivedSlipIndexOutputData $outputData
         */
        function output(OrderReceivedSlipIndexOutputData $outputData);
    }
}
