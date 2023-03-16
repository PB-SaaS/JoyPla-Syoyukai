<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\ReceivedReturn {
    use JoyPla\Application\InputPorts\Api\ReceivedReturn\ReturnShowInputData;
    use JoyPla\Application\InputPorts\Api\ReceivedReturn\ReturnShowInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\ReceivedReturn\ReturnShowOutputData;
    use JoyPla\Application\OutputPorts\Api\ReceivedReturn\ReturnShowOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ReturnRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ReturnShowInteractor
     * @package JoyPla\Application\Interactors\Api\Return
     */
    class ReturnShowInteractor implements ReturnShowInputPortInterface
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
         * @param ReturnShowInputData $inputData
         */
        public function handle(ReturnShowInputData $inputData)
        {
            [
                $returns,
                $count,
            ] = $this->repositoryProvider
                ->getReturnRepository()
                ->search(
                    new HospitalId($inputData->user->hospitalId),
                    $inputData->search
                );
            $this->presenterProvider
                ->getReturnShowPresenter()
                ->output(new ReturnShowOutputData($returns, $count));
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\ReceivedReturn {
    use Auth;
    use stdClass;

    /**
     * Class ReturnShowInputData
     * @package JoyPla\Application\InputPorts\Api\Return
     */
    class ReturnShowInputData
    {
        public Auth $user;
        public stdClass $search;

        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->itemName = $search['itemName'];
            $this->search->makerName = $search['makerName'];
            $this->search->itemCode = $search['itemCode'];
            $this->search->itemStandard = $search['itemStandard'];
            $this->search->itemJANCode = $search['itemJANCode'];
            $this->search->registerDate = $search['registerDate'];
            $this->search->returnDate = $search['returnDate'];
            $this->search->divisionIds = $search['divisionIds'];
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\Return
     */
    interface ReturnShowInputPortInterface
    {
        /**
         * @param ReturnShowInputData $inputData
         */
        function handle(ReturnShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\ReceivedReturn {
    use JoyPla\Enterprise\Models\ReturnData;

    /**
     * Class ReturnShowOutputData
     * @package JoyPla\Application\OutputPorts\Api\Return;
     */
    class ReturnShowOutputData
    {
        public int $count;
        public array $returns;

        public function __construct(array $returns, int $count)
        {
            $this->count = $count;
            $this->returns = array_map(function (ReturnData $return) {
                return $return->toArray();
            }, $returns);
        }
    }

    /**
     * Interface ReturnShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\Return;
     */
    interface ReturnShowOutputPortInterface
    {
        /**
         * @param ReturnShowOutputData $outputData
         */
        function output(ReturnShowOutputData $outputData);
    }
}
