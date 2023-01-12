<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\ItemRequest {

    use JoyPla\Application\InputPorts\Api\ItemRequest\TotalizationInputData;
    use JoyPla\Application\InputPorts\Api\ItemRequest\TotalizationInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\TotalizationOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\TotalizationOutputPortInterface;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\TotalRequestItem;
    use JoyPla\InterfaceAdapters\GateWays\Repository\TotalizationRepositoryInterface;

    /**
     * Class TotalizationInteractor
     * @package JoyPla\Application\Interactors\Api\ItemRequest
     */
    class TotalizationInteractor implements TotalizationInputPortInterface
    {
        /** @var TotalizationOutputPortInterface */
        private TotalizationOutputPortInterface $outputPort;

        /** @var TotalizationRepositoryInterface */
        private TotalizationRepositoryInterface $repository;

        /**
         * TotalizationInteractor constructor.
         * @param TotalizationOutputPortInterface $outputPort
         */
        public function __construct(
            TotalizationOutputPortInterface $outputPort,
            TotalizationRepositoryInterface $repository
        ) {
            $this->outputPort = $outputPort;
            $this->repository = $repository;
        }

        /**
         * @param TotalizationInputData $inputData
         */
        public function handle(TotalizationInputData $inputData)
        {

            [$totalRequestItems, $count] = $this->repository->search(
                (new HospitalId($inputData->user->hospitalId)),
                $inputData->search
            );
            $this->outputPort->output(new TotalizationOutputData($totalRequestItems, $count));
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
     * Class TotalizationInputData
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    class TotalizationInputData
    {
        /**
         * TotalizationInputData constructor.
         */
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
     * Interface TotalizationInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    interface TotalizationInputPortInterface
    {
        /**
         * @param TotalizationInputData $inputData
         */
        function handle(TotalizationInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\ItemRequest {

    use Collection;
    use JoyPla\Enterprise\Models\TotalRequestItem;

    /**
     * Class TotalizationOutputData
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    class TotalizationOutputData
    {
        /**
         * TotalizationOutputData constructor.
         */
        public function __construct(array $totalRequestItems, int $count)
        {
            $this->totalRequestItems = array_map(function (TotalRequestItem $totalRequestItem) {
                return $totalRequestItem->toArray();
            }, $totalRequestItems);
            $this->count = $count;
        }
    }

    /**
     * Interface TotalizationOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    interface TotalizationOutputPortInterface
    {
        /**
         * @param TotalizationOutputData $outputData
         */
        function output(TotalizationOutputData $outputData);
    }
}
