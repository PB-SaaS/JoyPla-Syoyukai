<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Order {

    use App\Model\Division;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\Order\FixedQuantityOrderInputData;
    use JoyPla\Application\InputPorts\Api\Order\FixedQuantityOrderInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Order\FixedQuantityOrderOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\FixedQuantityOrderOutputPortInterface;
    use JoyPla\Enterprise\Models\OrderId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\Order;
    use JoyPla\InterfaceAdapters\GateWays\Repository\stockRepositoryInterface;

    /**
     * Class FixedQuantityOrderInteractor
     * @package JoyPla\Application\Interactors\Api\Order
     */
    class FixedQuantityOrderInteractor implements FixedQuantityOrderInputPortInterface
    {
        /** @var FixedQuantityOrderOutputPortInterface */
        private FixedQuantityOrderOutputPortInterface $outputPort;

        /** @var StockRepositoryInterface */
        private StockRepositoryInterface $stockRepository;

        /**
         * FixedQuantityOrderInteractor constructor.
         * @param FixedQuantityOrderOutputPortInterface $outputPort
         */
        public function __construct(FixedQuantityOrderOutputPortInterface $outputPort , StockRepositoryInterface $stockRepository)
        {
            $this->outputPort = $outputPort;
            $this->stockRepository = $stockRepository;
        }

        /**
         * @param FixedQuantityOrderInputData $inputData
         */
        public function handle(FixedQuantityOrderInputData $inputData)
        {
            [$stocks , $count] = $this->stockRepository->search($inputData->auth , $inputData->search);
            $this->outputPort->output(new FixedQuantityOrderOutputData($stocks , $count));
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
     * Class FixedQuantityOrderInputData
     * @package JoyPla\Application\InputPorts\Api\Order
     */
    class FixedQuantityOrderInputData
    {
        /**
         * FixedQuantityOrderInputData constructor.
         */
        public function __construct(Auth $auth , array $search )
        {
            $this->auth = $auth;
            $this->search = new stdClass();
            $this->search->itemName = $search['itemName'];
            $this->search->makerName = $search['makerName'];
            $this->search->itemCode = $search['itemCode'];
            $this->search->itemStandard = $search['itemStandard'];
            $this->search->itemJANCode = $search['itemJANCode'];
            $this->search->yearMonth = $search['yearMonth'];
            $this->search->divisionIds = $search['divisionIds'];
            $this->search->perPage= $search['perPage'];
            $this->search->currentPage= $search['currentPage'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Order
    */
    interface FixedQuantityOrderInputPortInterface
    {
        /**
         * @param FixedQuantityOrderInputData $inputData
         */
        function handle(FixedQuantityOrderInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Order {

    use JoyPla\Enterprise\Models\Stock;

    /**
     * Class FixedQuantityOrderOutputData
     * @package JoyPla\Application\OutputPorts\Api\Order;
     */
    class FixedQuantityOrderOutputData
    {
        /** @var string */

        /**
         * FixedQuantityOrderOutputData constructor.
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
     * Interface FixedQuantityOrderOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Order;
    */
    interface FixedQuantityOrderOutputPortInterface
    {
        /**
         * @param FixedQuantityOrderOutputData $outputData
         */
        function output(FixedQuantityOrderOutputData $outputData);
    }
} 