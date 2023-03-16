<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\ItemRequest {
    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestHistoryInputData;
    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestHistoryInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestHistoryOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestHistoryOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRequestRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ItemRequestHistoryInteractor
     * @package JoyPla\Application\Interactors\Api\ItemRequest
     */
    class ItemRequestHistoryInteractor implements
        ItemRequestHistoryInputPortInterface
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
         * @param ItemRequestHistoryInputData $inputData
         */
        public function handle(ItemRequestHistoryInputData $inputData)
        {
            [$itemRequests, $count] = $this->repository->search(
                new HospitalId($inputData->user->hospitalId),
                $inputData->search
            );
            $this->presenterProvider
                ->getItemRequestHistoryPresenter()
                ->output(
                    new ItemRequestHistoryOutputData($itemRequests, $count)
                );
        }
    }
}

/***
 * INPUT
 */

namespace JoyPla\Application\InputPorts\Api\ItemRequest {
    use Auth;
    use stdClass;

    /**
     * Class ItemRequestHistoryInputData
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    class ItemRequestHistoryInputData
    {
        public Auth $user;
        public stdClass $search;
        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->registrationDate = $search['yearMonth'];
            $this->search->itemName = $search['itemName'];
            $this->search->makerName = $search['makerName'];
            $this->search->itemCode = $search['itemCode'];
            $this->search->itemStandard = $search['itemStandard'];
            $this->search->itemJANCode = $search['itemJANCode'];
            $this->search->sourceDivisionIds = $search['sourceDivisionIds'];
            $this->search->targetDivisionIds = $search['targetDivisionIds'];
            $this->search->requestType = $search['requestType'];
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
        }
    }

    /**
     * Interface ItemRequestHistoryInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    interface ItemRequestHistoryInputPortInterface
    {
        /**
         * @param ItemRequestHistoryInputData $inputData
         */
        function handle(ItemRequestHistoryInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\ItemRequest {
    use Collection;
    use JoyPla\Enterprise\Models\ItemRequest;

    /**
     * Class ItemRequestHistoryOutputData
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    class ItemRequestHistoryOutputData
    {
        public array $itemRequests;
        public int $count;

        public function __construct(array $itemRequests, int $count)
        {
            $this->itemRequests = array_map(function (
                ItemRequest $itemRequest
            ) {
                return $itemRequest->toArray();
            },
            $itemRequests);
            $this->count = $count;
        }
    }

    /**
     * Interface ItemRequestHistoryOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    interface ItemRequestHistoryOutputPortInterface
    {
        /**
         * @param ItemRequestHistoryOutputData $outputData
         */
        function output(ItemRequestHistoryOutputData $outputData);
    }
}
