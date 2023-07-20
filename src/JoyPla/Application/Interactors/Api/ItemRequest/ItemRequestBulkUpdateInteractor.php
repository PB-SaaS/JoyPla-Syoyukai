<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\ItemRequest {
    use ApiResponse;
    use Exception;
    use framework\Exception\NotFoundException;
    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestBulkUpdateInputData;
    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestBulkUpdateInputPortInterface;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestBulkUpdateOutputData;
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
     * Class ItemRequestBulkUpdateInteractor
     * @package JoyPla\Application\Interactors\Api\ItemRequest
     */
    class ItemRequestBulkUpdateInteractor implements
        ItemRequestBulkUpdateInputPortInterface
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
         * @param ItemRequestBulkUpdateInputData $inputData
         */
        public function handle(ItemRequestBulkUpdateInputData $inputData)
        {
            $instance = ModelRepository::getStockItemViewInstance();
            $update = [];
            foreach ($inputData->items as $item) {
                $instance->orWhere('divisionId', $item['targetDivisionId']);
                $instance->orWhere(
                    'inHospitalItemId',
                    $item['inHospitalItemId']
                );
            }
            $stock = $instance->get();
            foreach ($inputData->items as $item) {
                $update[] = [
                    'registrationTime' => 'now',
                    'hospitalId' => $inputData->user->hospitalId,
                    'recordId' => array_find($stock, function ($s) use ($item) {
                        return $s->inHospitalItemId ===
                            $item['inHospitalItemId'] &&
                            $s->divisionId === $item['targetDivisionId'];
                    })->id,
                    'inHospitalItemId' => $item['inHospitalItemId'],
                    'itemId' => array_find($stock, function ($s) use ($item) {
                        return $s->inHospitalItemId ===
                            $item['inHospitalItemId'] &&
                            $s->divisionId === $item['targetDivisionId'];
                    })->itemId,
                    'sourceDivisionId' => $item['sourceDivisionId'],
                    'targetDivisionId' => $item['targetDivisionId'],
                    'quantity' => $item['requestQuantity'],
                ];
            }

            ModelRepository::getItemRequestItemCountTransactionInstance()->insert(
                $update
            );
            echo (new ApiResponse([], 1, 200, 'success', []))->toJson();
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
     * Class ItemRequestBulkUpdateInputData
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    class ItemRequestBulkUpdateInputData
    {
        public Auth $user;
        public array $items;

        public function __construct(Auth $user, array $items)
        {
            $this->user = $user;
            $this->items = array_map(function ($item) {
                return [
                    'sourceDivisionId' => $item['sourceDivisionId'],
                    'targetDivisionId' => $item['targetDivisionId'],
                    'inHospitalItemId' => $item['inHospitalItemId'],
                    'requestQuantity' => $item['requestQuantity'],
                ];
            }, $items);
        }
    }

    /**
     * Interface ItemRequestBulkUpdateInputPortInterface
     * @package JoyPla\Application\InputPorts\Api\ItemRequest
     */
    interface ItemRequestBulkUpdateInputPortInterface
    {
        /**
         * @param ItemRequestBulkUpdateInputData $inputData
         */
        function handle(ItemRequestBulkUpdateInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\ItemRequest {
    use JoyPla\Enterprise\Models\ItemRequest;

    /**
     * Class ItemRequestBulkUpdateOutputData
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    class ItemRequestBulkUpdateOutputData
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
     * Interface ItemRequestBulkUpdateOutputPortInterface
     * @package JoyPla\Application\OutputPorts\Api\ItemRequest;
     */
    interface ItemRequestBulkUpdateOutputPortInterface
    {
        /**
         * @param ItemRequestBulkUpdateOutputData $outputData
         */
        function output(ItemRequestBulkUpdateOutputData $outputData);
    }
}
