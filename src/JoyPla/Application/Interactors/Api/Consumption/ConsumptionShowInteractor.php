<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Consumption {

    use App\Model\Division;
    use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionShowInputData;
    use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionShowOutputPortInterface;
    use JoyPla\Enterprise\Models\Consumption;
    use JoyPla\Enterprise\Models\ConsumptionDate;
    use JoyPla\Enterprise\Models\ConsumptionId;
    use JoyPla\Enterprise\Models\ConsumptionStatus;
    use JoyPla\Enterprise\Models\DivisionId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\consumptionRepositoryInterface;

    /**
     * Class ConsumptionShowInteractor
     * @package JoyPla\Application\Interactors\Consumption\Api
     */
    class ConsumptionShowInteractor implements ConsumptionShowInputPortInterface
    {
        /** @var ConsumptionShowOutputPortInterface */
        private ConsumptionShowOutputPortInterface $outputPort;

        /** @var ConsumptionRepositoryInterface */
        private ConsumptionRepositoryInterface $consumptionRepository;

        /**
         * ConsumptionShowInteractor constructor.
         * @param ConsumptionShowOutputPortInterface $outputPort
         */
        public function __construct(ConsumptionShowOutputPortInterface $outputPort , ConsumptionRepositoryInterface $consumptionRepository)
        {
            $this->outputPort = $outputPort;
            $this->consumptionRepository = $consumptionRepository;
        }

        /**
         * @param ConsumptionShowInputData $inputData
         */
        public function handle(ConsumptionShowInputData $inputData)
        {
            [ $consumptions , $count ] = $this->consumptionRepository->search(
                (new HospitalId($inputData->hospitalId)),
                $inputData->search
            );
            $this->outputPort->output(new ConsumptionShowOutputData($consumptions , $count));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Consumption {

    use stdClass;

    /**
     * Class ConsumptionShowInputData
     * @package JoyPla\Application\InputPorts\Consumption\Api
     */
    class ConsumptionShowInputData
    {
        /**
         * ConsumptionShowInputData constructor.
         */
        public function __construct(string $hospitalId , array $search)
        {
            $this->hospitalId = $hospitalId;
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
     * @package JoyPla\Application\InputPorts\Consumption\Api
    */
    interface ConsumptionShowInputPortInterface
    {
        /**
         * @param ConsumptionShowInputData $inputData
         */
        function handle(ConsumptionShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Consumption {

    use JoyPla\Enterprise\Models\Consumption;

    /**
     * Class ConsumptionShowOutputData
     * @package JoyPla\Application\OutputPorts\Consumption\Api;
     */
    class ConsumptionShowOutputData
    {
        /** @var string */

        /**
         * ConsumptionShowOutputData constructor.
         */
        
        public function __construct(array $consumptions , int $count)
        {
            $this->count = $count;
            $this->consumptions = array_map(
                function( Consumption $consumption)
                {
                    return $consumption->toArray();
                },$consumptions
            );
        }
    }

    /**
     * Interface ConsumptionShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Consumption\Api;
    */
    interface ConsumptionShowOutputPortInterface
    {
        /**
         * @param ConsumptionShowOutputData $outputData
         */
        function output(ConsumptionShowOutputData $outputData);
    }
} 