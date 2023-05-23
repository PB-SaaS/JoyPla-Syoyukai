<?php

/***
 * USECASE
 */
//商品一覧表機能のusecaseなのでclass名はItemListでOK
namespace JoyPla\Application\Interactors\Api\ItemList {
    use ApiResponse;
    use Collection;
    use JoyPla\Application\InputPorts\Api\ItemList\ItemListIndexInputPortInterface;
    use JoyPla\Application\InputPorts\Api\ItemList\ItemListIndexInputData;
    use JoyPla\Enterprise\Models\ItemList;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ItemListIndexInteractor
     * @package JoyPla\Application\Interactors\ItemList\Api
     */
    class ItemListIndexInteractor implements ItemListIndexInputPortInterface
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
         * @param ItemListIndexInputData $inputData
         */
        public function handle(ItemListIndexInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);

            [
                $itemLists,
                $count,
            ] = $this->repositoryProvider
                ->getItemListRepository()
                ->search($hospitalId, $inputData->search);

            echo (new ApiResponse($itemLists, $count, 200, 'success', [
                'ItemListIndexPresenter',
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
     * Class ItemListIndexInputData
     * @package JoyPla\Application\InputPorts\ItemList\Api
     */
    class ItemListIndexInputData
    {
        public Auth $user;
        public stdClass $search;

        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->divisionIds = $search['divisionIds'];
            $this->search->itemListName = $search['itemListName'];
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\ItemList\Api
     */
    interface ItemListIndexInputPortInterface
    {
        /**
         * @param ItemListIndexInputData $inputData
         */
        function handle(ItemListIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\ItemList {
    use Collection;

    /**
     * Class ItemListIndexOutputData
     * @package JoyPla\Application\OutputPorts\ItemList\Api;
     */
    class ItemListIndexOutputData
    {
        public array $data;
        public int $count;
        /**
         * ItemListIndexOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface ItemListIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\ItemList\Api;
     */
    interface ItemListIndexOutputPortInterface
    {
        /**
         * @param ItemListIndexOutputData $outputData
         */
        function output(ItemListIndexOutputData $outputData);
    }
}
