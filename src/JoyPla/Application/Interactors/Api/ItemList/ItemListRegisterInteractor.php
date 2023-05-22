<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\ItemList {
    use ApiResponse;
    use Collection;
    use JoyPla\Application\InputPorts\Api\ItemList\ItemListRegisterInputPortInterface;
    use JoyPla\Application\InputPorts\Api\ItemList\ItemListRegisterInputData;
    use JoyPla\Enterprise\Models\ItemList;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ItemListRegisterInteractor
     * @package JoyPla\Application\Interactors\ItemList\Api
     */
    class ItemListRegisterInteractor implements
        ItemListRegisterInputPortInterface
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
         * @param ItemListRegisterInputData $inputData
         */
        public function handle(ItemListRegisterInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);

            $itemList = ItemList::init(
                '', //itemListId
                $hospitalId->value(),
                $inputData->request['divisionId'],
                $inputData->request['itemListName'],
                $inputData->request['usableStatus'],
                '0' //itemsNumber
            );

            $this->repositoryProvider
                ->getItemListRepository()
                ->register($itemList);

            echo (new ApiResponse($itemList->toArray(), 1, 200, 'success', [
                'ItemListRegisterPresenter', 
            ]))->toJson();
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\ItemList {
    use Auth;
    use stdClass;

    /**
     * Class ItemListRegisterInputData
     * @package JoyPla\Application\InputPorts\ItemList\Api
     */
    class ItemListRegisterInputData
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
                'itemListId' => $request['itemListId'],
                'hospitalId' => $user->hospitalId,
                'divisionId' => $request['divisionId'],
                'itemListName' => $request['itemListName'],
                'usableStatus' => $request['usableStatus'],
            ];
//            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\ItemList\Api
     */
    interface ItemListRegisterInputPortInterface
    {
        /**
         * @param ItemListRegisterInputData $inputData
         */
        function handle(ItemListRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\ItemList {
    use Collection;

    /**
     * Class ItemListRegisterOutputData
     * @package JoyPla\Application\OutputPorts\ItemList\Api;
     */
    class ItemListRegisterOutputData
    {
        public array $data;
        public int $count;
        /**
         * ItemListRegisterOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface ItemListRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\ItemList\Api;
     */
    interface AccountantItemListRegisterOutputPortInterface
    {
        /**
         * @param ItemListRegisterOutputData $outputData
         */
        function output(ItemListRegisterOutputData $outputData);
    }
}
