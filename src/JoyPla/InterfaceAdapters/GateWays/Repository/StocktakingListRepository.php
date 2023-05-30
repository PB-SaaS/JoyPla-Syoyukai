<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use framework\SpiralConnecter\SpiralDB;
use JoyPla\Enterprise\Models\DateYearMonth;
use JoyPla\Enterprise\Models\DateYearMonthDay;
use JoyPla\Enterprise\Models\DistributorId;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\StocktakingListId;
use JoyPla\Enterprise\Models\StocktakingList; //棚卸商品管理表
use JoyPla\Enterprise\Models\StocktakingListRow; //棚卸商品管理表項目
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use stdClass;

use function App\Lib\pager;
//Repositoryは処理系なのでclass名はStocktakingList。

class StocktakingListRepository implements StocktakingListRepositoryInterface
{
    public function register(StocktakingList $stocktakingList)
    {
        $stocktakingList = $stocktakingList->toArray();
        ModelRepository::getStocktakingListTableInstance()->create([
            'stockListId' => $stocktakingList['stocktakingListId'], //フィールドタイトルと違うため注意
            'stockListName' => $stocktakingList['stocktakingListName'], //フィールドタイトルと違うため注意
            'hospitalId' => $stocktakingList['hospitalId'],
            'divisionId' => $stocktakingList['divisionId'],
        ]);
    }

    //indexページで使うのがこれ？検索するし。ただ、検索条件は部署と一覧表の名称くらいになるのかなあ
    public function search(HospitalId $hospitalId, object $search)
    {
        $stocktakingListInstance = ModelRepository::getStocktakingListTableViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->orderBy('id', 'desc');

        if (is_array($search->divisionIds) && count($search->divisionIds) > 0) {
            foreach ($search->divisionIds as $divisionId) {
                $stocktakingListInstance->orWhere('divisionId', $divisionId);
            }
        }

        if($search->stocktakingListName != ''){ //商品一覧表の名前で検索。スペースでand検索。
            $needles = preg_split('/[\p{Z}\p{Cc}]++/u', $search->stocktakingListName); //空白文字で分割
            foreach($needles as $txt){
                $stocktakingListInstance->where('stocktakingListName', '%'.$txt.'%', 'LIKE'); //もしかしたら%はいらんかも
            }
        }

        $stocktakingLists = $stocktakingListInstance
            ->page($search->currentPage)
            ->paginate($search->perPage);

        if ($stocktakingLists->getData()->count() == 0) {
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

        if ($stocktakingLists->getData()->count() > 0) {
            $division = ModelRepository::getDivisionInstance()->where(
                'hospitalId',
                $hospitalId->value()
            );
            foreach ($stocktakingLists->getData()->all() as $stocktakingList) {
                $division->orWhere('divisionId', $stocktakingList->divisionId);
            }
            $divisions = $division->get();
        }

        foreach ($stocktakingLists->getData()->all() as $stocktakingList) {
            $list = new stdClass();
            $list->id = $stocktakingList->id;
            //必要なものを残す（このコメントは後で消す）
            $list->stocktakingListId = $stocktakingList->stockListId;
            $list->stocktakingListName = $stocktakingList->stockListName;
            $list->hospitalId = $stocktakingList->hospitalId;
            $list->divisionId = $stocktakingList->divisionId;
            $list->itemsNumber = $stocktakingList->itemsNumber;

            $list->_division = array_find($divisions, function (
                $division
            ) use ($stocktakingList) {
                return $division->divisionId == $stocktakingList->divisionId;
            });

            $list->_id = $count;
            $lists[] = $list;
            $count++;
        }

        return [$lists, $stocktakingLists->getTotal()];
    }

    public function findByStocktakingListId(
        HospitalId $hospitalId,
        StocktakingListId $stocktakingListId
    ) {
        $stocktakingList = ModelRepository::getStocktakingListTableViewInstance()
            ->where('hospitalId', $hospitalId->value())
            ->where('stockListId', $stocktakingListId->value())
            ->get();

        $stocktakingList = $stocktakingList->first();

        $division = ModelRepository::getDivisionInstance()
            ->where('divisionId', $stocktakingList->divisionId)
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

        $stocktakingList = new StocktakingList(
            new StocktakingListId($stocktakingList->stockListId),
            new HospitalId($stocktakingList->hospitalId),
            $stocktakingList->divisionId
                ? new DivisionId($stocktakingList->divisionId)
                : null,
            $stocktakingList->stockListName ?? null,
            $stocktakingList->itemsNumber
/* ここいらないはず
            $accountant->distributorId
                ? new DistributorId($accountant->distributorId)
                : null,
 */
        );

        $stocktakingList->_division = $division;
/* 
        $accountant->_distributor = $distributor;
 */

        $items = ModelRepository::getStocktakingListRowsViewInstance() //多分仮想DBのほう。画面表示に使いそうだし。
            ->where('stockListId', $stocktakingListId->value())
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

            $stocktakingListRow = StocktakingListRow::init(
                $item->index,
                $item->stockListId,
                $item->stockListRowId,
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
                $item->distributorName,
                $item->rackName,
                $item->mandatoryFlag
            );
            $stocktakingListRow->price = $item->price;
            $stocktakingListRow->priceId = $item->priceId;
            $stocktakingListRow->invUnitPrice = $item->invUnitPrice;
            $additems[] = $stocktakingListRow;
        }
        $stocktakingList->setItems($additems);
        return $stocktakingList;
    }

