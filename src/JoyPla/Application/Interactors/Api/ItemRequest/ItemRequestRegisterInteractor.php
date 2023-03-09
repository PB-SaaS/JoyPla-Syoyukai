<?php

/***
 * USECASE
 */

namespace JoyPla\Application\Interactors\Api\ItemRequest {
    use App\Model\Division;
    use App\SpiralDb\StockView;
    use Exception;
    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestRegisterInputPortInterface;
    use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestRegisterInputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\ItemRequestRegisterOutputPortInterface;
    use JoyPla\Enterprise\Models\RequestHId;
    use JoyPla\Enterprise\Models\ItemRequest;
    use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
    use JoyPla\Enterprise\Models\RequestType;
    use JoyPla\Enterprise\Models\Hospital;
    use JoyPla\Enterprise\Models\HospitalId;
    use JoyPla\Enterprise\Models\HospitalName;
    use JoyPla\Enterprise\Models\RequestItemCount;
    use JoyPla\Enterprise\Models\Pref;
    use JoyPla\Enterprise\Models\TextFieldType64Bytes;
    use JoyPla\InterfaceAdapters\GateWays\Repository\ItemRequestRepositoryInterface;
    use JoyPla\InterfaceAdapters\GateWays\Repository\RequestItemCountRepositoryInterface;
    use JoyPla\Service\Presenter\Api\PresenterProvider;
    use JoyPla\Service\Repository\RepositoryProvider;

    /**
     * Class ItemRequestRegisterInteractor
     * @package JoyPla\Application\Interactors\ItemRequest\Api
     */
    class ItemRequestRegisterInteractor implements
        ItemRequestRegisterInputPortInterface
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
         * @param ItemRequestRegisterInputData $inputData
         */
        public function handle(ItemRequestRegisterInputData $inputData)
        {
            $hospitalId = new HospitalId($inputData->user->hospitalId);
            $requestType = new RequestType($inputData->requestType);

            $inputData->requestItems = array_map(function ($v) use (
                $inputData
            ) {
                if (
                    $inputData->isOnlyMyDivision &&
                    $inputData->user->divisionId !== $v->sourceDivisionId
                ) {
                    throw new Exception('Illegal request', 403);
                }
                if ($v->sourceDivisionId == $v->targetDivisionId) {
                    throw new Exception('Invalid request', 999);
                }
                return $v;
            },
            $inputData->requestItems);

            $requestItems = $this->repositoryProvider
                ->getItemRequestRepository()
                ->findByInHospitalItem($hospitalId, $inputData->requestItems);

            if (count($requestItems) === 0) {
                throw new Exception("Request items don't exist.", 999);
            }

            $ids = [];
            $result = [];

            foreach ($requestItems as $i) {
                $exist = false;
                foreach ($result as $key => $r) {
                    if (
                        $r->equalDivisions(
                            $i->getSourceDivision(),
                            $i->getTargetDivision()
                        )
                    ) {
                        $exist = true;
                        $result[$key] = $r->addRequestItem($i);
                    }
                }
                if ($exist) {
                    continue;
                }

                $id = RequestHId::generate();
                $ids[] = $id->value();
                //登録時には病院名は必要ないので、いったんhogeでいい
                $result[] = new ItemRequest(
                    $id,
                    new DateYearMonthDayHourMinutesSecond(''),
                    new DateYearMonthDayHourMinutesSecond(''),
                    [$i],
                    new Hospital(
                        $hospitalId,
                        new HospitalName('hoge'),
                        '',
                        '',
                        new Pref(''),
                        ''
                    ),
                    $i->getSourceDivision(),
                    $i->getTargetDivision(),
                    $requestType,
                    new TextFieldType64Bytes($inputData->user->name)
                );
            }

            $stockViewInstance = StockView::where(
                'hospitalId',
                $hospitalId->value()
            );
            foreach ($result as $itemRequest) {
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
            }

            $stocks = $stockViewInstance->get();
            if ((int) $stocks->count === 0) {
                throw new Exception("Stocks don't exist.", 998);
            }

            $stocks = $stocks->data->all();
            $requestItemCounts = [];
            $inHpItem = [];
            foreach ($result as $itemRequest) {
                $inHpItem = [];
                foreach ($itemRequest->getRequestItems() as $item) {
                    $inHpItem[] = $item->getInHospitalItemId()->value();
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
                                (int) $item->getRequestQuantity()->value(),
                                $itemRequest
                                    ->getSourceDivision()
                                    ->getDivisionId(),
                                $itemRequest
                                    ->getTargetDivision()
                                    ->getDivisionId()
                            );
                        }
                    }
                }
            }

            if (count($requestItemCounts) !== count(array_unique($inHpItem))) {
                throw new Exception("Stocks don't exist.", 998);
            }

            $this->repositoryProvider
                ->getRequestItemCountRepository()
                ->saveToArray($requestItemCounts);

            $this->repositoryProvider
                ->getItemRequestRepository()
                ->saveToArray($result);

            $this->repositoryProvider
                ->getItemRequestRepository()
                ->sendRegistrationMail($result, $inputData->user);

            $this->presenterProvider
                ->getItemRequestRegisterPresenter()
                ->output(new ItemRequestRegisterOutputData($ids));
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
     * Class ItemRequestRegisterInputData
     * @package JoyPla\Application\InputPorts\ItemRequest\Api
     */
    class ItemRequestRegisterInputData
    {
        public Auth $user;
        public array $requestItems;
        public int $requestType;
        public bool $isOnlyMyDivision;

        public function __construct(
            Auth $user,
            array $requestItems,
            int $requestType,
            bool $isOnlyMyDivision
        ) {
            $this->user = $user;
            $this->requestItems = array_map(function ($v) {
                $object = new stdClass();
                $object->inHospitalItemId = $v['inHospitalItemId'];
                $object->requestQuantity = $v['requestQuantity'];
                $object->sourceDivisionId = $v['sourceDivisionId'];
                $object->targetDivisionId = $v['targetDivisionId'];
                $object->requestType = $v['requestType'];
                return $object;
            }, $requestItems);

            $this->requestType = $requestType;
            $this->isOnlyMyDivision = $isOnlyMyDivision;
        }
    }

    /**
     * Interface UserCreateInputPortInterface
     * @package JoyPla\Application\InputPorts\ItemRequest\Api
     */
    interface ItemRequestRegisterInputPortInterface
    {
        /**
         * @param ItemRequestRegisterInputData $inputData
         */
        public function handle(ItemRequestRegisterInputData $inputData);
    }
}

/***
 * OUTPUT
 */

namespace JoyPla\Application\OutputPorts\Api\ItemRequest {
    /**
     * Class ItemRequestRegisterOutputData
     * @package JoyPla\Application\OutputPorts\ItemRequest\Api;
     */
    class ItemRequestRegisterOutputData
    {
        public array $ids;
        public function __construct(array $ids)
        {
            $this->ids = $ids;
        }
    }

    /**
     * Interface ItemRequestRegisterOutputPortInterface
     * @package JoyPla\Application\OutputPorts\ItemRequest\Api;
     */
    interface ItemRequestRegisterOutputPortInterface
    {
        /**
         * @param ItemRequestRegisterOutputData $outputData
         */
        public function output(ItemRequestRegisterOutputData $outputData);
    }
}
