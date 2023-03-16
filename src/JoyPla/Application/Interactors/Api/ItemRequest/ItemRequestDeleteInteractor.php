<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\ItemRequest {
    use ApiErrorCode\AccessFrequencyLimitExceededScopeIs;
    use App\SpiralDb\StockView;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestDeleteInputData;
    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestDeleteInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestDeleteOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestDeleteOutputPortInterface;
    use JoyPla\Enterprise\Models\ItemRequest;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\RequestHId;
    use JoyPla\Enterprise\Models\RequestItemCount;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRequestRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\RequestItemCountRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ItemRequestDeleteInteractor
     * @package JoyPla\Application\Interactors\Api\ItemRequest
     */
    class ItemRequestDeleteInteractor implements
        ItemRequestDeleteInputPortInterface
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
         * @param ItemRequestDeleteInputData $inputData
         */
        public function handle(ItemRequestDeleteInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $requestHId = new RequestHId($inputData->requestHId);

            $itemRequest = $this->repository->show($hospitalId, $requestHId);

            if ($itemRequest === null) {
                throw new NotFoundException('Not Found.', 404);
            }

            if (
                $inputData->isOnlyMyDivision &&
                !$itemRequest
                    ->getSourceDivision()
                    ->getDivisionId()
                    ->equal($inputData->user->divisionId)
            ) {
                throw new NotFoundException('Not Found.', 404);
            }

            $stockViewInstance = StockView::where(
                'hospitalId',
                $hospitalId->value()
            );
            $stockViewInstance->orWhere(
                'divisionId',
                $itemRequest
                    ->getTargetDivision()
                    ->getDivisionId()
                    ->value()
            );
            foreach ($itemRequest->getRequestItems() as $requestItem) {
                $stockViewInstance->orWhere(
                    'inHospitalItemId',
                    $requestItem->getInHospitalItemId()->value()
                );
            }

            $stocks = $stockViewInstance->get();
            if ((int) $stocks->count === 0) {
                throw new Exception("Stocks don't exist.", 998);
            }

            $stocks = $stocks->data->all();
            $requestItemCounts = [];
            foreach ($itemRequest->getRequestItems() as $item) {
                foreach ($stocks as $stock) {
                    if (
                        $itemRequest
                            ->getTargetDivision()
                            ->getDivisionId()
                            ->value() === $stock->divisionId &&
                        $item->getInHospitalItemId()->value() ===
                            $stock->inHospitalItemId
                    ) {
                        $requestItemCounts[] = new RequestItemCount(
                            $stock->recordId,
                            $hospitalId,
                            $item->getInHospitalItemId(),
                            $item->getItem()->getItemId(),
                            ((int) $item->getRequestQuantity()->value()) * -1,
                            $itemRequest->getSourceDivision()->getDivisionId(),
                            $itemRequest->getTargetDivision()->getDivisionId()
                        );
                    }
                }
            }

            if (
                count($requestItemCounts) !==
                count($itemRequest->getRequestItems())
            ) {
                throw new Exception("Stocks don't exist.", 998);
            }

            $this->repositoryProvider
                ->getRequestItemCountRepository()
                ->saveToArray($requestItemCounts);

            $deleteCount = $this->repository->delete($hospitalId, $requestHId);

            $this->presenterProvider
                ->getItemRequestDeletePresenter()
                ->output(new ItemRequestDeleteOutputData($deleteCount));
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
     * Class ItemRequestDeleteInputData
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    class ItemRequestDeleteInputData
    {
        public Auth $user;
        public string $requestHId;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            string $requestHId,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->requestHId = $requestHId;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface ItemRequestDeleteInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    interface ItemRequestDeleteInputPortInterface
    {
        /**
         * @param ItemRequestDeleteInputData $inputData
         */
        function handle(ItemRequestDeleteInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\ItemRequest {
    /**
     * Class ItemRequestDeleteOutputData
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    class ItemRequestDeleteOutputData
    {
        public int $count;

        public function __construct(int $deleteCount)
        {
            $this->count = $deleteCount;
        }
    }

    /**
     * Interface ItemRequestDeleteOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    interface ItemRequestDeleteOutputPortInterface
    {
        /**
         * @param ItemRequestDeleteOutputData $outputData
         */
        function output(ItemRequestDeleteOutputData $outputData);
    }
}
