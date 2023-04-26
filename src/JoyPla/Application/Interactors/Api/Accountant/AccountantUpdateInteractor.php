<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\Accountant {
    use ApiResponse;
    use Collection;
    use framework\Exception\NotFoundException;
    use framework\Facades\Gate;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantUpdateInputPortInterface;
    use JoyPla\Application\InputPorts\Api\Accountant\AccountantUpdateInputData;
    use JoyPla\Enterprise\Models\Accountant;
    use JoyPla\Enterprise\Models\AccountantId;
    use JoyPla\Enterprise\Models\AccountantItem;
    use JoyPla\Enterprise\Models\AccountantItemId;
    use JoyPla\Enterprise\Models\AccountantService;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class AccountantUpdateInteractor
     * @package JoyPla\Application\Interactors\Accountant\Api
     */
    class AccountantUpdateInteractor implements
        AccountantUpdateInputPortInterface
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
         * @param AccountantUpdateInputData $inputData
         */
        public function handle(AccountantUpdateInputData $inputData)
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

            $oldAccountant = clone $accountant;

            $items = [];
            foreach ($inputData->items as $item) {
                $items[] = AccountantItem::init(
                    $item['index'],
                    $accountantId->value(),
                    $item['method'],
                    $item['action'],
                    $item['accountantItemId'],
                    $item['itemId'],
                    $item['makerName'],
                    $item['itemName'],
                    $item['itemCode'],
                    $item['itemStandard'],
                    $item['itemJANCode'],
                    $item['count'],
                    $item['unit'],
                    $item['price'],
                    $item['taxrate']
                );
            }

            $accountant->setItems($items);

            $accoutantItemLogs = AccountantService::checkAccountant(
                $accountant,
                $oldAccountant,
                $inputData->user->id
            );

            $this->repositoryProvider
                ->getAccountantRepository()
                ->save($accountant);

            $this->repositoryProvider
                ->getAccountantRepository()
                ->saveItemLog($accoutantItemLogs);

            echo (new ApiResponse($accountant->toArray(), 1, 200, 'success', [
                'AccountantUpdatePresenter',
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
     * Class AccountantUpdateInputData
     * @package JoyPla\Application\InputPorts\Accountant\Api
     */
    class AccountantUpdateInputData
    {
        public Auth $user;
        public string $accountantId;
        public array $items;

        public function __construct(
            Auth $user,
            string $accountantId,
            array $items = []
        ) {
            $this->user = $user;
            $this->accountantId = $accountantId;

            $this->items = array_map(function ($item) {
                return [
                    'index' => $item['index'],
                    'accountantItemId' => $item['accountantItemId'],
                    'method' => $item['method'],
                    'action' => $item['action'],
                    'itemId' => $item['itemId'],
                    'makerName' => $item['makerName'],
                    'itemName' => $item['itemName'],
                    'itemCode' => $item['itemCode'],
                    'itemStandard' => $item['itemStandard'],
                    'itemJANCode' => $item['itemJANCode'],
                    'count' => (int) $item['count'],
                    'unit' => $item['unit'],
                    'price' => (float) $item['price'],
                    'taxrate' => (int) $item['taxrate'],
                ];
            }, $items);
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Accountant\Api
     */
    interface AccountantUpdateInputPortInterface
    {
        /**
         * @param AccountantUpdateInputData $inputData
         */
        function handle(AccountantUpdateInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\Accountant {
    use Collection;

    /**
     * Class AccountantUpdateOutputData
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    class AccountantUpdateOutputData
    {
        public array $data;
        public int $count;
        /**
         * AccountantUpdateOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface AccountantUpdateOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Accountant\Api;
     */
    interface AccountantUpdateOutputPortInterface
    {
        /**
         * @param AccountantUpdateOutputData $outputData
         */
        function output(AccountantUpdateOutputData $outputData);
    }
}
