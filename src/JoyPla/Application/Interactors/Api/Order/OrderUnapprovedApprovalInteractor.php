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

    /**
     * Class OrderUnapprovedApprovalInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderUnapprovedApprovalInteractor implements OrderUnapprovedApprovalInputPortInterface
    {
        /** @var OrderUnapprovedApprovalOutputPortInterface */
        private OrderUnapprovedApprovalOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;
        
        /** @var DivisionRepositoryInterface */
        private DivisionRepositoryInterface $divisionRepository;
        
        /** @var InventoryCalculationRepositoryInterface */
        private InventoryCalculationRepositoryInterface $inventoryCalculationRepository;

        /**
         * OrderUnapprovedApprovalInteractor constructor.
         * @param OrderUnapprovedApprovalOutputPortInterface $outputPort
         */
        public function __construct(
            OrderUnapprovedApprovalOutputPortInterface $outputPort , 
            OrderRepositoryInterface $orderRepository , 
            DivisionRepositoryInterface $divisionRepository,
            InventoryCalculationRepositoryInterface $inventoryCalculationRepository)
        {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
            $this->divisionRepository = $divisionRepository;
            $this->inventoryCalculationRepository = $inventoryCalculationRepository;
        }

        /**
         * @param OrderUnapprovedApprovalInputData $inputData
         */
        public function handle(OrderUnapprovedApprovalInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $orderId = new OrderId($inputData->orderId);
            $order = $this->orderRepository->index(
                $hospitalId,
                $orderId,
                [
                    OrderStatus::UnOrdered,
                ]
            );

            if( $order === null ){
                throw new NotFoundException("Not Found.",404);
            }

            if($inputData->isOnlyMyDivision && ! $order->getDivision()->getDivisionId()->equal($inputData->user->divisionId))
            {
                throw new NotFoundException("Not Found.",404);
            }

            $order = $order->approval();
            
            $this->orderRepository->saveToArray($hospitalId , [$order]);
            
            $inventoryCalculations = [];


            if ($order->getReceivedTarget() === 1) { // 大倉庫
                $division = $this->divisionRepository->getStorehouse($hospitalId);
                foreach($order->getOrderItems() as $item)
                {
                    $inventoryCalculations[] = new InventoryCalculation(
                        $item->getHospitalId(),
                        $division->getDivisionId(),
                        $item->getInHospitalItemId(),
                        $item->getOrderQuantity()->value() * $item->getQuantity()->getQuantityNum(),
                        2,
                        (new Lot(  new LotNumber('') ,new LotDate('')  )),
                        0
                    );
                }
            }
            if ($order->getReceivedTarget() === 2) { // 部署
                foreach($order->getOrderItems() as $item)
                {
                    $inventoryCalculations[] = new InventoryCalculation(
                        $item->getHospitalId(),
                        $item->getDivision()->getDivisionId(),
                        $item->getInHospitalItemId(),
                        $item->getOrderQuantity()->value() * $item->getQuantity()->getQuantityNum(),
                        2,
                        (new Lot(  new LotNumber('') ,new LotDate('')  )),
                        0
                    );
                }
            }

            $this->orderRepository->sendApprovalOrderMail($order , $inputData->user);

            $this->inventoryCalculationRepository->saveToArray($inventoryCalculations);
            
            $this->outputPort->output(new OrderUnapprovedApprovalOutputData($order));
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
        /**
         * OrderUnapprovedApprovalInputData constructor.
         */
        public function __construct(Auth $user , string $orderId , bool $isOnlyMyDivision)
        {
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
        /** @var string */

        /**
         * OrderUnapprovedApprovalOutputData constructor.
         */
        
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