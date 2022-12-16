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
    public function findByHospitalId(HospitalId $hospitalId)
    {
        $billingHistory = (SpiralDbItemRequest::where('hospitalId', $hospitalId->value())->get())->data->all();

        return $billingHistory;
    }

    public function findByInHospitalItem(HospitalId $hospitalId, array $requestItems)
    {
        $payoutUnitPriceUseFlag = (Hospital::where('hospitalId', $hospitalId->value())->value('payoutUnitPrice')->get())->data->get(0);
        var_dump($payoutUnitPriceUseFlag);
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

        $inHospitalItem = InHospitalItemView::where('hospitalId', $hospitalId->value());
        foreach ($requestItems as $item) {
            $inHospitalItem->orWhere('inHospitalItemId', $item->inHospitalItemId);
        }
        $inHospitalItem = ($inHospitalItem->get())->data->all();

        $result = [];
        foreach ($requestItems as $item) {
            $source_division_find_key = array_search($item->sourceDivisionId, collect_column($division, 'divisionId'));
            $target_division_find_key = array_search($item->targetDivisionId, collect_column($division, 'divisionId'));
            $inHospitalItem_find_key = array_search($item->inHospitalItemId, collect_column($inHospitalItem, 'inHospitalItemId'));

            $unitprice = 0;
            if (is_numeric($inHospitalItem[$inHospitalItem_find_key]->unitPrice)) {
                $unitprice = (float)$inHospitalItem[$inHospitalItem_find_key]->unitPrice;
            }

            if ($payoutUnitPriceUseFlag->payoutUnitPrice !== '1') {
                if ($inHospitalItem[$inHospitalItem_find_key]->quantity != 0 && $inHospitalItem[$inHospitalItem_find_key]->price != 0) {
                    $unitprice = ((int)$inHospitalItem[$inHospitalItem_find_key]->price / (int)$inHospitalItem[$inHospitalItem_find_key]->quantity) ;
                } else {
                    $unitprice = 0;
                }
            }

            $result[] = new RequestItem(
                (RequestId::generate()),
                (new RequestHId('')),
                (new InHospitalItemId($inHospitalItem[$inHospitalItem_find_key]->inHospitalItemId) ),
                (Item::create($inHospitalItem[$inHospitalItem_find_key])),
                $hospitalId,
                (Division::create($division[$source_division_find_key])),
                (Division::create($division[$target_division_find_key])),
                (new RequestQuantity((int)$item->requestQuantity)),
                (new RequestType($item->requestType)),
                (Quantity::create($inHospitalItem[$inHospitalItem_find_key])),
                (new Price($inHospitalItem[$inHospitalItem_find_key]->price) ),
                (new UnitPrice($unitprice) )
            );
        }
        return $result;
    }

    public function saveToArray(array $itemRequests)
    {
        $itemRequests = array_map(function (ItemRequest $itemRequest) {
            return $itemRequest;
        }, $itemRequests);

        $history = [];
        $items = [];

        foreach ($itemRequests as $itemRequest) {
            $itemRequestToArray = $itemRequest->toArray();

            $history[] = [
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

            foreach ($itemRequestToArray['requestItems'] as $itemRequest) {
                $items[] = [
                    "requestId" => (string)$itemRequestToArray['requestId'],
                    "requestHId" => (string)$itemRequestToArray['requestHId'],
                    "hospitalId" => (string)$itemRequest['hospitalId'],
                    "itemId" => (string)$itemRequest['item']['itemId'],
                    "inHospitalItemId" => (string)$itemRequest['inHospitalItemId'],
                    "sourceDivisionId" => (string)$itemRequest['sourceDivision']['divisionId'],
                    "targetDivisionId" => (string)$itemRequest['sourceDivision']['divisionId'],
                    "requestQuantity" => (string)$itemRequest['requestQuantity'],
                    "quantity" => (string)$itemRequest['quantity']['quantityNum'],
                    "quantityUnit" => (string)$itemRequest['quantity']['quantityUnit'],
                    "itemUnit" => (string)$itemRequest['quantity']['itemUnit'],
                    "price" => (string)$itemRequest['price'],
                    "unitPrice" => (string)$itemRequest['unitPrice'],
                ];
            }
        }

        SpiralDbItemRequest::insert($history);
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
            $itemRequestsToArray = $itemRequest->toArray();
            $registrationMailViewModel[] = [
                'requestHId' => $itemRequestsToArray['requestHId'],
                'sourceDivisionName' => $itemRequestsToArray['sourceDivision']['divisionName'],
                'targetDivisionName' => $itemRequestsToArray['targetDivision']['divisionName'],
                'itemCount' => number_format_jp($itemRequestsToArray['itemCount']),
            ];
        }

        $mailBody = view('mail/ItemRequest/Register', [
            'name' => '%val:usr:name%',
            'hospitalName' => $itemRequest[0]->getHospital()->getHospitalName()->value(),
            'requestUserName' => $user->name,
            'histories' => $registrationMailViewModel,
            'url' => config('url.hospital', ''),
        ], false)->render();

        $ids = SpiralDb::title('NJ_HUserDB')->where('hospitalId', $user->hospitalId)->value('id')->whereIn('userPermission', [1,3])->get();

        $mailId = SpiralDb::mail('NJ_HUserDB')->subject('[JoyPla] 未発注書が作成されました')
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
    public function findByHospitalId(HospitalId $hospitalId);
    public function findByInHospitalItem(HospitalId $hospitalId, array $itemRequests);
    public function saveToArray(array $itemRequests);
    public function sendRegistrationMail(array $itemRequests, Auth $user);

//    public function search(HospitalId $hospitalId, object $search);
//    public function index(HospitalId $hospitalId, ItemRequestId $ItemRequestId);
//    public function delete(HospitalId $hospitalId, ItemRequestId $ItemRequestId);
}
