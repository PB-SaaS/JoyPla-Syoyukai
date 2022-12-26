<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\ItemRequest {

    use ApiErrorCode\AccessFrequencyLimitExceededScopeIs;
    use App\SpiralDb\StockView;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\ItemRequest\RequestItemDeleteInputData;
    use JoyPla\Application\InputPorts\Api\ItemRequest\RequestItemDeleteInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\RequestItemDeleteOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\RequestItemDeleteOutputPortInterface;
    use JoyPla\Enterprise\Models\ItemRequest;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\RequestHId;
    use JoyPla\Enterprise\Models\RequestId;
    use JoyPla\Enterprise\Models\RequestItemCount;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRequestRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\RequestItemCountRepositoryInterface;

    /**
     * Class RequestItemDeleteInteractor
     * @package JoyPla\Application\Interactors\Api\ItemRequest
     */
    class RequestItemDeleteInteractor implements RequestItemDeleteInputPortInterface
    {
        /** @var RequestItemDeleteOutputPortInterface */
        private RequestItemDeleteOutputPortInterface $outputPort;

        /** @var ItemRequestRepositoryInterface */
        private ItemRequestRepositoryInterface $repository;

        /** @var RequestItemCountRepositoryInterface */
        private RequestItemCountRepositoryInterface $requestItemCountRepository;

        /**
         * RequestItemDeleteInteractor constructor.
         * @param RequestItemDeleteOutputPortInterface $outputPort
         */
        public function __construct(
            RequestItemDeleteOutputPortInterface $outputPort,
            ItemRequestRepositoryInterface $repository,
            RequestItemCountRepositoryInterface $requestItemCountRepository
        ) {
            $this->outputPort = $outputPort;
            $this->repository = $repository;
            $this->requestItemCountRepository = $requestItemCountRepository;
        }

        /**
         * @param RequestItemDeleteInputData $inputData
         */
        public function handle(RequestItemDeleteInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $requestHId = new RequestHId($inputData->requestHId);
            $requestId = new RequestId($inputData->requestId);

            $itemRequest = $this->repository->show($hospitalId, $requestHId);

            if ($itemRequest === null) {
                throw new Exception("Invalid value.", 422);
            }

            if ($inputData->isOnlyMyDivision && !$itemRequest->getSourceDivision()->getDivisionId()->equal($inputData->user->divisionId)) {
                throw new NotFoundException("Not Found.", 404);
            }

            if (!$itemRequest->existRequestItem($requestId)) {
                throw new Exception("Invalid value.", 422);
            }

            $stockViewInstance = StockView::where('hospitalId', $hospitalId->value());
            $stockViewInstance->Where('divisionId', $itemRequest->getTargetDivision()->getDivisionId()->value());
            foreach ($itemRequest->getRequestItems() as $requestItem) {
                if ($requestItem->getRequestId()->equal($requestId->value())) {
                    $stockViewInstance->Where('inHospitalItemId', $requestItem->getInHospitalItemId()->value());
                }
            }

            $stocks = $stockViewInstance->get();
            if ((int)$stocks->count === 0) {
                throw new Exception("Stocks don't exist.", 999);
            }

            $stock = $stocks->data->get(0);
            $requestItemCounts = [];
            foreach ($itemRequest->getRequestItems() as $item) {
                if (($itemRequest->getTargetDivision()->getDivisionId()->value() === $stock->divisionId) &&
                    ($item->getInHospitalItemId()->value() === $stock->inHospitalItemId)
                ) {
                    $requestItemCounts[] = new RequestItemCount(
                        $stock->recordId,
                        $hospitalId,
                        $item->getInHospitalItemId(),
                        $item->getItem()->getItemId(),
                        ((int)$item->getRequestQuantity()->value()) * -1,
                        $itemRequest->getSourceDivision()->getDivisionId(),
                        $itemRequest->getTargetDivision()->getDivisionId()
                    );
                }
            }

            $this->requestItemCountRepository->saveToArray($requestItemCounts);

            $itemRequest = $itemRequest->deleteItem($requestId);
            $isItemRequestDeleted = $this->repository->deleteItem($hospitalId, $requestId, $itemRequest);

            $this->outputPort->output(new RequestItemDeleteOutputData($isItemRequestDeleted));
        }
    }
}


/***
 * INPUT
 */

namespace JoyPla\Application\InputPorts\Api\ItemRequest {

    use Auth;

    /**
     * Class RequestItemDeleteInputData
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    class RequestItemDeleteInputData
    {
        /**
         * RequestItemDeleteInputData constructor.
         */
        public function __construct(Auth $user, string $requestHId, string $requestId, bool $isOnlyMyDivision)
        {
            $this->user = $user;
            $this->requestHId = $requestHId;
            $this->requestId = $requestId;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    interface RequestItemDeleteInputPortInterface
    {
        /**
         * @param RequestItemDeleteInputData $inputData
         */
        function handle(RequestItemDeleteInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\ItemRequest {

    /**
     * Class RequestItemDeleteOutputData
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    class RequestItemDeleteOutputData
    {
        /** @var bool */

        /**
         * RequestItemDeleteOutputData constructor.
         */

        public function __construct(bool $isItemRequestDeleted)
        {
            $this->data = [
                'isItemRequestDeleted' => $isItemRequestDeleted,
            ];
        }
    }

    /**
     * Interface RequestItemDeleteOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    interface RequestItemDeleteOutputPortInterface
    {
        /**
         * @param RequestItemDeleteOutputData $outputData
         */
        function output(RequestItemDeleteOutputData $outputData);
    }
}
