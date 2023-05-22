<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\ItemList {
    use ApiResponse;
    use Collection;
    use framework\Exception\NotFoundException;
    use framework\Facades\Gate;
    use JoyPla\Application\InputPorts\Api\ItemList\ItemListShowInputPortInterface;
    use JoyPla\Application\InputPorts\Api\ItemList\ItemListShowInputData;
    use JoyPla\Enterprise\Models\ItemListId;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ItemListShowInteractor
     * @package JoyPla\Application\Interactors\ItemList\Api
     */
    class ItemListShowInteractor implements ItemListShowInputPortInterface
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
         * @param ItemListShowInputData $inputData
         */
        public function handle(ItemListShowInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $itemListId = new ItemListId($inputData->itemListId);

            $itemList = $this->repositoryProvider
                ->getItemListRepository()
                ->findByItemListId($hospitalId, $itemListId);

            if (
                Gate::allows('is_user') &&
                $itemList->getDivisionId()->value() !== $inputData->user->divisionId &&
                $itemList->usableStatus === '1'
            ) {
                throw new NotFoundException('not found', '404');
            }

            echo (new ApiResponse($itemList->toArray(), 1, 200, 'success', [
                'ItemListShowPresenter',
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
     * Class ItemListShowInputData
     * @package JoyPla\Application\InputPorts\ItemList\Api
     */
    class ItemListShowInputData
    {
        public Auth $user;
        public string $itemListId;

        public function __construct(Auth $user, string $itemListId)
        {
            $this->user = $user;
            $this->itemListId = $itemListId;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\ItemList\Api
     */
    interface ItemListShowInputPortInterface
    {
        /**
         * @param ItemListShowInputData $inputData
         */
        function handle(ItemListShowInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\ItemList {
    use Collection;

    /**
     * Class ItemListShowOutputData
     * @package JoyPla\Application\OutputPorts\ItemList\Api;
     */
    class ItemListShowOutputData
    {
        public array $data;
        public int $count;
        /**
         * ItemListShowOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface ItemListShowOutputPortInterface
     * @package JoyPla\Application\OutputPorts\ItemList\Api;
     */
    interface ItemListShowOutputPortInterface
    {
        /**
         * @param ItemListShowOutputData $outputData
         */
        function output(ItemListShowOutputData $outputData);
    }
}
