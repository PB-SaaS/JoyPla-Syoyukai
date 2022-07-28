<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Web\Consumption {

    use App\Model\Division;
    use JoyPla\Application\InputPorts\Web\Consumption\ConsumptionIndexInputData;
    use JoyPla\Application\InputPorts\Web\Consumption\ConsumptionIndexInputPortInterface;
    use JoyPla\Application\OutputPorts\Web\Consumption\ConsumptionIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Consumption\ConsumptionIndexOutputPortInterface;
    use JoyPla\Enterprise\Models\ConsumptionId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\consumptionRepositoryInterface;

    /**
     * Class ConsumptionIndexInteractor
     * @package JoyPla\Application\Interactors\Web\Consumption
     */
    class ConsumptionIndexInteractor implements ConsumptionIndexInputPortInterface
    {
        /** @var ConsumptionIndexOutputPortInterface */
        private ConsumptionIndexOutputPortInterface $outputPort;

        /** @var ConsumptionRepositoryInterface */
        private ConsumptionRepositoryInterface $consumptionRepository;

        /**
         * ConsumptionIndexInteractor constructor.
         * @param ConsumptionIndexOutputPortInterface $outputPort
         */
        public function __construct(ConsumptionIndexOutputPortInterface $outputPort , ConsumptionRepositoryInterface $consumptionRepository)
        {
            $this->outputPort = $outputPort;
            $this->consumptionRepository = $consumptionRepository;
        }

        /**
         * @param ConsumptionIndexInputData $inputData
         */
        public function handle(ConsumptionIndexInputData $inputData)
        {

            $hospitalId = new HospitalId($inputData->hospitalId);
            $consumptionId = new ConsumptionId($inputData->consumptionId);

            $consumption = $this->consumptionRepository->index($hospitalId,$consumptionId);

            $this->outputPort->output(new ConsumptionIndexOutputData($consumption));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Web\Consumption {

    use stdClass;

    /**
     * Class ConsumptionIndexInputData
     * @package JoyPla\Application\InputPorts\Web\Consumption
     */
    class ConsumptionIndexInputData
    {
        /**
         * ConsumptionIndexInputData constructor.
         */
        public function __construct(string $hospitalId , string $consumptionId)
        {
            $this->hospitalId = $hospitalId;
            $this->consumptionId= $consumptionId;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Web\Consumption
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
namespace JoyPla\Application\OutputPorts\Web\Consumption {

    use JoyPla\Enterprise\Models\Consumption;

    /**
     * Class ConsumptionIndexOutputData
     * @package JoyPla\Application\OutputPorts\Web\Consumption;
     */
    class ConsumptionIndexOutputData
    {
        /** @var string */

        /**
         * ConsumptionIndexOutputData constructor.
         */
        public function __construct(Consumption $consumption)
        {
            $this->consumption = $consumption->toArray();
        }
    }

    /**
     * Interface ConsumptionIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Web\Consumption;
    */
    interface ConsumptionIndexOutputPortInterface
    {
        /**
         * @param ConsumptionIndexOutputData $outputData
         */
        function output(ConsumptionIndexOutputData $outputData);
    }
} 