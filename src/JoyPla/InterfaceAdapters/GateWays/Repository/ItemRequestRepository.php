<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use Auth;
use JoyPla\Enterprise\Models\ItemRequest;
use JoyPla\Enterprise\Models\RequestItem;
use JoyPla\Enterprise\Models\RequestId;
use JoyPla\Enterprise\Models\RequestHId;
use JoyPla\Enterprise\Models\RequestQuantity;
use JoyPla\Enterprise\Models\RequestType;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Item;
use JoyPla\Enterprise\Models\Price;
use JoyPla\Enterprise\Models\Quantity;
use JoyPla\Enterprise\Models\UnitPrice;
use JoyPla\Enterprise\Models\DateYearMonth;
use Collection;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;

class ItemRequestRepository implements ItemRequestRepositoryInterface
{
    public function findByInHospitalItem(
        HospitalId $hospitalId,
        array $requestItems
    ) {
        $payoutUnitPriceUseFlag = ModelRepository::getHospitalInstance()
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->first();
        //$division = SpiralDbDivision::where('hospitalId',$hospitalId->value());

        $division = ModelRepository::getDivisionInstance();

        $division->where('hospitalId', $hospitalId->value());

        foreach ($requestItems as $item) {
            $division->orWhere('divisionId', $item->sourceDivisionId);
            $division->orWhere('divisionId', $item->targetDivisionId);
        }

        $division = $division->get();
        $division = $division->all();

        $inHospitalItem = ModelRepository::getInHospitalItemViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('notUsedFlag', '1', '!=');

        foreach ($requestItems as $item) {
            $inHospitalItem->orWhere(
                'inHospitalItemId',
                $item->inHospitalItemId
            );
        }
        $inHospitalItem = $inHospitalItem->get()->all();

        $result = [];
        foreach ($requestItems as $item) {
            if ((int) $item->requestQuantity < 1) {
                continue;
            }

            $source_division_find_key = array_search(
                $item->sourceDivisionId,
                collect_column($division, 'divisionId')
            );
            $target_division_find_key = array_search(
                $item->targetDivisionId,
                collect_column($division, 'divisionId')
            );
            $inHospitalItem_find_key = array_search(
                $item->inHospitalItemId,
                collect_column($inHospitalItem, 'inHospitalItemId')
            );

            if (
                $source_division_find_key !== false &&
                $target_division_find_key !== false &&
                $inHospitalItem_find_key !== false
            ) {
                $unitprice = 0;
                if (
                    is_numeric(
                        $inHospitalItem[$inHospitalItem_find_key]->unitPrice
                    )
                ) {
                    $unitprice =
                        (float) $inHospitalItem[$inHospitalItem_find_key]
                            ->unitPrice;
                }

                if ($payoutUnitPriceUseFlag->payoutUnitPrice !== '1') {
                    if (
                        $inHospitalItem[$inHospitalItem_find_key]->quantity !=
                            0 &&
                        $inHospitalItem[$inHospitalItem_find_key]->price != 0
                    ) {
                        $unitprice =
                            (int) $inHospitalItem[$inHospitalItem_find_key]
                                ->price /
                            (int) $inHospitalItem[$inHospitalItem_find_key]
                                ->quantity;
                    } else {
                        $unitprice = 0;
                    }
                }

                $result[] = new RequestItem(
                    RequestId::generate(),
                    new RequestHId(''),
                    new InHospitalItemId(
                        $inHospitalItem[
                            $inHospitalItem_find_key
                        ]->inHospitalItemId
                    ),
                    Item::create($inHospitalItem[$inHospitalItem_find_key]),
                    $hospitalId,
                    Division::create($division[$source_division_find_key]),
                    Division::create($division[$target_division_find_key]),
                    new RequestQuantity((int) $item->requestQuantity),
                    new RequestType((int) $item->requestType),
                    Quantity::create($inHospitalItem[$inHospitalItem_find_key]),
                    new Price(
                        (float) $inHospitalItem[$inHospitalItem_find_key]->price
                    ),
                    new UnitPrice($unitprice)
                );
            }
        }
        return $result;
    }

