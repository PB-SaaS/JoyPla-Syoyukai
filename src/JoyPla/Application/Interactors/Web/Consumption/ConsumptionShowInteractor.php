<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Web\Consumption {
    use App\Model\Division;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Web\Consumption\ConsumptionShowInputData;
    use JoyPla\Application\InputPorts\Web\Consumption\ConsumptionShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Web\Consumption\ConsumptionShowOutputData;
    use JoyPla\Application\OutputPorts\Web\Consumption\ConsumptionShowOutputPortInterface;
    use JoyPla\Enterprise\Models\ConsumptionId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\consumptionRepositoryInterface;

    /**
     * Class ConsumptionShowInteractor
     * @package JoyPla\Application\Interactors\Web\Consumption
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
        public function __construct(
            ConsumptionShowOutputPortInterface $outputPort,
            ConsumptionRepositoryInterface $consumptionRepository
        ) {
            $this->outputPort = $outputPort;
            $this->consumptionRepository = $consumptionRepository;
        }

        /**
         * @param ConsumptionShowInputData $inputData
         */
        public function handle(ConsumptionShowInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $consumptionId = new ConsumptionId($inputData->consumptionId);

            $consumption = $this->consumptionRepository->find(
                $hospitalId,
                $consumptionId
            );

            if (empty($consumption)) {
                throw new NotFoundException('not found.', 404);
            }

            if (
                $inputData->isMyOnlyDivision &&
                !$consumption
                    ->getDivision()
                    ->getDivisionId()
                    ->equal($inputData->user->divisionId)
            ) {
                throw new Exception('Illegal request', 403);
            }

            $this->outputPort->output(
                new ConsumptionShowOutputData($consumption)
            );
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Web\Consumption {
    use Auth;
    use stdClass;

    /**
     * Class ConsumptionShowInputData
     * @package JoyPla\Application\InputPorts\Web\Consumption
     */
    class ConsumptionShowInputData
    {
        public Auth $user;
        public string $consumptionId;
        public bool $isMyOnlyDivision;
        /**
         * ConsumptionShowInputData constructor.
         */
        public function __construct(
            Auth $user,
            string $consumptionId,
            bool $isMyOnlyDivision
        ) {
            $this->user = $user;
            $this->consumptionId = $consumptionId;
            $this->isMyOnlyDivision = $isMyOnlyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Web\Consumption
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
namespace JoyPla\Application\OutputPorts\Web\Consumption {
    use JoyPla\Enterprise\Models\Consumption;

    /**
     * Class ConsumptionShowOutputData
     * @package JoyPla\Application\OutputPorts\Web\Consumption;
     */
    class ConsumptionShowOutputData
    {
        /** @var array */
        public array $consumption;
        /**
         * ConsumptionShowOutputData constructor.
         */
        public function __construct(Consumption $consumption)
        {
            $this->consumption = $consumption->toArray();
        }
    }

    /**
     * Interface ConsumptionShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Web\Consumption;
     */
    interface ConsumptionShowOutputPortInterface
    {
        /**
         * @param ConsumptionShowOutputData $outputData
         */
        function output(ConsumptionShowOutputData $outputData);
    }
}
