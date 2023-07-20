<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Accountant {
    use ApiResponse;
    use Collection;
    use framework\Exception\NotFoundException;
    use framework\Facades\Gate;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantShowInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantShowInputData;
    use JoyPla\Enterprise\Models\AccountantId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class AccountantShowInteractor
     * @package JoyPla\Application\Interactors\Accountant\Api
     */
    class AccountantShowInteractor implements AccountantShowInputPortInterface
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
         * @param AccountantShowInputData $inputData
         */
        public function handle(AccountantShowInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $accountantId = new AccountantId($inputData->accountantId);

            $accountant = $this->repositoryProvider
                ->getAccountantRepository()
                ->findByAccountantId($hospitalId, $accountantId);

            if (
                Gate::allows('is_user') &&
                $accountant->getDivisionId()->value() !==
                    $inputData->user->divisionId
            ) {
                throw new NotFoundException('not found', '404');
            }

            echo (new ApiResponse($accountant->toArray(), 1, 200, 'success', [
                'AccountantShowPresenter',
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
     * Class AccountantShowInputData
     * @package JoyPla\Application\InputPorts\Accountant\Api
     */
    class AccountantShowInputData
    {
        public Auth $user;
        public string $accountantId;

        public function __construct(Auth $user, string $accountantId)
        {
            $this->user = $user;
            $this->accountantId = $accountantId;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Accountant\Api
     */
    interface AccountantShowInputPortInterface
    {
        /**
         * @param AccountantShowInputData $inputData
         */
        function handle(AccountantShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Accountant {
    use Collection;

    /**
     * Class AccountantShowOutputData
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    class AccountantShowOutputData
    {
        public array $data;
        public int $count;
        /**
         * AccountantShowOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface AccountantShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    interface AccountantShowOutputPortInterface
    {
        /**
         * @param AccountantShowOutputData $outputData
         */
        function output(AccountantShowOutputData $outputData);
    }
}
