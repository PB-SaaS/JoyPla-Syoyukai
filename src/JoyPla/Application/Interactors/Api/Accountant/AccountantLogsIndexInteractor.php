<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Accountant {
    use ApiResponse;
    use Collection;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantLogsIndexInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantLogsIndexInputData;
    use JoyPla\Enterprise\Models\Accountant;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class AccountantLogsIndexInteractor
     * @package JoyPla\Application\Interactors\Accountant\Api
     */
    class AccountantLogsIndexInteractor implements
        AccountantLogsIndexInputPortInterface
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
         * @param AccountantLogsIndexInputData $inputData
         */
        public function handle(AccountantLogsIndexInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            [
                $accountants,
                $count,
            ] = $this->repositoryProvider
                ->getAccountantLogRepository()
                ->search($hospitalId, $inputData->search);

            echo (new ApiResponse($accountants, $count, 200, 'success', [
                'AccountantLogsIndexPresenter',
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
     * Class AccountantLogsIndexInputData
     * @package JoyPla\Application\InputPorts\Accountant\Api
     */
    class AccountantLogsIndexInputData
    {
        public Auth $user;
        public stdClass $search;

        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->sortColumn = $search['sortColumn'] ?? 'id';
            $this->search->sortDirection = $search['sortDirection'] ?? 'asc';
            $this->search->itemName = $search['itemName'] ?? '';
            $this->search->makerName = $search['makerName'] ?? '';
            $this->search->itemCode = $search['itemCode'] ?? '';
            $this->search->itemStandard = $search['itemStandard'] ?? '';
            $this->search->itemJANCode = $search['itemJANCode'] ?? '';
            $this->search->yearMonth = $search['yearMonth'] ?? '';
            $this->search->divisionIds = $search['divisionIds'] ?? '';
            $this->search->perPage = $search['perPage'] ?? 1;
            $this->search->currentPage = $search['currentPage'] ?? 1;
            $this->search->distributorIds = $search['distributorIds'] ?? '';
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Accountant\Api
     */
    interface AccountantLogsIndexInputPortInterface
    {
        /**
         * @param AccountantLogsIndexInputData $inputData
         */
        function handle(AccountantLogsIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Accountant {
    use Collection;

    /**
     * Class AccountantLogsIndexOutputData
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    class AccountantLogsIndexOutputData
    {
        public array $data;
        public int $count;
        /**
         * AccountantLogsIndexOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface AccountantLogsIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    interface AccountantLogsIndexOutputPortInterface
    {
        /**
         * @param AccountantLogsIndexOutputData $outputData
         */
        function output(AccountantLogsIndexOutputData $outputData);
    }
}
