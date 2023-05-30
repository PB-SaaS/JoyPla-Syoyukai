<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\StocktakingList {
    use ApiResponse;
    use Collection;
    use framework\Exception\NotFoundException;
    use framework\Facades\Gate;
    use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListUpdateInputPortInterface;
    use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListUpdateInputData;
    use JoyPla\Enterprise\Models\StocktakingList;
    use JoyPla\Enterprise\Models\StocktakingListId;
    use JoyPla\Enterprise\Models\StocktakingListRow;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class StocktakingListUpdateInteractor
     * @package JoyPla\Application\Interactors\StocktakingList\Api
     */
    class StocktakingListUpdateInteractor implements
        StocktakingListUpdateInputPortInterface
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
         * @param StocktakingListUpdateInputData $inputData
         */
        public function handle(StocktakingListUpdateInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $stocktakingListId = new StocktakingListId($inputData->stocktakingListId);
            $stocktakingListName = $inputData->stocktakingListName;

            $stocktakingList = $this->repositoryProvider
                ->getStocktakingListRepository()
                ->findByStocktakingListId($hospitalId, $stocktakingListId);

            if (
                Gate::allows('is_user') &&
                $stocktakingList->getDivisionId()->value() !== $inputData->user->divisionId
            ) {
                throw new NotFoundException('not found', '404');
            }

            $items = [];
            foreach ($inputData->items as $item) {
                $items[] = StocktakingListRow::init(
                    $item['index'],
                    $stocktakingListId->value(),
                    $item['stocktakingListRowId'],
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
                    $item['hospitalId'],
                    $item['distributorName'],
                    $item['rackName'],
                    $item['mandatoryFlag']
                );
            }

            $stocktakingList->setItems($items);
            $stocktakingList->setStocktakingListName($stocktakingListName);

            $this->repositoryProvider
                ->getStocktakingListRepository()
                ->save($stocktakingList);

            echo (new ApiResponse($stocktakingList->toArray(), 1, 200, 'success', [
                'StocktakingListUpdatePresenter',
            ]))->toJson();
        }
    }
}

/***
 * INPUT
 */
namespace JoyPla\Application\InputPorts\Api\StocktakingList {
    use Auth;
    use stdClass;

    /**
     * Class StocktakingListUpdateInputData
     * @package JoyPla\Application\InputPorts\StocktakingList\Api
     */
    class StocktakingListUpdateInputData
    {
        public Auth $user;
        public string $stocktakingListId;
        public array $items;
        public string $stocktakingListName;

        public function __construct(
            Auth $user,
            string $stocktakingListId,
            array $items = [],
            string $stocktakingListName
        ) {
            $this->user = $user;
            $this->stocktakingListId = $stocktakingListId;

            $this->items = array_map(function ($item) {
                return [
                    'index' => $item['index'],
                    'stocktakingListId' => $item['stocktakingListId'],
                    'stocktakingListRowId' => $item['stocktakingListRowId'],
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
                    'rackName' => $item['rackName'],
                    'mandatoryFlag' => $item['mandatoryFlag'],
                ];
            }, $items);
            $this->stocktakingListName = $stocktakingListName;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\StocktakingList\Api
     */
    interface StocktakingListUpdateInputPortInterface
    {
        /**
         * @param StocktakingListUpdateInputData $inputData
         */
        function handle(StocktakingListUpdateInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\StocktakingList {
    use Collection;

    /**
     * Class StocktakingListUpdateOutputData
     * @package JoyPla\Application\OutputPorts\StocktakingList\Api;
     */
    class StocktakingListUpdateOutputData
    {
        public array $data;
        public int $count;
        /**
         * StocktakingListUpdateOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface StocktakingListUpdateOutputPortInterface
     * @package JoyPla\Application\OutputPorts\StocktakingList\Api;
     */
    interface StocktakingListUpdateOutputPortInterface
    {
        /**
         * @param StocktakingListUpdateOutputData $outputData
         */
        function output(StocktakingListUpdateOutputData $outputData);
    }
}
