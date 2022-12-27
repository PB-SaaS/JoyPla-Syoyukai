<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\ItemRequest {

    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestTotalizationInputData;
    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestTotalizationInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestTotalizationOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestTotalizationOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRequestRepositoryInterface;

    /**
     * Class ItemRequestTotalizationInteractor
     * @package JoyPla\Application\Interactors\Api\ItemRequest
     */
    class ItemRequestTotalizationInteractor implements ItemRequestTotalizationInputPortInterface
    {
        /** @var ItemRequestTotalizationOutputPortInterface */
        private ItemRequestTotalizationOutputPortInterface $outputPort;

        /** @var itemRequestRepositoryInterface */
        private ItemRequestRepositoryInterface $repository;

        /**
         * ItemRequestTotalizationInteractor constructor.
         * @param ItemRequestTotalizationOutputPortInterface $outputPort
         */
        public function __construct(ItemRequestTotalizationOutputPortInterface $outputPort, ItemRequestRepositoryInterface $repository)
        {
            $this->outputPort = $outputPort;
            $this->repository = $repository;
        }

        /**
         * @param ItemRequestTotalizationInputData $inputData
         */
        public function handle(ItemRequestTotalizationInputData $inputData)
        {
            return;
            [$itemRequests, $count] = $this->repository->search(
                (new HospitalId($inputData->user->hospitalId)),
                $inputData->search
            );
            $this->outputPort->output(new ItemRequestTotalizationOutputData($itemRequests, $count));
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
     * Class ItemRequestTotalizationInputData
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    class ItemRequestTotalizationInputData
    {
        /**
         * ItemRequestTotalizationInputData constructor.
         */
        public function __construct(Auth $user, array $search)
        {
            $this->user = $user;
            $this->search = new stdClass();
            $this->search->registrationDate = $search['registrationDate'];
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
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    interface ItemRequestTotalizationInputPortInterface
    {
        /**
         * @param ItemRequestTotalizationInputData $inputData
         */
        function handle(ItemRequestTotalizationInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\ItemRequest {

    use Collection;
    use JoyPla\Enterprise\Models\ItemRequest;

    /**
     * Class ItemRequestTotalizationOutputData
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    class ItemRequestTotalizationOutputData
    {
        /**
         * ItemRequestTotalizationOutputData constructor.
         */
        public function __construct(array $itemRequests, int $count)
        {
            $this->itemRequests = array_map(function (ItemRequest $itemRequest) {
                return $itemRequest->toArray();
            }, $itemRequests);
            $this->count = $count;
        }
    }

    /**
     * Interface ItemRequestTotalizationOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    interface ItemRequestTotalizationOutputPortInterface
    {
        /**
         * @param ItemRequestTotalizationOutputData $outputData
         */
        function output(ItemRequestTotalizationOutputData $outputData);
    }
}
