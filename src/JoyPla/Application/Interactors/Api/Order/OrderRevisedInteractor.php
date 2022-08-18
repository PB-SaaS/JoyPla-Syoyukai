<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {

    use App\Model\Division;
    use Exception;
    use JoyPla\Application\InputPorts\Api\Order\OrderRevisedInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderRevisedInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\OrderRevisedOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderRevisedOutputPortInterface;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\InventoryCalculation;
    use JoyPla\Enterprise\Models\Lot;
    use JoyPla\Enterprise\Models\LotDate;
    use JoyPla\Enterprise\Models\LotNumber;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\OrderQuantity;
    use JoyPla\Enterprise\Models\OrderStatus;
    use JoyPla\InterfaceAdapters\GateWays\Repository\InventoryCalculationRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;

    /**
     * Class OrderRevisedInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderRevisedInteractor implements OrderRevisedInputPortInterface
    {
        /** @var OrderRevisedOutputPortInterface */
        private OrderRevisedOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;

        /**
         * OrderRevisedInteractor constructor.
         * @param OrderRevisedOutputPortInterface $outputPort
         */
        public function __construct(OrderRevisedOutputPortInterface $outputPort , OrderRepositoryInterface $orderRepository , InventoryCalculationRepositoryInterface $inventoryCalculationRepository)
        {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
            $this->inventoryCalculationRepository = $inventoryCalculationRepository;
        }

        /**
         * @param OrderRevisedInputData $inputData
         */
        public function handle(OrderRevisedInputData $inputData)
        {
            $order = $this->orderRepository->index(
                (new HospitalId($inputData->user->hospitalId)),
                (new OrderId($inputData->orderId)),
                [
                    OrderStatus::OrderCompletion,
                    OrderStatus::OrderFinished,
                    OrderStatus::DeliveryDateReported,
                    OrderStatus::PartOfTheCollectionIsIn,
                    OrderStatus::ReceivingIsComplete,
                    OrderStatus::DeliveryIsCanceled,
                    OrderStatus::Borrowing,
                ]
            );

            $orderItems = $order->getOrderItems();
            foreach($orderItems as $fkey => $item)
            {
                foreach($inputData->revisedItems as $revisedItem)
                {
                    if( $item->getOrderItemId()->equal($revisedItem->orderItemId))
                    {
                        $nowOrderQuantity = $item->getOrderQuantity();
                        $orderQuantity = new OrderQuantity($revisedItem->revisedOrderQuantity);
                        
                        $isPlusRevised = (  $item->isPlus() && ($item->getOrderQuantity()->value() >= $orderQuantity->value()) && ($item->getReceivedQuantity()->value() <= $orderQuantity->value()) );
                        $isMinusRevised = (  $item->isMinus() && ($item->getOrderQuantity()->value() <= $orderQuantity->value()) && ($item->getReceivedQuantity()->value() >= $orderQuantity->value()) );
                        if( ! ( $isPlusRevised || $isMinusRevised ) )
                        {
                            throw new Exception('The number of orders exceeded the number of corrections possible, and therefore, could not be registered.',422);
                        }

                        $orderItems[$fkey] = $item->setOrderQuantity($orderQuantity);
                        
                        $inventoryCalculations[] = new InventoryCalculation(
                            $item->getHospitalId(),
                            $item->getDivision()->getDivisionId(),
                            $item->getInHospitalItemId(),
                            ( $item->getOrderQuantity()->value() - $orderQuantity->value() ) * $item->getQuantity()->getQuantityNum() * -1,
                            2,
                            (new Lot(
                                new LotNumber(''),
                                new LotDate('')
                            )),
                            0,
                        );
                    }
                }
            }

            $order = $order->setOrderItems($orderItems);
            $order = $order->updateOrderStatus();
            $this->orderRepository->saveToArray(
                (new HospitalId($inputData->user->hospitalId)),
                [$order]);
            
            $this->inventoryCalculationRepository->saveToArray($inventoryCalculations);

            $this->outputPort->output(new OrderRevisedOutputData());
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
     * Class OrderRevisedInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderRevisedInputData
    {
        /**
         * OrderRevisedInputData constructor.
         */
        public function __construct(Auth $user , string $orderId , array $revisedItems)
        {
            $this->user = $user;
            $this->orderId = $orderId;
            $this->revisedItems = array_map(function($x){
                $test = new stdClass();
                $test->orderItemId = $x['orderItemId'];
                $test->revisedOrderQuantity = $x['revisedOrderQuantity'];
                return $test;
            },$revisedItems);
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
    */
    interface OrderRevisedInputPortInterface
    {
        /**
         * @param OrderRevisedInputData $inputData
         */
        function handle(OrderRevisedInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {

    use JoyPla\Enterprise\Models\Order;

    /**
     * Class OrderRevisedOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderRevisedOutputData
    {
        /** @var string */

        /**
         * OrderRevisedOutputData constructor.
         */
        
        public function __construct()
        {
        }
    }

    /**
     * Interface OrderRevisedOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
    */
    interface OrderRevisedOutputPortInterface
    {
        /**
         * @param OrderRevisedOutputData $outputData
         */
        function output(OrderRevisedOutputData $outputData);
    }
} 