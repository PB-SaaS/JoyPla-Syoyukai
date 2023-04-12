<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Division {
    use JoyPla\Application\InputPorts\Api\Division\DivisionIndexInputData;
    use JoyPla\Application\InputPorts\Api\Division\DivisionIndexInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\Division\DivisionIndexOutputData;
    use JoyPla\Application\OutputPorts\Api\Division\DivisionIndexOutputPortInterface;
    use JoyPla\Enterprise\Models\DivisionId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\DivisionRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class DivisionIndexInteractor
     * @package JoyPla\Application\Interactors\Division\Api
     */
    class DivisionIndexInteractor implements DivisionIndexInputPortInterface
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
         * @param DivisionIndexInputData $inputData
         */
        public function handle(DivisionIndexInputData $inputData)
        {
            $divisions = [];
            if ($inputData->isOnlyMyDivision) {
                $divisions[] = $this->repositoryProvider
                    ->getDivisionRepository()
                    ->find(
                        new HospitalId($inputData->user->hospitalId),
                        new DivisionId($inputData->user->divisionId)
                    );
            } else {
                $divisions = $this->repositoryProvider
                    ->getDivisionRepository()
                    ->findByHospitalId(
                        new HospitalId($inputData->user->hospitalId),
                        $inputData->deleted
                    );
            }

            $this->presenterProvider
                ->getDivisionIndexPresenter()
                ->output(new DivisionIndexOutputData($divisions));
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Division {
    use Auth;

    /**
     * Class DivisionIndexInputData
     * @package JoyPla\Application\InputPorts\Division\Api
     */
    class DivisionIndexInputData
    {
        public Auth $user;
        public bool $isOnlyMyDivision = false;
        public bool $deleted = false;

        /**
         * DivisionIndexInputData constructor.
         */
        public function __construct(
            Auth $user,
            bool $isOnlyMyDivision,
            bool $deleted = false
        ) {
            $this->user = $user;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
            $this->deleted = $deleted;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Division\Api
     */
    interface DivisionIndexInputPortInterface
    {
        /**
         * @param DivisionIndexInputData $inputData
         */
        function handle(DivisionIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Division {
    use JoyPla\Enterprise\Models\Division;

    /**
     * Class DivisionIndexOutputData
     * @package JoyPla\Application\OutputPorts\Division\Api;
     */
    class DivisionIndexOutputData
    {
        /** @var array */
        public array $divisions = [];

        /**
         * DivisionIndexOutputData constructor.
         */
        public function __construct(array $divisions)
        {
            $this->divisions = array_map(function (Division $d) {
                return $d->toArray();
            }, $divisions);
        }
    }

    /**
     * Interface DivisionIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Division\Api;
     */
    interface DivisionIndexOutputPortInterface
    {
        /**
         * @param DivisionIndexOutputData $outputData
         */
        function output(DivisionIndexOutputData $outputData);
    }
}
