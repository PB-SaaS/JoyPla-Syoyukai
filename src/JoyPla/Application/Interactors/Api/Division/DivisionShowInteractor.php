<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Division {

    use JoyPla\Application\InputPorts\Api\Division\DivisionShowInputData;
    use JoyPla\Application\InputPorts\Api\Division\DivisionShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Division\DivisionShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Division\DivisionShowOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\DivisionRepositoryInterface;

    /**
     * Class DivisionShowInteractor
     * @package JoyPla\Application\Interactors\Division\Api
     */
    class DivisionShowInteractor implements DivisionShowInputPortInterface
    {
        /** @var DivisionShowOutputPortInterface */
        private DivisionShowOutputPortInterface $outputPort;

        /** @var DivisionRepositoryInterface */
        private DivisionRepositoryInterface $divisionRepository;

        /**
         * DivisionShowInteractor constructor.
         * @param DivisionShowOutputPortInterface $outputPort
         */
        public function __construct(DivisionShowOutputPortInterface $outputPort , DivisionRepositoryInterface $divisionRepository)
        {
            $this->outputPort = $outputPort;
            $this->divisionRepository = $divisionRepository;
        }

        /**
         * @param DivisionShowInputData $inputData
         */
        public function handle(DivisionShowInputData $inputData)
        {
            $division = $this->divisionRepository->findByHospitalId((new HospitalId($inputData->hospitalId)));
            $this->outputPort->output(new DivisionShowOutputData($division));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Division {
    /**
     * Class DivisionShowInputData
     * @package JoyPla\Application\InputPorts\Division\Api
     */
    class DivisionShowInputData
    {
        /**
         * DivisionShowInputData constructor.
         */
        public function __construct(string $hospitalId)
        {
            $this->hospitalId = $hospitalId;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Division\Api
    */
    interface DivisionShowInputPortInterface
    {
        /**
         * @param DivisionShowInputData $inputData
         */
        function handle(DivisionShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Division {

    use JoyPla\Enterprise\Models\Division;

    /**
     * Class DivisionShowOutputData
     * @package JoyPla\Application\OutputPorts\Division\Api;
     */
    class DivisionShowOutputData
    {
        /** @var string */
        private $createdId;

        /**
         * DivisionShowOutputData constructor.
         */
        public function __construct(array $divisions)
        {
            $this->divisions = array_map(
                function(Division $d)
                {
                    return $d->toArray();
                }, $divisions
            );
        }
    }

    /**
     * Interface DivisionShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Division\Api;
    */
    interface DivisionShowOutputPortInterface
    {
        /**
         * @param DivisionShowOutputData $outputData
         */
        function output(DivisionShowOutputData $outputData);
    }
}