    public function save(StocktakingList $stocktakingList)
    {
        $itemUpsert = [];
        $itemInstance = ModelRepository::getStocktakingListRowsInstance()->where(
            'stockListId',
            $stocktakingList->getStocktakingListId()->value()
        );
        foreach ($stocktakingList->getItems() as $item) {
            $item = $item->toArray();
            $itemUpsert[] = [
                'updateTime' => 'now',
                'stockListId' => $stocktakingList->getStocktakingListId()->value(),
                'stockListRowId'=> $item['stocktakingListRowId'],
                'itemId' => $item['itemId'],
                'inHospitalItemId' => $item['inHospitalItemId'],
                'hospitalId' => $item['hospitalId'],
                'divisionId' => $stocktakingList->getDivisionId()->value(),
                'distributorId' => $item['distributorId'],
                'index' => $item['index'],
                'rackName' => $item['rackName'],
                'mandatoryFlag' => $item['mandatoryFlag'],
            ];

            if($item['stocktakingListRowId'] !== null){ //項目IDはフィールド値自動生成トリガで自動作成のためNULL対策
                $itemInstance->where(
                    'stockListRowId',
                    $item['stocktakingListRowId'],
                    '!='
                );
            }
        }

        ModelRepository::getStocktakingListTableInstance()
            ->where('stockListId', $stocktakingList->getStocktakingListId()->value())
            ->update([
                'updateTime' => 'now',
                'itemsNumber' => !empty($itemUpsert)? count($itemUpsert) : 0,
                'stockListName' => $stocktakingList->getStocktakingListName(),
            ]);

        $itemInstance->delete();
        if (!empty($itemUpsert)) {
            ModelRepository::getStocktakingListRowsInstance()->upsertBulk(
                'stockListRowId',
                $itemUpsert
            );
        }
    }

    public function saveToArray(array $stocktakingLists)
    {
        $stocktakingLists = array_map(function (StocktakingList $stocktakingList) {
            return $stocktakingList;
        }, $stocktakingLists);

        $stocktakingListRowsInstance = ModelRepository::getStocktakingListRowsInstance();

        $upsertList = [];
        $itemUpsert = [];

        foreach ($stocktakingLists as $stocktakingList) {
            $stocktakingListsToArray = $stocktakingList->toArray();
            $upsertList[] = [
                'updateTime' => 'now',
                'stockListId' => $stocktakingListsToArray['stockListId'],
                'hospitalId' => $stocktakingListsToArray['hospitalId'],
                'divisionId' => $stocktakingListsToArray['divisionId'],
                'itemsNumber' => $stocktakingListsToArray['itemsNumber'],
            ];

            foreach ($stocktakingList->getItems() as $item) {
                $item = $item->toArray();
                $itemUpsert[] = [
                    'updateTime' => 'now',
                    'stockListId' => (string) $stocktakingList
                        ->getStocktakingListId()
                        ->value(),
                    'stockListRowId' => (string) $item['stockListRowId'],
                    'itemId' => (string) $item['itemId'],
                    'inHospitalItemId' => (string) $item['inHospitalItemId'],
                    'hospitalId' => (string) $item['hospitalId'],
                    'divisionId' => (string) $item['divisionId'],
                    'distributorId' => (string) $item['distributorId'],
                    'index' => (string) $item['index'],
                    'rackName' => (string) $item['rackName'],
                    'mandatoryFlag' => (string) $item['mandatoryFlag'],
                ];

                $stocktakingListRowsInstance
                    ->orWhere('stockListId', $item['stockListId'], '=')
                    ->where(
                        'stockListRowId',
                        $item['stockListRowId'],
                        '!='
                    );
            }
        }
        ModelRepository::getStocktakingListTableInstance()->upsertBulk(
            'stockListId',
            $upsertList
        );

        $stocktakingListRowsInstance->delete();
        if (!empty($itemUpsert)) {
            ModelRepository::getStocktakingListRowsInstance()->upsertBulk(
                'stockListRowId',
                $itemUpsert
            );
        }
    }

}

interface StocktakingListRepositoryInterface
{
}