    public function saveToArray(array $itemRequests)
    {
        $itemRequests = array_map(function (ItemRequest $itemRequest) {
            return $itemRequest;
        }, $itemRequests);

        $histories = [];
        $items = [];

        foreach ($itemRequests as $itemRequest) {
            $itemRequestToArray = $itemRequest->toArray();

            $histories[] = [
                'requestHId' => (string) $itemRequestToArray['requestHId'],
                'hospitalId' =>
                    (string) $itemRequestToArray['hospital']['hospitalId'],
                'sourceDivisionId' =>
                    (string) $itemRequestToArray['sourceDivision'][
                        'divisionId'
                    ],
                'sourceDivision' =>
                    (string) $itemRequestToArray['sourceDivision'][
                        'divisionName'
                    ],
                'targetDivisionId' =>
                    (string) $itemRequestToArray['targetDivision'][
                        'divisionId'
                    ],
                'targetDivision' =>
                    (string) $itemRequestToArray['targetDivision'][
                        'divisionName'
                    ],
                'requestType' => (string) $itemRequestToArray['requestType'],
                'totalAmount' => (string) $itemRequestToArray['totalAmount'],
                'itemsNumber' => (string) $itemRequestToArray['itemCount'],
                'requestUserName' =>
                    (string) $itemRequestToArray['requestUserName'],
            ];

            foreach ($itemRequestToArray['requestItems'] as $requestItem) {
                $items[] = [
                    'requestId' => (string) $requestItem['requestId'],
                    'requestHId' => (string) $itemRequestToArray['requestHId'],
                    'hospitalId' => (string) $requestItem['hospitalId'],
                    'itemId' => (string) $requestItem['item']['itemId'],
                    'inHospitalItemId' =>
                        (string) $requestItem['inHospitalItemId'],
                    'sourceDivisionId' =>
                        (string) $requestItem['sourceDivision']['divisionId'],
                    'targetDivisionId' =>
                        (string) $requestItem['targetDivision']['divisionId'],
                    'requestQuantity' =>
                        (string) $requestItem['requestQuantity'],
                    'requestType' => (string) $requestItem['requestType'],
                    'quantity' =>
                        (string) $requestItem['quantity']['quantityNum'],
                    'quantityUnit' =>
                        (string) $requestItem['quantity']['quantityUnit'],
                    'itemUnit' => (string) $requestItem['quantity']['itemUnit'],
                    'price' => (string) $requestItem['price'],
                    'unitPrice' => (string) $requestItem['unitPrice'],
                ];
            }
        }

        ModelRepository::getItemRequestInstance()->insert($histories);
        ModelRepository::getItemRequestItemInstance()->insert($items);

        return $itemRequests;
    }

    public function sendRegistrationMail(array $itemRequests, Auth $user)
    {
        $itemRequests = array_map(function (ItemRequest $itemRequest) {
            return $itemRequest;
        }, $itemRequests);

        $registrationMailViewModel = [];

        foreach ($itemRequests as $itemRequest) {
            $itemRequestToArray = $itemRequest->toArray();
            $registrationMailViewModel[] = [
                'requestHId' => $itemRequestToArray['requestHId'],
                'sourceDivisionName' =>
                    $itemRequestToArray['sourceDivision']['divisionName'],
                'targetDivisionName' =>
                    $itemRequestToArray['targetDivision']['divisionName'],
                'itemCount' => number_format_jp(
                    $itemRequestToArray['itemCount']
                ),
            ];
        }

        $mailBody = view(
            'mail/ItemRequest/Register',
            [
                'name' => '%val:usr:name%',
                'hospitalName' => $itemRequests[0]
                    ->getHospital()
                    ->getHospitalName()
                    ->value(),
                'requestUserName' => $user->name,
                'histories' => $registrationMailViewModel,
                'url' => config('url.hospital', ''),
            ],
            false
        )->render();

        $ids = ModelRepository::getHospitalUserInstance()
            ->where('hospitalId', $user->hospitalId)
            ->whereIn('userPermission', [1, 3])
            ->get();

        $mailId = ModelRepository::getHospitalUserMailInstance()
            ->subject('[JoyPla] 請求書が作成されました')
            ->standby(false)
            ->reserveDate('now')
            ->bodyText($mailBody)
            ->formAddress(FROM_ADDRESS)
            ->formName(FROM_NAME)
            ->mailField('mailAddress')
            ->regist();

        $ids = array_values(array_column($ids->toArray(), 'id'));

        ModelRepository::getHospitalUserMailInstance()
            ->ruleId($mailId)
            ->sampling($ids);
    }

