<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Consumption {
    use App\Model\Division;
    use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionIndexInputData;
    use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionIndexInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionIndexOutputData;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionIndexOutputPortInterface;
    use JoyPla\Enterprise\Models\Consumption;
    use JoyPla\Enterprise\Models\ConsumptionDate;
    use JoyPla\Enterprise\Models\ConsumptionId;
    use JoyPla\Enterprise\Models\ConsumptionStatus;
    use JoyPla\Enterprise\Models\DivisionId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\consumptionRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ConsumptionIndexInteractor
     * @package JoyPla\Application\Interactors\Consumption\Api
     */
    class ConsumptionIndexInteractor implements
        ConsumptionIndexInputPortInterface
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
         * @param ConsumptionIndexInputData $inputData
         */
        public function handle(ConsumptionIndexInputData $inputData)
        {
            [
                $consumptions,
                $count,
            ] = $this->repositoryProvider
                ->getConsumptionRepository()
                ->search(
                    new HospitalId($inputData->hospitalId),
                    $inputData->search
                );
            $this->presenterProvider
                ->getConsumptionIndexPresenter()
                ->output(new ConsumptionIndexOutputData($consumptions, $count));
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Consumption {
    use stdClass;

    /**
     * Class ConsumptionIndexInputData
     * @package JoyPla\Application\InputPorts\Consumption\Api
     */
    class ConsumptionIndexInputData
    {
        public string $hospitalId;
        public stdClass $search;

        public function __construct(string $hospitalId, array $search)
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
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
            $this->search->billingStatus = $search['billingStatus'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Consumption\Api
     */
    interface ConsumptionIndexInputPortInterface
    {
        /**
         * @param ConsumptionIndexInputData $inputData
         */
        function handle(ConsumptionIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Consumption {
    use JoyPla\Enterprise\Models\Consumption;

    /**
     * Class ConsumptionIndexOutputData
     * @package JoyPla\Application\OutputPorts\Consumption\Api;
     */
    class ConsumptionIndexOutputData
    {
        public int $count;
        public array $consumptions;

        public function __construct(array $consumptions, int $count)
        {
            $this->count = $count;
            $this->consumptions = array_map(function (
                Consumption $consumption
            ) {
                return $consumption->toArray();
            },
            $consumptions);
        }
    }

    /**
     * Interface ConsumptionIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Consumption\Api;
     */
    interface ConsumptionIndexOutputPortInterface
    {
        /**
         * @param ConsumptionIndexOutputData $outputData
         */
        function output(ConsumptionIndexOutputData $outputData);
    }
}
