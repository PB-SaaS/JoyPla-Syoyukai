<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Distributor {

    use JoyPla\Application\InputPorts\Api\Distributor\DistributorShowInputData;
    use JoyPla\Application\InputPorts\Api\Distributor\DistributorShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Distributor\DistributorShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Distributor\DistributorShowOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\DistributorRepositoryInterface;

    /**
     * Class DistributorShowInteractor
     * @package JoyPla\Application\Interactors\Distributor\Api
     */
    class DistributorShowInteractor implements DistributorShowInputPortInterface
    {
        /** @var DistributorShowOutputPortInterface */
        private DistributorShowOutputPortInterface $outputPort;

        /** @var DistributorRepositoryInterface */
        private DistributorRepositoryInterface $DistributorRepository;

        /**
         * DistributorShowInteractor constructor.
         * @param DistributorShowOutputPortInterface $outputPort
         */
        public function __construct(DistributorShowOutputPortInterface $outputPort , DistributorRepositoryInterface $DistributorRepository)
        {
            $this->outputPort = $outputPort;
            $this->DistributorRepository = $DistributorRepository;
        }

        /**
         * @param DistributorShowInputData $inputData
         */
        public function handle(DistributorShowInputData $inputData)
        {
            $Distributor = $this->DistributorRepository->findByHospitalId((new HospitalId($inputData->hospitalId)));
            $this->outputPort->output(new DistributorShowOutputData($Distributor));
        }
    }
}


/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Distributor {
    /**
     * Class DistributorShowInputData
     * @package JoyPla\Application\InputPorts\Distributor\Api
     */
    class DistributorShowInputData
    {
        /**
         * DistributorShowInputData constructor.
         */
        public function __construct(string $hospitalId)
        {
            $this->hospitalId = $hospitalId;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Distributor\Api
    */
    interface DistributorShowInputPortInterface
    {
        /**
         * @param DistributorShowInputData $inputData
         */
        function handle(DistributorShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Distributor {

    use JoyPla\Enterprise\Models\Distributor;

    /**
     * Class DistributorShowOutputData
     * @package JoyPla\Application\OutputPorts\Api\Distributor;
     */
    class DistributorShowOutputData
    {
        /** @var string */
        private $createdId;

        /**
         * DistributorShowOutputData constructor.
         */
        public function __construct(array $distributors)
        {
            $this->distributors = $distributors;
        }
    }

    /**
     * Interface DistributorShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Distributor;
    */
    interface DistributorShowOutputPortInterface
    {
        /**
         * @param DistributorShowOutputData $outputData
         */
        function output(DistributorShowOutputData $outputData);
    }
} 