<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\ItemRequest {
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestUpdateInputData;
    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestUpdateInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestUpdateOutputData;
    use JoyPla\Enterprise\Models\ItemRequest;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\RequestHId;
    use JoyPla\Enterprise\Models\RequestQuantity;
    use JoyPla\Enterprise\Models\RequestType;
    use JoyPla\Enterprise\Models\RequestItem;
    use JoyPla\Enterprise\Models\RequestItemCount;
    use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ItemRequestUpdateInteractor
     * @package JoyPla\Application\Interactors\Api\ItemRequest
     */
    class ItemRequestUpdateInteractor implements
        ItemRequestUpdateInputPortInterface
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
         * @param ItemRequestUpdateInputData $inputData
         */
        public function handle(ItemRequestUpdateInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $requestHId = new RequestHId($inputData->requestHId);
            $requestType = new RequestType((int) $inputData->requestType);

            $itemRequest = $this->repositoryProvider
                ->getItemRequestRepository()
                ->show($hospitalId, $requestHId);

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

            $requestItems = $itemRequest->getRequestItems();
            $oldRequestItems = $itemRequest->getRequestItems();

            foreach ($requestItems as $key => $item) {
                $fkey = array_search(
                    $item->getRequestId()->value(),
                    array_column($inputData->updateModel, 'requestId')
                );
                if ($fkey === null) {
                    continue;
                }
                $requestQuantity =
                    $inputData->updateModel[$fkey]['requestQuantity'];
                $item = $item->setRequestType($requestType);
                $requestItems[$key] = $item->setRequestQuantity(
                    new RequestQuantity((int) $requestQuantity)
                );
            }

            $itemRequest = $itemRequest->setRequestItem($requestItems);
            $itemRequest = $itemRequest->setRequestType($requestType);

            $stockViewInstance = ModelRepository::getStockViewInstance()->where(
                'hospitalId',
                $hospitalId->value()
            );
            $stockViewInstance->Where(
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
            if ((int) $stocks->count() === 0) {
                throw new Exception("Stocks don't exist.", 998);
            }

            $oldRequestItemsToArray = array_map(function (RequestItem $v) {
                return $v->toArray();
            }, $oldRequestItems);

            $stocks = $stocks->all();
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
                        $oldValueKey = array_search(
                            $item->getRequestId()->value(),
                            array_column($oldRequestItemsToArray, 'requestId')
                        );
                        $quantity = 0;
                        $updateQuantity = (int) $item
                            ->getRequestQuantity()
                            ->value();
                        $oldQuantity =
                            (int) $oldRequestItemsToArray[$oldValueKey][
                                'requestQuantity'
                            ];

                        if ($updateQuantity > $oldQuantity) {
                            $quantity = $updateQuantity - $oldQuantity;
                        }
                        if ($updateQuantity < $oldQuantity) {
                            $quantity = ($oldQuantity - $updateQuantity) * -1;
                        }
                        $requestItemCounts[] = new RequestItemCount(
                            $stock->recordId,
                            $hospitalId,
                            $item->getInHospitalItemId(),
                            $item->getItem()->getItemId(),
                            $quantity,
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

            $this->repositoryProvider
                ->getItemRequestRepository()
                ->update($hospitalId, $itemRequest);

            $this->presenterProvider
                ->getItemRequestUpdatePresenter()
                ->output(new ItemRequestUpdateOutputData($itemRequest));
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
     * Class ItemRequestUpdateInputData
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    class ItemRequestUpdateInputData
    {
        public Auth $user;
        public string $requestHId;
        public string $requestType;
        public array $updateModel;
        public int $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            array $itemRequest,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->requestHId = $itemRequest['requestHId'];
            $this->requestType = $itemRequest['requestType'];
            $this->updateModel = array_map(function (array $model) {
                return [
                    'requestId' => $model['requestId'],
                    'requestQuantity' => $model['requestQuantity'],
                ];
            }, $itemRequest['updateModel']);

            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface ItemRequestUpdateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    interface ItemRequestUpdateInputPortInterface
    {
        /**
         * @param ItemRequestUpdateInputData $inputData
         */
        function handle(ItemRequestUpdateInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\ItemRequest {
    use JoyPla\Enterprise\Models\ItemRequest;

    /**
     * Class ItemRequestUpdateOutputData
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    class ItemRequestUpdateOutputData
    {
        public array $data;
        public int $count;

        public function __construct(ItemRequest $itemRequest)
        {
            $this->data = $itemRequest->toArray();
            $this->count = count($itemRequest->toArray());
        }
    }

    /**
     * Interface ItemRequestUpdateOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    interface ItemRequestUpdateOutputPortInterface
    {
        /**
         * @param ItemRequestUpdateOutputData $outputData
         */
        function output(ItemRequestUpdateOutputData $outputData);
    }
}
