<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\ItemRequest as SpiralDbItemRequest;
use App\SpiralDb\RequestItem as SpiralDbRequestItem;
use App\SpiralDb\Hospital;
use App\SpiralDb\InHospitalItemView;
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
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Item;
use JoyPla\Enterprise\Models\Price;
use JoyPla\Enterprise\Models\Quantity;
use JoyPla\Enterprise\Models\UnitPrice;

class ItemRequestRepository implements ItemRequestRepositoryInterface
{
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

    /*
    public function search(HospitalId $hospitalId, object $search)
    {
        $itemSearchFlag = false;
        $itemViewInstance = ItemRequestItemView::where('hospitalId', $hospitalId->value())->value('billingNumber');
        $historyViewInstance = ItemRequestView::where('hospitalId', $hospitalId->value());

        if ($search->itemName !== "") {
            $itemViewInstance->orWhere('itemName', "%".$search->itemName."%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->makerName !== "") {
            $itemViewInstance->orWhere('makerName', "%".$search->makerName."%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->itemCode !== "") {
            $itemViewInstance->orWhere('itemCode', "%".$search->itemCode."%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->itemStandard !== "") {
            $itemViewInstance->orWhere('itemStandard', "%".$search->itemStandard."%", "LIKE");
            $itemSearchFlag = true;
        }
        if ($search->itemJANCode !== "") {
            $itemViewInstance->orWhere('itemJANCode', "%".$search->itemJANCode."%", "LIKE");
            $itemSearchFlag = true;
        }

        if ($itemSearchFlag) {
            $itemViewInstance = $itemViewInstance->get();
            if ($itemViewInstance->count == 0) {
                return [[],0];
            }
            foreach ($itemViewInstance->data->all() as $item) {
                $historyViewInstance = $historyViewInstance->orWhere('billingNumber', $item->billingNumber);
            }
        }

        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $historyViewInstance->orWhere('divisionId', $divisionId);
            }
        }

        if ($search->yearMonth !== "") {
            $yearMonth = new DateYearMonth($search->yearMonth);
            $nextMonth =  $yearMonth->nextMonth();

            $historyViewInstance->where('billingDate', $yearMonth->format('Y-m-01'), '>=');
            $historyViewInstance->where('billingDate', $nextMonth->format('Y-m-01'), '<');
        }


        $historys = $historyViewInstance->sort('billingDate', 'desc')->page($search->currentPage)->paginate($search->perPage);

        if ($historys->count == 0) {
            return  [[],0];
        }

        $itemViewInstance = ItemRequestItemView::getNewInstance()->where('hospitalId', $hospitalId->value());

        foreach ($historys->data->all() as $history) {
            $itemViewInstance = $itemViewInstance->orWhere('billingNumber', $history->billingNumber);
        }

        $items = $itemViewInstance->get();
        $ItemRequests = [];
        foreach ($historys->data->all() as $history) {
            $ItemRequest = ItemRequest::create($history);

            foreach ($items->data->all() as $item) {
                if ($ItemRequest->getItemRequestId()->equal($item->billingNumber)) {
                    $ItemRequest = $ItemRequest->addItemRequestItem(ItemRequestItem::create($item));
                }
            }

            $ItemRequests[] = $ItemRequest;
        }

        return [ $ItemRequests , $historys->count ];
    }


    public function index(HospitalId $hospitalId, ItemRequestId $ItemRequestId)
    {
        $ItemRequestView = ItemRequestView::where('hospitalId', $hospitalId->value())->where('billingNumber', $ItemRequestId->value())->get();
        if ($ItemRequestView->count <= 0) {
            return null;
        }
        $ItemRequestItemView = ItemRequestItemView::sort('id', 'asc')->where('hospitalId', $hospitalId->value())->where('billingNumber', $ItemRequestId->value())->get();

        $ItemRequest = ItemRequest::create($ItemRequestView->data->get(0));

        foreach ($ItemRequestItemView->data->all() as $item) {
            $ItemRequest = $ItemRequest->addItemRequestItem(ItemRequestItem::create($item));
        }

        return $ItemRequest;
    }

    public function delete(HospitalId $hospitalId, ItemRequestId $ItemRequestId)
    {
        ItemRequestView::where('hospitalId', $hospitalId->value())->where('billingNumber', $ItemRequestId->value())->delete();
    }
    */
}

interface ItemRequestRepositoryInterface
{
    public function findByInHospitalItem(HospitalId $hospitalId, array $itemRequests);
    public function saveToArray(array $itemRequests);
    public function sendRegistrationMail(array $itemRequests, Auth $user);

    //    public function search(HospitalId $hospitalId, object $search);
    //    public function index(HospitalId $hospitalId, ItemRequestId $ItemRequestId);
    //    public function delete(HospitalId $hospitalId, ItemRequestId $ItemRequestId);
}
