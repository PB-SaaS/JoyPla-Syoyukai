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
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class DistributorShowInteractor
     * @package JoyPla\Application\Interactors\Distributor\Api
     */
    class DistributorShowInteractor implements DistributorShowInputPortInterface
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
         * @param DistributorShowInputData $inputData
         */
        public function handle(DistributorShowInputData $inputData)
        {
            $Distributor = $this->repositoryProvider
                ->getDistributorRepository()
                ->findByHospitalId(new HospitalId($inputData->hospitalId));
            $this->presenterProvider
                ->getDistributorShowPresenter()
                ->output(new DistributorShowOutputData($Distributor));
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
        public array $distributors;

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
