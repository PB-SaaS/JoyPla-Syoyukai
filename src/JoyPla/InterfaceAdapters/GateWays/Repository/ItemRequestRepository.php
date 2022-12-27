<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\ItemRequest as SpiralDbItemRequest;
use App\SpiralDb\RequestItem as SpiralDbRequestItem;
use App\SpiralDb\Hospital;
use App\SpiralDb\InHospitalItemView;
use App\SpiralDb\RequestItemView;
use App\SpiralDb\ItemRequestView;

use Exception;
use Auth;
use framework\SpiralConnecter\SpiralDB;
use JoyPla\Enterprise\Models\ItemRequest;
use JoyPla\Enterprise\Models\RequestItem;
use JoyPla\Enterprise\Models\RequestId;
use JoyPla\Enterprise\Models\RequestHId;
use JoyPla\Enterprise\Models\RequestQuantity;
use JoyPla\Enterprise\Models\RequestType;
use JoyPla\Enterprise\Models\Division;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\HospitalName;
use JoyPla\Enterprise\Models\Pref;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Item;
use JoyPla\Enterprise\Models\Price;
use JoyPla\Enterprise\Models\Quantity;
use JoyPla\Enterprise\Models\UnitPrice;
use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\Hospital as hp;
use JoyPla\Enterprise\Models\TextFieldType64Bytes;
use JoyPla\Enterprise\Models\DateYearMonthDayHourMinutesSecond;
use Collection;

class ItemRequestRepository implements ItemRequestRepositoryInterface
{
    /*
    public function findByHospitalId(HospitalId $hospitalId)
    {
        $history = (SpiralDbItemRequest::where('hospitalId', $hospitalId->value())->get())->data->all();

        return $history;
    }
*/
    public function findByInHospitalItem(HospitalId $hospitalId, array $requestItems)
    {
        $payoutUnitPriceUseFlag = (Hospital::where('hospitalId', $hospitalId->value())->value('payoutUnitPrice')->get())->data->get(0);
        //$division = SpiralDbDivision::where('hospitalId',$hospitalId->value());

        $division = SpiralDB::title('NJ_divisionDB')->value([
            "registrationTime",
            "divisionId",
            "hospitalId",
            "divisionName",
            "divisionType",
            "deleteFlag",
            "authkey",
            "deliveryDestCode"
        ]);

        $division->where('hospitalId', $hospitalId->value());

        foreach ($requestItems as $item) {
            $division->orWhere('divisionId', $item->sourceDivisionId);
            $division->orWhere('divisionId', $item->targetDivisionId);
        }

        $division = $division->get();
        $division = $division->all();

        $inHospitalItem = InHospitalItemView::where('hospitalId', $hospitalId->value())->where('notUsedFlag', '1', '!=');
        foreach ($requestItems as $item) {
            $inHospitalItem->orWhere('inHospitalItemId', $item->inHospitalItemId);
        }
        $inHospitalItem = ($inHospitalItem->get())->data->all();

        $result = [];
        foreach ($requestItems as $item) {
            if ((int)$item->requestQuantity < 1) {
                continue;
            }

            $source_division_find_key = array_search($item->sourceDivisionId, collect_column($division, 'divisionId'));
            $target_division_find_key = array_search($item->targetDivisionId, collect_column($division, 'divisionId'));
            $inHospitalItem_find_key = array_search($item->inHospitalItemId, collect_column($inHospitalItem, 'inHospitalItemId'));

            if (($source_division_find_key !== false) && ($target_division_find_key !== false) && ($inHospitalItem_find_key !== false)) {

                $unitprice = 0;
                if (is_numeric($inHospitalItem[$inHospitalItem_find_key]->unitPrice)) {
                    $unitprice = (float)$inHospitalItem[$inHospitalItem_find_key]->unitPrice;
                }

                if ($payoutUnitPriceUseFlag->payoutUnitPrice !== '1') {
                    if ($inHospitalItem[$inHospitalItem_find_key]->quantity != 0 && $inHospitalItem[$inHospitalItem_find_key]->price != 0) {
                        $unitprice = ((int)$inHospitalItem[$inHospitalItem_find_key]->price / (int)$inHospitalItem[$inHospitalItem_find_key]->quantity);
                    } else {
                        $unitprice = 0;
                    }
                }

                $result[] = new RequestItem(
                    (RequestId::generate()),
                    (new RequestHId('')),
                    (new InHospitalItemId($inHospitalItem[$inHospitalItem_find_key]->inHospitalItemId)),
                    (Item::create($inHospitalItem[$inHospitalItem_find_key])),
                    $hospitalId,
                    (Division::create($division[$source_division_find_key])),
                    (Division::create($division[$target_division_find_key])),
                    (new RequestQuantity((int)$item->requestQuantity)),
                    (new RequestType((int)$item->requestType)),
                    (Quantity::create($inHospitalItem[$inHospitalItem_find_key])),
                    (new Price((float)$inHospitalItem[$inHospitalItem_find_key]->price)),
                    (new UnitPrice($unitprice))
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
                "requestHId" => (string)$itemRequestToArray['requestHId'],
                "hospitalId" => (string)$itemRequestToArray['hospital']['hospitalId'],
                "sourceDivisionId" => (string)$itemRequestToArray['sourceDivision']['divisionId'],
                "sourceDivision" => (string)$itemRequestToArray['sourceDivision']['divisionName'],
                "targetDivisionId" => (string)$itemRequestToArray['targetDivision']['divisionId'],
                "targetDivision" => (string)$itemRequestToArray['targetDivision']['divisionName'],
                "requestType" => (string)$itemRequestToArray['requestType'],
                "totalAmount" => (string)$itemRequestToArray['totalAmount'],
                "itemsNumber" => (string)$itemRequestToArray['itemCount'],
                "requestUserName" => (string)$itemRequestToArray['requestUserName'],
            ];

            foreach ($itemRequestToArray['requestItems'] as $requestItem) {
                $items[] = [
                    "requestId" => (string)$requestItem['requestId'],
                    "requestHId" => (string)$itemRequestToArray['requestHId'],
                    "hospitalId" => (string)$requestItem['hospitalId'],
                    "itemId" => (string)$requestItem['item']['itemId'],
                    "inHospitalItemId" => (string)$requestItem['inHospitalItemId'],
                    "sourceDivisionId" => (string)$requestItem['sourceDivision']['divisionId'],
                    "targetDivisionId" => (string)$requestItem['targetDivision']['divisionId'],
                    "requestQuantity" => (string)$requestItem['requestQuantity'],
                    "requestType" => (string)$requestItem['requestType'],
                    "quantity" => (string)$requestItem['quantity']['quantityNum'],
                    "quantityUnit" => (string)$requestItem['quantity']['quantityUnit'],
                    "itemUnit" => (string)$requestItem['quantity']['itemUnit'],
                    "price" => (string)$requestItem['price'],
                    "unitPrice" => (string)$requestItem['unitPrice'],
                ];
            }
        }

        SpiralDbItemRequest::insert($histories);
        SpiralDbRequestItem::insert($items);

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
                'sourceDivisionName' => $itemRequestToArray['sourceDivision']['divisionName'],
                'targetDivisionName' => $itemRequestToArray['targetDivision']['divisionName'],
                'itemCount' => number_format_jp($itemRequestToArray['itemCount']),
            ];
        }

