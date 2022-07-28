<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {

    use ApiErrorCode\AccessFrequencyLimitExceededScopeIs;
    use App\Model\Division;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedDeleteInputData;
    use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedDeleteInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedDeleteOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedDeleteOutputPortInterface;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\OrderAdjustment;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\OrderItemId;
    use JoyPla\Enterprise\Models\OrderQuantity;
    use JoyPla\InterfaceAdapters\GateWays\Repository\OrderRepositoryInterface;

    /**
     * Class OrderUnapprovedDeleteInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class OrderUnapprovedDeleteInteractor implements OrderUnapprovedDeleteInputPortInterface
    {
        /** @var OrderUnapprovedDeleteOutputPortInterface */
        private OrderUnapprovedDeleteOutputPortInterface $outputPort;

        /** @var OrderRepositoryInterface */
        private OrderRepositoryInterface $orderRepository;

        /**
         * OrderUnapprovedDeleteInteractor constructor.
         * @param OrderUnapprovedDeleteOutputPortInterface $outputPort
         */
        public function __construct(OrderUnapprovedDeleteOutputPortInterface $outputPort , OrderRepositoryInterface $orderRepository)
        {
            $this->outputPort = $outputPort;
            $this->orderRepository = $orderRepository;
        }

        /**
         * @param OrderUnapprovedDeleteInputData $inputData
         */
        public function handle(OrderUnapprovedDeleteInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->hospitalId);
            $orderId = new OrderId($inputData->orderId);
            
            $deleteCount = $this->orderRepository->delete($hospitalId , $orderId);
            
            $this->outputPort->output(new OrderUnapprovedDeleteOutputData($deleteCount));
        }
    }
}



/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Order {

    use stdClass;

    /**
     * Class OrderUnapprovedDeleteInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class OrderUnapprovedDeleteInputData
    {
        /**
         * OrderUnapprovedDeleteInputData constructor.
         */
        public function __construct(string $hospitalId , string $orderId )
        {
            $this->hospitalId = $hospitalId;
            $this->orderId = $orderId;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
    */
    interface OrderUnapprovedDeleteInputPortInterface
    {
        /**
         * @param OrderUnapprovedDeleteInputData $inputData
         */
        function handle(OrderUnapprovedDeleteInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {

    use JoyPla\Enterprise\Models\Order;

    /**
     * Class OrderUnapprovedDeleteOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class OrderUnapprovedDeleteOutputData
    {
        /** @var string */

        /**
         * OrderUnapprovedDeleteOutputData constructor.
         */
        
        public function __construct(int $deleteCount)
        {
            $this->count = $deleteCount;
        }
    }

    /**
     * Interface OrderUnapprovedDeleteOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
    */
    interface OrderUnapprovedDeleteOutputPortInterface
    {
        /**
         * @param OrderUnapprovedDeleteOutputData $outputData
         */
        function output(OrderUnapprovedDeleteOutputData $outputData);
    }
} 