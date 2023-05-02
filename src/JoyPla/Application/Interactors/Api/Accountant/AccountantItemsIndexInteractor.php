<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Accountant {
    use ApiResponse;
    use Collection;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantItemsIndexInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantItemsIndexInputData;
    use JoyPla\Enterprise\Models\Accountant;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class AccountantItemsIndexInteractor
     * @package JoyPla\Application\Interactors\Accountant\Api
     */
    class AccountantItemsIndexInteractor implements
        AccountantItemsIndexInputPortInterface
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
         * @param AccountantItemsIndexInputData $inputData
         */
        public function handle(AccountantItemsIndexInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            [
                $accountants,
                $count,
            ] = $this->repositoryProvider
                ->getAccountantItemRepository()
                ->search($hospitalId, $inputData->search);

            echo (new ApiResponse($accountants, $count, 200, 'success', [
                'AccountantItemsIndexPresenter',
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
     * Class AccountantItemsIndexInputData
     * @package JoyPla\Application\InputPorts\Accountant\Api
     */
    class AccountantItemsIndexInputData
    {
        public Auth $user;
        public stdClass $search;

        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
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
    interface AccountantItemsIndexInputPortInterface
    {
        /**
         * @param AccountantItemsIndexInputData $inputData
         */
        function handle(AccountantItemsIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Accountant {
    use Collection;

    /**
     * Class AccountantItemsIndexOutputData
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    class AccountantItemsIndexOutputData
    {
        public array $data;
        public int $count;
        /**
         * AccountantItemsIndexOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface AccountantItemsIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    interface AccountantItemsIndexOutputPortInterface
    {
        /**
         * @param AccountantItemsIndexOutputData $outputData
         */
        function output(AccountantItemsIndexOutputData $outputData);
    }
}