        $mailBody = view('mail/ItemRequest/Register', [
            'name' => '%val:usr:name%',
            'hospitalName' => $itemRequests[0]->getHospital()->getHospitalName()->value(),
            'requestUserName' => $user->name,
            'histories' => $registrationMailViewModel,
            'url' => config('url.hospital', ''),
        ], false)->render();

        $ids = SpiralDb::title('NJ_HUserDB')->where('hospitalId', $user->hospitalId)->value('id')->whereIn('userPermission', [1, 3])->get();

        $mailId = SpiralDb::mail('NJ_HUserDB')->subject('[JoyPla] 請求書が作成されました')
            ->standby(false)->reserveDate('now')->bodyText($mailBody)->formAddress(FROM_ADDRESS)->formName(FROM_NAME)->mailField('mailAddress')->regist();

        $ids = array_values(array_column($ids->toArray(), 'id'));

        SpiralDb::mail('NJ_HUserDB')->ruleId($mailId)->sampling($ids);
    }


    public function search(HospitalId $hospitalId, object $search)
    {
        $itemSearchFlag = false;
        $itemViewInstance = RequestItemView::where('hospitalId', $hospitalId->value())->value('requestHId')->value('requestId');
        $historyViewInstance = ItemRequestView::where('hospitalId', $hospitalId->value());

        if ($search->itemName) {
            $itemViewInstance->orWhere('itemName', "%" . $search->itemName . "%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->makerName) {
            $itemViewInstance->orWhere('makerName', "%" . $search->makerName . "%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->itemCode) {
            $itemViewInstance->orWhere('itemCode', "%" . $search->itemCode . "%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->itemStandard) {
            $itemViewInstance->orWhere('itemStandard', "%" . $search->itemStandard . "%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->itemJANCode) {
            $itemViewInstance->orWhere('itemJANCode', "%" . $search->itemJANCode . "%", "LIKE");
            $itemSearchFlag = true;
        }

        $requestIds = [];
        if ($itemSearchFlag) {
            $itemViewInstance = $itemViewInstance->get();
            if ((int)$itemViewInstance->count === 0) {
                return [[], 0];
            }
            foreach ($itemViewInstance->data->all() as $item) {
                $historyViewInstance = $historyViewInstance->orWhere('requestHId', $item->requestHId);
                $requestIds[] = $item->requestId;
            }
        }

        if (is_array($search->sourceDivisionIds) && count($search->sourceDivisionIds) > 0) {
            foreach ($search->sourceDivisionIds as $sourceDivisionId) {
                $historyViewInstance->orWhere('sourceDivisionId', $sourceDivisionId);
            }
        }

        if (is_array($search->targetDivisionIds) && count($search->targetDivisionIds) > 0) {
            foreach ($search->targetDivisionIds as $targetDivisionId) {
                $historyViewInstance->orWhere('targetDivisionId', $targetDivisionId);
            }
        }

        if ($search->requestType === 1) {
            $historyViewInstance->orWhere('requestType', "1", "=");
        }
        if ($search->requestType === 2) {
            $historyViewInstance->orWhere('requestType', "2", "=");
        }

        if ($search->registrationDate) {
            $registrationDate = new DateYearMonth($search->registrationDate);
            $nextMonth = $registrationDate->nextMonth();

            $historyViewInstance->where('registrationTime', $registrationDate->format('Y-m-01'), '>=');
            $historyViewInstance->where('registrationTime', $nextMonth->format('Y-m-01'), '<');
        }

        $histories = $historyViewInstance->sort('id', 'desc')->page($search->currentPage)->paginate($search->perPage);
        if ((int)$histories->count === 0) {
            return [[], 0];
        }

        $itemViewInstance = RequestItemView::getNewInstance()->where('hospitalId', $hospitalId->value());
        foreach ($histories->data->all() as $history) {
            $itemViewInstance = $itemViewInstance->orWhere('requestHId', $history->requestHId);
        }
        foreach ($requestIds as $requestId) {
            $itemViewInstance = $itemViewInstance->orWhere('requestId', $requestId);
        }

        $items = $itemViewInstance->get();
        $itemRequests = [];
        foreach ($histories->data->all() as $history) {
            $sourceDivision = new Collection();
            $sourceDivision->hospitalId = $hospitalId->value();
            $sourceDivision->divisionId = $history->sourceDivisionId;
            $sourceDivision->divisionName = htmlspecialchars_decode($history->sourceDivision, ENT_QUOTES);
            $targetDivision = new Collection();
            $targetDivision->hospitalId = $hospitalId->value();
            $targetDivision->divisionId = $history->targetDivisionId;
            $targetDivision->divisionName = htmlspecialchars_decode($history->targetDivision, ENT_QUOTES);
            $history->set('sourceDivision', $sourceDivision);
            $history->set('targetDivision', $targetDivision);
            $history->set('requestType', (int)$history->requestType);
            $history->set('requestUserName', htmlspecialchars_decode($history->requestUserName, ENT_QUOTES));
            /*
            $test = new ItemRequest(
                new RequestHId($history->requestHId),
                new DateYearMonthDayHourMinutesSecond($history->registrationTime),
                new DateYearMonthDayHourMinutesSecond($history->updateTime),
                [],
                new hp(
                    $hospitalId,
                    (new HospitalName('hoge')),
                    "",
                    "",
                    new Pref(""),
                    ""
                ),
                Division::create($sourceDivision),
                Division::create($targetDivision),
                new RequestType((int)$history->requestType),
                (new TextFieldType64Bytes('aa'))
            );
            */
            $itemRequest = ItemRequest::create($history);
            //            var_dump($itemRequest);
            foreach ($items->data->all() as $item) {
                if ($itemRequest->getRequestHId()->equal($item->requestHId)) {
                    $item->set('sourceDivision', $sourceDivision);
                    $item->set('targetDivision', $targetDivision);
                    $item->set('requestType', (int)$item->requestType);
                    $itemRequest = $itemRequest->addRequestItem(RequestItem::create($item));
                }
                //                var_dump($itemRequest);
            }

            $itemRequests[] = $itemRequest;
        }
        //        var_dump($itemRequests);
        return [$itemRequests, $histories->count];
    }


    public function show(HospitalId $hospitalId, RequestHId $requestHId)
    {
        $historyViewInstance = ItemRequestView::where('hospitalId', $hospitalId->value())->where('requestHId', $requestHId->value())->get();
        if ((int)$historyViewInstance->count <= 0) {
            return null;
        }
        $itemViewInstance = RequestItemView::sort('id', 'asc')->where('hospitalId', $hospitalId->value())->where('requestHId', $requestHId->value())->get();
        if ((int)$itemViewInstance->count <= 0) {
            return null;
        }

        $history = $historyViewInstance->data->get(0);
        //        var_dump(htmlspecialchars_decode($history->requestUserName, ENT_QUOTES));
        $sourceDivision = new Collection();
        $sourceDivision->hospitalId = $hospitalId->value();
        $sourceDivision->divisionId = $history->sourceDivisionId;
        $sourceDivision->divisionName = htmlspecialchars_decode($history->sourceDivision, ENT_QUOTES);
        $targetDivision = new Collection();
        $targetDivision->hospitalId = $hospitalId->value();
        $targetDivision->divisionId = $history->targetDivisionId;
        $targetDivision->divisionName = htmlspecialchars_decode($history->targetDivision, ENT_QUOTES);
        $history->set('sourceDivision', $sourceDivision);
        $history->set('targetDivision', $targetDivision);
        $history->set('requestType', (int)$history->requestType);
        $history->set('requestUserName', htmlspecialchars_decode($history->requestUserName, ENT_QUOTES));

        $itemRequest = ItemRequest::create($history);
        //        var_dump($itemRequest);

        foreach ($itemViewInstance->data->all() as $item) {
            $item->set('sourceDivision', $sourceDivision);
            $item->set('targetDivision', $targetDivision);
            $item->set('requestType', (int)$item->requestType);
            $itemRequest = $itemRequest->addRequestItem(RequestItem::create($item));
        }

        return $itemRequest;

        //return null;
    }


    public function delete(HospitalId $hospitalId, RequestHId $requestHId)
    {
        $result = SpiralDbItemRequest::where('hospitalId', $hospitalId->value())->where('requestHId', $requestHId->value())->delete();
        return $result->count;
    }


    public function deleteItem(HospitalId $hospitalId, RequestId $requestId, ItemRequest $itemRequest)
    {
        $itemRequestToArray = $itemRequest->toArray();
        if (count($itemRequestToArray['requestItems']) === 0) {
            SpiralDbItemRequest::where('hospitalId', $hospitalId->value())->where('requestHId', $itemRequest->getRequestHId()->value())->delete();
            return true;
        }

        $history = [];
        $history[] = [
            "requestHId" => (string)$itemRequestToArray[0]['requestHId'],
            "totalAmount" => (string)$itemRequestToArray[0]['totalAmount'],
            "itemsNumber" => (string)$itemRequestToArray[0]['itemCount']
        ];
        var_dump($history);
        SpiralDbRequestItem::where('hospitalId', $hospitalId->value())->where('requestId', $requestId->value())->delete();
        SpiralDbItemRequest::upsert('requestHId', $history);

        return false;
    }


    public function update(HospitalId $hospitalId, ItemRequest $itemRequest)
    {
        $itemRequestToArray = $itemRequest->toArray();

        $history = [];
        $history[] = [
            "requestHId" => (string)$itemRequestToArray['requestHId'],
            "totalAmount" => (string)$itemRequestToArray['totalAmount'],
            "itemsNumber" => (string)$itemRequestToArray['itemCount'],
            "requestType" => (string)$itemRequestToArray['requestType']
        ];

        $items = [];
        foreach ($itemRequestToArray['requestItems'] as $requestItem) {
            $items[] = [
                "requestId" => (string)$requestItem['requestId'],
                "requestQuantity" => (string)$requestItem['requestQuantity'],
                "requestType" => (string)$requestItem['requestType']
            ];
        }
        SpiralDbItemRequest::upsert('requestHId', $history);
        SpiralDbRequestItem::upsert('requestId', $items);
    }
}

interface ItemRequestRepositoryInterface
{
    //    public function findByHospitalId(HospitalId $hospitalId);
    public function findByInHospitalItem(HospitalId $hospitalId, array $itemRequests);
    public function saveToArray(array $itemRequests);
    public function sendRegistrationMail(array $itemRequests, Auth $user);
    public function search(HospitalId $hospitalId, object $search);
    public function show(HospitalId $hospitalId, RequestHId $requestHId);
    public function delete(HospitalId $hospitalId, RequestHId $requestHId);
    public function deleteItem(HospitalId $hospitalId, RequestId $requestId, ItemRequest $itemRequest);
    public function update(HospitalId $hospitalId, ItemRequest $itemRequest);
}
