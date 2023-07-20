<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Accountant {
    use ApiResponse;
    use Collection;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantIndexInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantIndexInputData;
    use JoyPla\Enterprise\Models\Accountant;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class AccountantIndexInteractor
     * @package JoyPla\Application\Interactors\Accountant\Api
     */
    class AccountantIndexInteractor implements AccountantIndexInputPortInterface
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
         * @param AccountantIndexInputData $inputData
         */
        public function handle(AccountantIndexInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);

            [
                $accountants,
                $count,
            ] = $this->repositoryProvider
                ->getAccountantRepository()
                ->search($hospitalId, $inputData->search);

            echo (new ApiResponse($accountants, $count, 200, 'success', [
                'AccountantIndexPresenter',
            ]))->toJson();
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\Accountant {
    use Auth;
    use stdClass;

    /**
     * Class AccountantIndexInputData
     * @package JoyPla\Application\InputPorts\Accountant\Api
     */
    class AccountantIndexInputData
    {
        public Auth $user;
        public stdClass $search;

        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->distributorIds = $search['distributorIds'];
            $this->search->yearMonth = $search['yearMonth'];
            $this->search->divisionIds = $search['divisionIds'];
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Accountant\Api
     */
    interface AccountantIndexInputPortInterface
    {
        /**
         * @param AccountantIndexInputData $inputData
         */
        function handle(AccountantIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Accountant {
    use Collection;

    /**
     * Class AccountantIndexOutputData
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    class AccountantIndexOutputData
    {
        public array $data;
        public int $count;
        /**
         * AccountantIndexOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface AccountantIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    interface AccountantIndexOutputPortInterface
    {
        /**
         * @param AccountantIndexOutputData $outputData
         */
        function output(AccountantIndexOutputData $outputData);
    }
}
