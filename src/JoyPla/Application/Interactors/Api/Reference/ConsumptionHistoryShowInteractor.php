<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\Reference {
    use JoyPla\Application\InputPorts\Api\Reference\ConsumptionHistoryShowInputData;
    use JoyPla\Application\InputPorts\Api\Reference\ConsumptionHistoryShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Reference\ConsumptionHistoryShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Reference\ConsumptionHistoryShowOutputPortInterface;
    use JoyPla\Enterprise\Models\ConsumptionForReference;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ConsumptionHistoryRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ReferenceHistoryShowInteractor
     * @package JoyPla\Application\Interactors\Reference\Api
     */
    class ConsumptionHistoryShowInteractor implements
        ConsumptionHistoryShowInputPortInterface
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
         * @param ConsumptionHistoryShowInputData $inputData
         */
        public function handle(ConsumptionHistoryShowInputData $inputData)
        {
            [
                $histories,
                $count,
            ] = $this->repositoryProvider
                ->getConsumptionHistoryRepository()
                ->search(
                    new HospitalId($inputData->hospitalId),
                    $inputData->search
                );

            $this->presenterProvider
                ->getConsumptionHistoryShowPresenter()
                ->output(
                    new ConsumptionHistoryShowOutputData($histories, $count)
                );
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
        public string $hospitalId;
        public stdClass $search;

        public function __construct(string $hospitalId, array $search)
        {
            $this->hospitalId = $hospitalId;
            $this->search = new stdClass();
            $this->search->divisionIds = $search['divisionIds'];
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
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
    use JoyPla\Enterprise\Models\ConsumptionForReference;

    /**
     * Class ConsumptionHistoryShowOutputData
     * @package JoyPla\Application\OutputPorts\Reference\Api;
     */
    class ConsumptionHistoryShowOutputData
    {
        public int $count;
        public array $histories;

        public function __construct(array $histories, int $count)
        {
            $this->count = $count;
            $this->histories = array_map(function (
                ConsumptionForReference $history
            ) {
                return $history->toArray();
            },
            $histories);
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
