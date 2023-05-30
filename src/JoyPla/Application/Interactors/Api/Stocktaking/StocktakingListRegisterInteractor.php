<?php

/***
 * USECASE
 */
namespace JoyPla\Application\Interactors\Api\StocktakingList {
    use ApiResponse;
    use Collection;
    use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListRegisterInputPortInterface;
    use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListRegisterInputData;
    use JoyPla\Enterprise\Models\StocktakingList;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class StocktakingListRegisterInteractor
     * @package JoyPla\Application\Interactors\StocktakingList\Api
     */
    class StocktakingListRegisterInteractor implements
        StocktakingListRegisterInputPortInterface
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
         * @param StocktakingListRegisterInputData $inputData
         */
        public function handle(StocktakingListRegisterInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);

            $stocktakingList = StocktakingList::init(
                $inputData->request['stocktakingListId'], //stocktakingListIdは病院IDと部署DBの合成だから
                $hospitalId->value(),
                $inputData->request['divisionId'],
                $inputData->request['stocktakingListName'],
                '0' //itemsNumber
            );

            $this->repositoryProvider
                ->getStocktakingListRepository()
                ->register($stocktakingList);

            echo (new ApiResponse($stocktakingList->toArray(), 1, 200, 'success', [
                'StocktakingListRegisterPresenter', 
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
     * Class StocktakingListRegisterInputData
     * @package JoyPla\Application\InputPorts\StocktakingList\Api
     */
    class StocktakingListRegisterInputData
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
                'stocktakingListId' => $user->hospitalId . $request['divisionId'],
                'hospitalId' => $user->hospitalId,
                'divisionId' => $request['divisionId'],
                'stocktakingListName' => $request['stocktakingListName'],
            ];
//            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\StocktakingList\Api
     */
    interface StocktakingListRegisterInputPortInterface
    {
        /**
         * @param StocktakingListRegisterInputData $inputData
         */
        function handle(StocktakingListRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\StocktakingList {
    use Collection;

    /**
     * Class StocktakingListRegisterOutputData
     * @package JoyPla\Application\OutputPorts\StocktakingList\Api;
     */
    class StocktakingListRegisterOutputData
    {
        public array $data;
        public int $count;
        /**
         * StocktakingListRegisterOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface StocktakingListRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\StocktakingList\Api;
     */
    interface StocktakingListRegisterOutputPortInterface
    {
        /**
         * @param StocktakingListRegisterOutputData $outputData
         */
        function output(StocktakingListRegisterOutputData $outputData);
    }
}
