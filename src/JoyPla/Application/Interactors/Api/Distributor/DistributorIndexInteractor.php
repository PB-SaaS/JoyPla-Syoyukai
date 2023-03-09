<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Distributor {
    use JoyPla\Application\InputPorts\Api\Distributor\DistributorIndexInputData;
    use JoyPla\Application\InputPorts\Api\Distributor\DistributorIndexInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Distributor\DistributorIndexOutputData;
    use JoyPla\Application\OutputPorts\Api\Distributor\DistributorIndexOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\DistributorRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class DistributorIndexInteractor
     * @package JoyPla\Application\Interactors\Distributor\Api
     */
    class DistributorIndexInteractor implements
        DistributorIndexInputPortInterface
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
         * @param DistributorIndexInputData $inputData
         */
        public function handle(DistributorIndexInputData $inputData)
        {
            $Distributor = $this->repositoryProvider
                ->getDistributorRepository()
                ->findByHospitalId(new HospitalId($inputData->hospitalId));
            $this->presenterProvider
                ->getDistributorIndexPresenter()
                ->output(new DistributorIndexOutputData($Distributor));
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Distributor {
    /**
     * Class DistributorIndexInputData
     * @package JoyPla\Application\InputPorts\Distributor\Api
     */
    class DistributorIndexInputData
    {
        public string $hospitalId;

        public function __construct(string $hospitalId)
        {
            $this->hospitalId = $hospitalId;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Distributor\Api
     */
    interface DistributorIndexInputPortInterface
    {
        /**
         * @param DistributorIndexInputData $inputData
         */
        function handle(DistributorIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Distributor {
    use JoyPla\Enterprise\Models\Distributor;

    /**
     * Class DistributorIndexOutputData
     * @package JoyPla\Application\OutputPorts\Api\Distributor;
     */
    class DistributorIndexOutputData
    {
        public array $distributors;

        public function __construct(array $distributors)
        {
            $this->distributors = $distributors;
        }
    }

    /**
     * Interface DistributorIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Distributor;
     */
    interface DistributorIndexOutputPortInterface
    {
        /**
         * @param DistributorIndexOutputData $outputData
         */
        function output(DistributorIndexOutputData $outputData);
    }
}
