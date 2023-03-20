<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Web\ItemRequest {
    use JoyPla\Application\InputPorts\Web\ItemRequest\PickingListInputData;
    use JoyPla\Application\InputPorts\Web\ItemRequest\PickingListInputPortInterface;
    use JoyPla\Application\OutputPorts\Web\ItemRequest\PickingListOutputData;
    use JoyPla\Application\OutputPorts\Web\ItemRequest\PickingListOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\TotalRequestItem;
    use JoyPla\InterfaceAdapters\GateWays\Repository\TotalizationRepositoryInterface;
    use JoyPla\Service\Presenter\Web\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class PickingListInteractor
     * @package JoyPla\Application\Interactors\Web\ItemRequest
     */
    class PickingListInteractor implements PickingListInputPortInterface
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
         * @param PickingListInputData $inputData
         */
        public function handle(PickingListInputData $inputData)
        {
            [
                $totalRequestItems,
                $count,
            ] = $this->repositoryProvider
                ->getTotalizationRepository()
                ->search(
                    new HospitalId($inputData->user->hospitalId),
                    $inputData->search
                );

            $this->presenterProvider
                ->getPickingListPresenter()
                ->output(new PickingListOutputData($totalRequestItems, $count));
        }
    }
}

/***
 * INPUT
 */

namespace JoyPla\Application\InputPorts\Web\ItemRequest {
    use Auth;
    use stdClass;

    /**
     * Class PickingListInputData
     * @package JoyPla\Application\InputPorts\Web\ItemRequest
     */
    class PickingListInputData
    {
        public Auth $user;
        public stdClass $search;

        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->itemName = $search['itemName'];
            $this->search->makerName = $search['makerName'];
            $this->search->itemCode = $search['itemCode'];
            $this->search->itemStandard = $search['itemStandard'];
            $this->search->itemJANCode = $search['itemJANCode'];
            $this->search->sourceDivisionIds = $search['sourceDivisionIds'];
            $this->search->targetDivisionIds = $search['targetDivisionIds'];
            $this->search->perPage = $search['perPage'];
            $this->search->currentPage = $search['currentPage'];
        }
    }

    /**
     * Interface PickingListInputPortInterface
     * @package JoyPla\Application\InputPorts\Web\ItemRequest
     */
    interface PickingListInputPortInterface
    {
        /**
         * @param PickingListInputData $inputData
         */
        function handle(PickingListInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Web\ItemRequest {
    use Collection;
    use JoyPla\Enterprise\Models\TotalRequestItem;

    /**
     * Class PickingListOutputData
     * @package JoyPla\Application\OutputPorts\Web\ItemRequest;
     */
    class PickingListOutputData
    {
        public array $totalRequestItems;
        public int $count;

        public function __construct(array $totalRequestItems, int $count)
        {
            $this->totalRequestItems = array_map(function (
                TotalRequestItem $totalRequestItem
            ) {
                return $totalRequestItem->toArray();
            },
            $totalRequestItems);
            $this->count = $count;
        }
    }

    /**
     * Interface PickingListOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Web\ItemRequest;
     */
    interface PickingListOutputPortInterface
    {
        /**
         * @param PickingListOutputData $outputData
         */
        function output(PickingListOutputData $outputData);
    }
}
