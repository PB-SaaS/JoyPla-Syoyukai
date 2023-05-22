<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use framework\SpiralConnecter\SpiralDB;
use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\DateYearMonthDay;
use JoyPla\Enterprise\Models\DistributorId;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\itemListId;
use JoyPla\Enterprise\Models\ItemList; //商品リスト
use JoyPla\Enterprise\Models\ItemListRow; //商品リスト項目
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use stdClass;

use function App\Lib\pager;
//Repositoryは処理系なのでclass名はItemList。

class ItemListRepository implements ItemListRepositoryInterface
{
    public function register(ItemList $itemList)
    {
        $itemList = $itemList->toArray();
        ModelRepository::getItemListTableInstance()->create([
            'itemListId' => $itemList['itemListId'],
            'hospitalId' => $itemList['hospitalId'],
            'divisionId' => $itemList['divisionId'],
            'itemListName' => $itemList['itemListName'],
            'usableStatus' => $itemList['usableStatus'],
        ]);
    }

    //indexページで使うのがこれ？検索するし。ただ、検索条件は部署と一覧表の名称くらいになるのかなあ
    public function search(HospitalId $hospitalId, object $search)
    {
        $itemListInstance = ModelRepository::getItemListTableViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->orderBy('id', 'desc');

        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $itemListInstance->orWhere('divisionId', $divisionId);
            }
        }

        if($search->itemListName != ''){ //商品一覧表の名前で検索。スペースでand検索。
            $needles = preg_split('/[\p{Z}\p{Cc}]++/u', $search->itemListName); //空白文字で分割
            foreach($needles as $txt){
                $itemListInstance->where('itemListName', '%'.$txt.'%', 'LIKE'); //もしかしたら%はいらんかも
            }
        }

        $itemLists = $itemListInstance
            ->page($search->currentPage)
            ->paginate($search->perPage);

        if ($itemLists->getData()->count() == 0) {
            return [[], 0];
        }
        /*
        10limit 
        1page = 1 ... 2 ... 3 ....... 10

        10limit
        2page = 11 ... 12 ... 13 ... 14 ... 15
        */

        $count =
            $search->currentPage - 1 > 0
                ? $search->perPage * ($search->currentPage - 1) + 1
                : 1;
        $lists = [];

        if ($itemLists->getData()->count() > 0) {
            $division = ModelRepository::getDivisionInstance()->where(
                'hospitalId',
                $hospitalId->value()
            );
            foreach ($itemLists->getData()->all() as $itemList) {
                $division->orWhere('divisionId', $itemList->divisionId);
            }
            $divisions = $division->get();
        }

        foreach ($itemLists->getData()->all() as $itemList) {
            $list = new stdClass();
            $list->id = $itemList->id;
            //必要なものを残す（このコメントは後で消す）
            $list->itemListId = $itemList->itemListId;
            $list->itemListName = $itemList->itemListName;
            $list->hospitalId = $itemList->hospitalId;
            $list->divisionId = $itemList->divisionId;
            $list->itemsNumber = $itemList->itemsNumber;
            $list->usableStatus = $itemList->usableStatus;

            $list->_division = array_find($divisions, function (
                $division
            ) use ($itemList) {
                return $division->divisionId == $itemList->divisionId;
            });

            $list->_id = $count;
            $lists[] = $list;
            $count++;
        }

        return [$lists, $itemLists->getTotal()];
    }

    public function findByItemListId(
        HospitalId $hospitalId,
        ItemListId $itemListId
    ) {
        $itemList = ModelRepository::getItemListTableViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('itemListId', $itemListId->value())
            ->get();

        $itemList = $itemList->first();

        $division = ModelRepository::getDivisionInstance()
            ->where('divisionId', $itemList->divisionId)
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->first();
/* ここいらないはず
        $distributor = ModelRepository::getDistributorInstance()
            ->where('distributorId', $accountant->distributorId)
            ->where('hospitalId', $hospitalId->value())
            ->get()
            ->first();
 */

        $itemList = new ItemList(
            new ItemListId($itemList->itemListId),
            new HospitalId($itemList->hospitalId),
            $itemList->divisionId
                ? new DivisionId($itemList->divisionId)
                : null,
            $itemList->itemListName ?? null,
            $itemList->usableStatus,
            $itemList->itemsNumber
/* ここいらないはず
            $accountant->distributorId
                ? new DistributorId($accountant->distributorId)
                : null,
 */
        );

        $itemList->_division = $division;
/* 
        $accountant->_distributor = $distributor;
 */

        $items = ModelRepository::getItemListRowsViewInstance() //多分仮想DBのほう。画面表示に使いそうだし。
            ->where('itemListId', $itemListId->value())
            ->get();

        $additems = [];
        foreach ($items->all() as $item) {
            //ラベルバーコード作成
            if($item->quantity >= 10000 ){
                $quantity = '9999';
            } else if($item->quantity < 1 ){
                $quantity = str_pad(1 , 4, "0", STR_PAD_LEFT);
            } else {
                $quantity = str_pad($item->quantity , 4, "0", STR_PAD_LEFT);
            }
            $labelBarcode = "01".$item->labelId.$quantity;

            $additems[] = ItemListRow::init(
                $item->index,
                $item->itemListId,
                $item->itemListRowId,
                $item->itemId,
                $item->inHospitalItemId,
                $item->makerName,
                $item->itemName,
                $item->itemCode,
                $item->itemStandard,
                $item->itemJANCode,
                $item->quantity,
                $item->quantityUnit,
                $item->itemUnit,
                $labelBarcode,
                $item->distributorId,
                $item->hospitalId,
                $item->distributorName
            );
        }
        $itemList->setItems($additems);
        return $itemList;
    }

    public function save(ItemList $itemList)
    {
        $itemUpsert = [];
        $itemInstance = ModelRepository::getItemListRowsInstance()->where(
            'itemListId',
            $itemList->getItemListId()->value()
        );
        foreach ($itemList->getItems() as $item) {
            $item = $item->toArray();
            $itemUpsert[] = [
                'updateTime' => 'now',
                'itemListId' => $itemList->getItemListId()->value(),
                'itemListRowId'=> $item['itemListRowId'],
                'itemId' => $item['itemId'],
                'inHospitalItemId' => $item['inHospitalItemId'],
                'hospitalId' => $item['hospitalId'],
                'divisionId' => $itemList->getDivisionId()->value(),
                'distributorId' => $item['distributorId'],
                'index' => $item['index'],
            ];

            if($item['itemListRowId'] !== null){ //項目IDはフィールド値自動生成トリガで自動作成のためNULL対策
                $itemInstance->where(
                    'itemListRowId',
                    $item['itemListRowId'],
                    '!='
                );
            }
        }

        ModelRepository::getItemListTableInstance()
            ->where('itemListId', $itemList->getItemListId()->value())
            ->update([
                'updateTime' => 'now',
                'itemsNumber' => !empty($itemUpsert)? count($itemUpsert) : 0,
                'itemListName' => $itemList->getItemListName(),
            ]);

        $itemInstance->delete();
        if (!empty($itemUpsert)) {
            ModelRepository::getItemListRowsInstance()->upsertBulk(
                'itemListRowId',
                $itemUpsert
            );
        }
    }

    public function saveToArray(array $itemLists)
    {
        $itemLists = array_map(function (ItemList $itemList) {
            return $itemList;
        }, $itemLists);

        $itemListRowsInstance = ModelRepository::getItemListRowsInstance();

        $upsertList = [];
        $itemUpsert = [];

        foreach ($itemLists as $itemList) {
            $itemListToArray = $itemList->toArray();
            $upsertList[] = [
                'updateTime' => 'now',
                'itemListId' => $itemListToArray['itemListId'],
                'hospitalId' => $itemListToArray['hospitalId'],
                'divisionId' => $itemListToArray['divisionId'],
                'itemsNumber' => $itemListToArray['itemsNumber'],
                'usableStatus' => $itemListToArray['usableStatus'],
            ];

            foreach ($itemList->getItems() as $item) {
                $item = $item->toArray();
                $itemUpsert[] = [
                    'updateTime' => 'now',
                    'itemListId' => (string) $itemList
                        ->getItemListId()
                        ->value(),
                    'itemListRowId' => (string) $item['itemListRowId'],
                    'itemId' => (string) $item['itemId'],
                    'inHospitalItemId' => (string) $item['inHospitalItemId'],
                    'hospitalId' => (string) $item['hospitalId'],
                    'divisionId' => (string) $item['divisionId'],
                    'distributorId' => (string) $item['distributorId'],
                    'index' => (string) $item['index'],
                ];

                $itemListRowsInstance
                    ->orWhere('itemListId', $item['itemListId'], '=')
                    ->where(
                        'itemListRowId',
                        $item['itemListRowId'],
                        '!='
                    );
            }
        }
        ModelRepository::getItemListTableInstance()->upsertBulk(
            'itemListId',
            $upsertList
        );

        $itemListRowsInstance->delete();
        if (!empty($itemUpsert)) {
            ModelRepository::getItemListRowsInstance()->upsertBulk(
                'itemListRowId',
                $itemUpsert
            );
        }
    }

}

interface ItemListRepositoryInterface
{
}
