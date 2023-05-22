<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\ItemList {
    use ApiResponse;
    use Collection;
    use framework\Exception\NotFoundException;
    use framework\Facades\Gate;
    use JoyPla\Application\InputPorts\Api\ItemList\ItemListUpdateInputPortInterface;
    use JoyPla\Application\InputPorts\Api\ItemList\ItemListUpdateInputData;
    use JoyPla\Enterprise\Models\ItemList;
    use JoyPla\Enterprise\Models\ItemListId;
    use JoyPla\Enterprise\Models\ItemListRow;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ItemListUpdateInteractor
     * @package JoyPla\Application\Interactors\ItemList\Api
     */
    class ItemListUpdateInteractor implements
        ItemListUpdateInputPortInterface
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
         * @param ItemListUpdateInputData $inputData
         */
        public function handle(ItemListUpdateInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $itemListId = new ItemListId($inputData->itemListId);
            $itemListName = $inputData->itemListName;

            $itemList = $this->repositoryProvider
                ->getItemListRepository()
                ->findByItemListId($hospitalId, $itemListId);

            if (
                Gate::allows('is_user') &&
                $itemList->getDivisionId()->value() !== $inputData->user->divisionId && 
                $itemList->getUsableStatus() === '1'
            ) {
                throw new NotFoundException('not found', '404');
            }

            $items = [];
            foreach ($inputData->items as $item) {
                $items[] = ItemListRow::init(
                    $item['index'],
                    $itemListId->value(),
                    $item['itemListRowId'],
                    $item['itemId'],
                    $item['inHospitalItemId'],
                    $item['makerName'],
                    $item['itemName'],
                    $item['itemCode'],
                    $item['itemStandard'],
                    $item['itemJANCode'],
                    $item['quantity'],
                    $item['quantityUnit'],
                    $item['itemUnit'],
                    $item['itemLabelBarcode'],
                    $item['distributorId'],
                    $item['hospitalId']
                );
            }

            $itemList->setItems($items);
            $itemList->setItemListName($itemListName);

            $this->repositoryProvider
                ->getItemListRepository()
                ->save($itemList);

            echo (new ApiResponse($itemList->toArray(), 1, 200, 'success', [
                'ItemListUpdatePresenter',
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
     * Class ItemListUpdateInputData
     * @package JoyPla\Application\InputPorts\ItemList\Api
     */
    class ItemListUpdateInputData
    {
        public Auth $user;
        public string $itemListId;
        public array $items;
        public string $itemListName;

        public function __construct(
            Auth $user,
            string $itemListId,
            array $items = [],
            string $itemListName
        ) {
            $this->user = $user;
            $this->itemListId = $itemListId;

            $this->items = array_map(function ($item) {
                return [
                    'index' => $item['index'],
                    'itemListId' => $item['itemListId'],
                    'itemListRowId' => $item['itemListRowId'],
                    'itemId' => $item['itemId'],
                    'hospitalId' => $this->user->hospitalId,
                    'inHospitalItemId' => $item['inHospitalItemId'],
                    'makerName' => $item['makerName'],
                    'itemName' => $item['itemName'],
                    'itemCode' => $item['itemCode'],
                    'itemStandard' => $item['itemStandard'],
                    'itemJANCode' => $item['itemJANCode'],
                    'quantity' => $item['quantity'],
                    'quantityUnit' => $item['quantityUnit'],
                    'itemUnit' => $item['itemUnit'],
                    'itemLabelBarcode' => $item['itemLabelBarcode'],
                    'distributorId' => $item['distributorId'],
                ];
            }, $items);
            $this->itemListName = $itemListName;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\ItemList\Api
     */
    interface ItemListUpdateInputPortInterface
    {
        /**
         * @param ItemListUpdateInputData $inputData
         */
        function handle(ItemListUpdateInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\ItemList {
    use Collection;

    /**
     * Class ItemListUpdateOutputData
     * @package JoyPla\Application\OutputPorts\ItemList\Api;
     */
    class ItemListUpdateOutputData
    {
        public array $data;
        public int $count;
        /**
         * ItemListUpdateOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface ItemListUpdateOutputPortInterface
     * @package JoyPla\Application\OutputPorts\ItemList\Api;
     */
    interface ItemListUpdateOutputPortInterface
    {
        /**
         * @param ItemListUpdateOutputData $outputData
         */
        function output(ItemListUpdateOutputData $outputData);
    }
}
