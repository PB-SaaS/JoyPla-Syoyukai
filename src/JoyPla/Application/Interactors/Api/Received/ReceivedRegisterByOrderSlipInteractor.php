<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Received {

    use App\Model\Division;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterByOrderSlipInputData;
    use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterByOrderSlipInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterByOrderSlipOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterByOrderSlipOutputPortInterface;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ReceivedRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\stockRepositoryInterface;

    /**
     * Class ReceivedRegisterByOrderSlipInteractor
     * @package JoyPla\Application\Interactors\Api\Received
     */
    class ReceivedRegisterByOrderSlipInteractor implements ReceivedRegisterByOrderSlipInputPortInterface
    {
        /** @var ReceivedRegisterByOrderSlipOutputPortInterface */
        private ReceivedRegisterByOrderSlipOutputPortInterface $outputPort;

        /** @var StockRepositoryInterface */
        private StockRepositoryInterface $stockRepository;

        /**
         * ReceivedRegisterByOrderSlipInteractor constructor.
         * @param ReceivedRegisterByOrderSlipOutputPortInterface $outputPort
         */
        public function __construct(ReceivedRegisterByOrderSlipOutputPortInterface $outputPort , ReceivedRepositoryInterface $receivedRepository)
        {
            $this->outputPort = $outputPort;
            $this->receivedRepository = $receivedRepository;
        }

        /**
         * @param ReceivedRegisterByOrderSlipInputData $inputData
         */
        public function handle(ReceivedRegisterByOrderSlipInputData $inputData)
        {
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Received {

    use Auth;
    use stdClass;

    /**
     * Class ReceivedRegisterByOrderSlipInputData
     * @package JoyPla\Application\InputPorts\Api\Received
     */
    class ReceivedRegisterByOrderSlipInputData
    {
        /**
         * ReceivedRegisterByOrderSlipInputData constructor.
         */
        public function __construct(Auth $auth , $orderId ,array $receivedItems )
        {
            $this->auth = $auth;
            $this->orderId = $orderId;
            $this->receivedItems = $receivedItems;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Received
    */
    interface ReceivedRegisterByOrderSlipInputPortInterface
    {
        /**
         * @param ReceivedRegisterByOrderSlipInputData $inputData
         */
        function handle(ReceivedRegisterByOrderSlipInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Received {

    use JoyPla\Enterprise\Models\Stock;

    /**
     * Class ReceivedRegisterByOrderSlipOutputData
     * @package JoyPla\Application\OutputPorts\Api\Received;
     */
    class ReceivedRegisterByOrderSlipOutputData
    {
        /** @var string */

        /**
         * ReceivedRegisterByOrderSlipOutputData constructor.
         */
        public function __construct(array $stocks , int $count)
        {
            $this->stocks = array_map(function(Stock $stock){
                return $stock->toArray();
            },$stocks);
            $this->count = $count;
        }
    }

    /**
     * Interface ReceivedRegisterByOrderSlipOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Received;
    */
    interface ReceivedRegisterByOrderSlipOutputPortInterface
    {
        /**
         * @param ReceivedRegisterByOrderSlipOutputData $outputData
         */
        function output(ReceivedRegisterByOrderSlipOutputData $outputData);
    }
} 