    public function search(HospitalId $hospitalId, object $search)
    {
        $itemSearchFlag = false;
        $itemViewInstance = ModelRepository::getItemRequestItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        $historyViewInstance = ModelRepository::getItemRequestViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        if ($search->itemName) {
            $itemViewInstance->orWhere(
                'itemName',
                '%' . $search->itemName . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->makerName) {
            $itemViewInstance->orWhere(
                'makerName',
                '%' . $search->makerName . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemCode) {
            $itemViewInstance->orWhere(
                'itemCode',
                '%' . $search->itemCode . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemStandard) {
            $itemViewInstance->orWhere(
                'itemStandard',
                '%' . $search->itemStandard . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }
        if ($search->itemJANCode) {
            $itemViewInstance->orWhere(
                'itemJANCode',
                '%' . $search->itemJANCode . '%',
                'LIKE'
            );
            $itemSearchFlag = true;
        }

        $requestIds = [];
        if ($itemSearchFlag) {
            $itemViewInstance = $itemViewInstance->get();
            if ((int) $itemViewInstance->count() === 0) {
                return [[], 0];
            }
            foreach ($itemViewInstance->all() as $item) {
                $historyViewInstance = $historyViewInstance->orWhere(
                    'requestHId',
                    $item->requestHId
                );
                $requestIds[] = $item->requestId;
            }
        }

        if (
            is_array($search->sourceDivisionIds) &&
            count($search->sourceDivisionIds) > 0
        ) {
            foreach ($search->sourceDivisionIds as $sourceDivisionId) {
                $historyViewInstance->orWhere(
                    'sourceDivisionId',
                    $sourceDivisionId
                );
            }
        }

        if (
            is_array($search->targetDivisionIds) &&
            count($search->targetDivisionIds) > 0
        ) {
            foreach ($search->targetDivisionIds as $targetDivisionId) {
                $historyViewInstance->orWhere(
                    'targetDivisionId',
                    $targetDivisionId
                );
            }
        }

        if (is_array($search->requestType) && count($search->requestType) > 0) {
            foreach ($search->requestType as $value) {
                $historyViewInstance->orWhere('requestType', $value);
            }
        }

        if ($search->registrationDate) {
            $registrationDate = new DateYearMonth($search->registrationDate);
            $nextMonth = $registrationDate->nextMonth();

            $historyViewInstance->where(
                'registrationTime',
                $registrationDate->format('Y-m-01'),
                '>='
            );
            $historyViewInstance->where(
                'registrationTime',
                $nextMonth->format('Y-m-01'),
                '<'
            );
        }

        $histories = $historyViewInstance
            ->orderBy('id', 'desc')
            ->page($search->currentPage)
            ->paginate($search->perPage);
        if ((int) $histories->getData()->count() === 0) {
            return [[], 0];
        }

        $itemViewInstance = ModelRepository::getItemRequestItemViewInstance()->where(
            'hospitalId',
            $hospitalId->value()
        );

        foreach ($histories->getData()->all() as $history) {
            $itemViewInstance = $itemViewInstance->orWhere(
                'requestHId',
                $history->requestHId
            );
        }
        foreach ($requestIds as $requestId) {
            $itemViewInstance = $itemViewInstance->orWhere(
                'requestId',
                $requestId
            );
        }

        $items = $itemViewInstance->get();
        $itemRequests = [];
        foreach ($histories->getData()->all() as $history) {
            $sourceDivision = new Collection();
            $sourceDivision->hospitalId = $hospitalId->value();
            $sourceDivision->divisionId = $history->sourceDivisionId;
            $sourceDivision->divisionName = htmlspecialchars_decode(
                $history->sourceDivision,
                ENT_QUOTES
            );
            $targetDivision = new Collection();
            $targetDivision->hospitalId = $hospitalId->value();
            $targetDivision->divisionId = $history->targetDivisionId;
            $targetDivision->divisionName = htmlspecialchars_decode(
                $history->targetDivision,
                ENT_QUOTES
            );
            $history->set('sourceDivision', $sourceDivision);
            $history->set('targetDivision', $targetDivision);
            $history->set('requestType', (int) $history->requestType);
            $history->set(
                'requestUserName',
                htmlspecialchars_decode($history->requestUserName, ENT_QUOTES)
            );

            $itemRequest = ItemRequest::create($history);

            foreach ($items->all() as $item) {
                if ($itemRequest->getRequestHId()->equal($item->requestHId)) {
                    $item->set('sourceDivision', $sourceDivision);
                    $item->set('targetDivision', $targetDivision);
                    $item->set('requestType', (int) $item->requestType);
                    $itemRequest = $itemRequest->addRequestItem(
                        RequestItem::create($item)
                    );
                }
            }

            $itemRequests[] = $itemRequest;
        }

        return [$itemRequests, $histories->getData()->count()];
    }

    public function show(HospitalId $hospitalId, RequestHId $requestHId)
    {
        $historyViewInstance = ModelRepository::getItemRequestViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('requestHId', $requestHId->value())
            ->get();
        if ((int) $historyViewInstance->count() <= 0) {
            return null;
        }
        $itemViewInstance = ModelRepository::getItemRequestItemViewInstance()
            ->orderBy('id', 'asc')
            ->where('hospitalId', $hospitalId->value())
            ->where('requestHId', $requestHId->value())
            ->get();
        if ((int) $itemViewInstance->count() <= 0) {
            return null;
        }

        $history = $historyViewInstance->first();

        $sourceDivision = new Collection();
        $sourceDivision->hospitalId = $hospitalId->value();
        $sourceDivision->divisionId = $history->sourceDivisionId;
        $sourceDivision->divisionName = htmlspecialchars_decode(
            $history->sourceDivision,
            ENT_QUOTES
        );
        $targetDivision = new Collection();
        $targetDivision->hospitalId = $hospitalId->value();
        $targetDivision->divisionId = $history->targetDivisionId;
        $targetDivision->divisionName = htmlspecialchars_decode(
            $history->targetDivision,
            ENT_QUOTES
        );
        $history->set('sourceDivision', $sourceDivision);
        $history->set('targetDivision', $targetDivision);
        $history->set('requestType', (int) $history->requestType);
        $history->set(
            'requestUserName',
            htmlspecialchars_decode($history->requestUserName, ENT_QUOTES)
        );

        $itemRequest = ItemRequest::create($history);

        foreach ($itemViewInstance->all() as $item) {
            $item->set('sourceDivision', $sourceDivision);
            $item->set('targetDivision', $targetDivision);
            $item->set('requestType', (int) $item->requestType);
            $itemRequest = $itemRequest->addRequestItem(
                RequestItem::create($item)
            );
        }

        return $itemRequest;
    }

    public function delete(HospitalId $hospitalId, RequestHId $requestHId)
    {
        $result = ModelRepository::getItemRequestInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('requestHId', $requestHId->value())
            ->delete();
        return $result;
    }

    public function deleteItem(
        HospitalId $hospitalId,
        RequestId $requestId,
        ItemRequest $itemRequest
    ) {
        $itemRequestToArray = $itemRequest->toArray();
        if (count($itemRequestToArray['requestItems']) === 0) {
            ModelRepository::getItemRequestInstance()
                ->where('hospitalId', $hospitalId->value())
                ->where('requestHId', $itemRequest->getRequestHId()->value())
                ->delete();
            return true;
        }

        $history = [];
        $history[] = [
            'requestHId' => (string) $itemRequestToArray['requestHId'],
            'totalAmount' => (string) $itemRequestToArray['totalAmount'],
            'itemsNumber' => (string) $itemRequestToArray['itemCount'],
        ];

        ModelRepository::getItemRequestItemInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('requestId', $requestId->value())
            ->delete();
        ModelRepository::getItemRequestInstance()->upsertBulk(
            'requestHId',
            $history
        );

        return false;
    }

    public function update(HospitalId $hospitalId, ItemRequest $itemRequest)
    {
        $itemRequestToArray = $itemRequest->toArray();

        $history = [];
        $history[] = [
            'requestHId' => (string) $itemRequestToArray['requestHId'],
            'totalAmount' => (string) $itemRequestToArray['totalAmount'],
            'itemsNumber' => (string) $itemRequestToArray['itemCount'],
            'requestType' => (string) $itemRequestToArray['requestType'],
        ];

        $items = [];
        foreach ($itemRequestToArray['requestItems'] as $requestItem) {
            $items[] = [
                'requestId' => (string) $requestItem['requestId'],
                'requestQuantity' => (string) $requestItem['requestQuantity'],
                'requestType' => (string) $requestItem['requestType'],
            ];
        }
        ModelRepository::getItemRequestInstance()->upsertBulk(
            'requestHId',
            $history
        );
        ModelRepository::getItemRequestItemInstance()->upsertBulk(
            'requestId',
            $items
        );
    }
}

interface ItemRequestRepositoryInterface
{
    public function findByInHospitalItem(
        HospitalId $hospitalId,
        array $itemRequests
    );
    public function saveToArray(array $itemRequests);
    public function sendRegistrationMail(array $itemRequests, Auth $user);
    public function search(HospitalId $hospitalId, object $search);
    public function show(HospitalId $hospitalId, RequestHId $requestHId);
    public function delete(HospitalId $hospitalId, RequestHId $requestHId);
    public function deleteItem(
        HospitalId $hospitalId,
        RequestId $requestId,
        ItemRequest $itemRequest
    );
    public function update(HospitalId $hospitalId, ItemRequest $itemRequest);
}
