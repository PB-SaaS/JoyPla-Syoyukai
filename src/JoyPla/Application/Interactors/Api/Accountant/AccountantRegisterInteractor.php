<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Accountant {
    use ApiResponse;
    use Collection;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantRegisterInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantRegisterInputData;
    use JoyPla\Enterprise\Models\Accountant;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class AccountantRegisterInteractor
     * @package JoyPla\Application\Interactors\Accountant\Api
     */
    class AccountantRegisterInteractor implements
        AccountantRegisterInputPortInterface
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
         * @param AccountantRegisterInputData $inputData
         */
        public function handle(AccountantRegisterInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);

            $accountant = Accountant::init(
                $inputData->request['accountantDate'],
                $hospitalId->value(),
                $inputData->request['divisionId'],
                $inputData->request['distributorId'],
                $inputData->request['orderId'],
                $inputData->request['receivedId']
            );

            $this->repositoryProvider
                ->getAccountantRepository()
                ->register($accountant);

            echo (new ApiResponse($accountant->toArray(), 1, 200, 'success', [
                'AccountantRegisterPresenter',
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
     * Class AccountantRegisterInputData
     * @package JoyPla\Application\InputPorts\Accountant\Api
     */
    class AccountantRegisterInputData
    {
        public Auth $user;
        public array $request;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            array $request,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->request = [
                'accountantDate' => $request['accountantDate'],
                'divisionId' => $request['divisionId'],
                'distributorId' => $request['distributorId'],
                'orderId' => $request['orderId'],
                'receivedId' => $request['receivedId'],
            ];
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Accountant\Api
     */
    interface AccountantRegisterInputPortInterface
    {
        /**
         * @param AccountantRegisterInputData $inputData
         */
        function handle(AccountantRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Accountant {
    use Collection;

    /**
     * Class AccountantRegisterOutputData
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    class AccountantRegisterOutputData
    {
        public array $data;
        public int $count;
        /**
         * AccountantRegisterOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface AccountantRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    interface AccountantRegisterOutputPortInterface
    {
        /**
         * @param AccountantRegisterOutputData $outputData
         */
        function output(AccountantRegisterOutputData $outputData);
    }
}
