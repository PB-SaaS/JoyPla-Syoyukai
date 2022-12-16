<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\Reference {
    use JoyPla\Application\InputPorts\Api\Reference\ConsumptionHistoryShowInputData;
    use JoyPla\Application\InputPorts\Api\Reference\ConsumptionHistoryShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Reference\ConsumptionHistoryShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Reference\ConsumptionHistoryShowOutputPortInterface;
    use JoyPla\Enterprise\Models\Consumption;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ConsumptionHistoryRepositoryInterface;

    /**
     * Class ReferenceHistoryShowInteractor
     * @package JoyPla\Application\Interactors\Reference\Api
     */
    class ConsumptionHistoryShowInteractor implements ConsumptionHistoryShowInputPortInterface
    {
        /** @var ConsumptionHistoryShowOutputPortInterface */
        private ConsumptionHistoryShowOutputPortInterface $outputPort;

        /** @var ConsumptionHistoryRepositoryInterface */
        private ConsumptionHistoryRepositoryInterface $repository;

        /**
         * ConsumptionHistoryShowInteractor constructor.
         * @param ConsumptionHistoryShowOutputPortInterface $outputPort
         */
        public function __construct(ConsumptionHistoryShowOutputPortInterface $outputPort, ConsumptionHistoryRepositoryInterface $repository)
        {
            $this->outputPort = $outputPort;
            $this->repository = $repository;
        }

        /**
         * @param ConsumptionHistoryShowInputData $inputData
         */
        public function handle(ConsumptionHistoryShowInputData $inputData)
        {
            [ $histories , $count ] = $this->repository->search(
                (new HospitalId($inputData->hospitalId)),
                $inputData->search
            );
            $this->outputPort->output(new ConsumptionHistoryShowOutputData($histories, $count));
        }
    }
}


/***
 * INPUT
 */

namespace JoyPla\Application\InputPorts\Api\Reference {
    use stdClass;

    /**
     * Class ConsumptionHistoryShowInputData
     * @package JoyPla\Application\InputPorts\Reference\Api
     */
    class ConsumptionHistoryShowInputData
    {
        /**
         * ConsumptionHistoryShowInputData constructor.
         */
        public function __construct(string $hospitalId, array $search)
        {
            $this->hospitalId = $hospitalId;
            $this->search = new stdClass();
            $this->search->divisionIds = $search['divisionIds'];
            $this->search->perPage= $search['perPage'];
            $this->search->currentPage= $search['currentPage'];
        }
    }

    /**
     * Interface ConsumptionHistoryShowInputPortInterface
     * @package JoyPla\Application\InputPorts\Reference\Api
    */
    interface ConsumptionHistoryShowInputPortInterface
    {
        /**
         * @param ConsumptionHistoryShowInputData $inputData
         */
        public function handle(ConsumptionHistoryShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\Reference {
    use JoyPla\Enterprise\Models\Consumption;

    /**
     * Class ConsumptionHistoryShowOutputData
     * @package JoyPla\Application\OutputPorts\Reference\Api;
     */
    class ConsumptionHistoryShowOutputData
    {
        /** @var string */

        /**
         * ConsumptionHistoryShowOutputData constructor.
         */

        public function __construct(array $histories, int $count)
        {
            $this->count = $count;
            $this->histories = array_map(
                function (Consumption $histories) {
                    return $histories->toArray();
                },
                $histories
            );
        }
    }

    /**
     * Interface ConsumptionHistoryShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Reference\Api;
    */
    interface ConsumptionHistoryShowOutputPortInterface
    {
        /**
         * @param ConsumptionHistoryShowOutputData $outputData
         */
        public function output(ConsumptionHistoryShowOutputData $outputData);
    }
}
