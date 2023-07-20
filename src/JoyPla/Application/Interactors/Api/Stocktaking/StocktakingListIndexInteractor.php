<?php

/***
 * USECASE
 */
//商品一覧表機能のusecaseなのでclass名はStocktakingListでOK
namespace JoyPla\Application\Interactors\Api\StocktakingList {
    use ApiResponse;
    use Collection;
    use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListIndexInputPortInterface;
    use JoyPla\Application\InputPorts\Api\StocktakingList\StocktakingListIndexInputData;
    use JoyPla\Enterprise\Models\StocktakingList;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class StocktakingListIndexInteractor
     * @package JoyPla\Application\Interactors\Stocktaking\Api
     */
    class StocktakingListIndexInteractor implements StocktakingListIndexInputPortInterface
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
         * @param StocktakingListIndexInputData $inputData
         */
        public function handle(StocktakingListIndexInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);

            [
                $StocktakingLists,
                $count,
            ] = $this->repositoryProvider
                ->getStocktakingListRepository()
                ->search($hospitalId, $inputData->search);

            echo (new ApiResponse($StocktakingLists, $count, 200, 'success', [
                'StocktakingListIndexPresenter',
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
     * Class StocktakingListIndexInputData
     * @package JoyPla\Application\InputPorts\StocktakingList\Api
     */
    class StocktakingListIndexInputData
    {
        public Auth $user;
        public stdClass $search;

        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->divisionIds = $search['divisionIds'];
            $this->search->stocktakingListName = $search['stocktakingListName'];
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\StocktakingList\Api
     */
    interface StocktakingListIndexInputPortInterface
    {
        /**
         * @param StocktakingListIndexInputData $inputData
         */
        function handle(StocktakingListIndexInputData $inputData);
    }
}

/***
 * OUTPUT
 */
namespace JoyPla\Application\OutputPorts\Api\StocktakingList {
    use Collection;

    /**
     * Class StocktakingListIndexOutputData
     * @package JoyPla\Application\OutputPorts\StocktakingList\Api;
     */
    class StocktakingListIndexOutputData
    {
        public array $data;
        public int $count;
        /**
         * StocktakingListIndexOutputData constructor.
         */
        public function __construct(array $data, int $count, $type)
        {
            $this->data = $data;
            $this->count = $count;
        }
    }

    /**
     * Interface StocktakingListIndexOutputPortInterface
     * @package JoyPla\Application\OutputPorts\StocktakingList\Api;
     */
    interface StocktakingListIndexOutputPortInterface
    {
        /**
         * @param StocktakingListIndexOutputData $outputData
         */
        function output(StocktakingListIndexOutputData $outputData);
    }
}
