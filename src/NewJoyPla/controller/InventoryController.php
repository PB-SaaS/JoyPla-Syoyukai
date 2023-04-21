<?php

namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\Division;
use App\Model\Hospital;
use App\Model\InHospitalItemView;
use App\Model\Inventory;
use App\Model\InventoryEnd;
use App\Model\InventoryHistory;
use App\Model\StockTakingTransaction;
use App\Model\StockView;
use App\Model\Lot;

use ApiErrorCode\FactoryApiErrorCode;
use App\Model\Billing;
use App\Model\Distributor;
use App\Model\InventoryHistoryDivisionView;
use App\Model\InventoryItemView;
use App\Model\ReceivingView;
use App\Model\Stock;
use stdClass;
use Exception;
use field\DateYearMonthDay;

class InventoryController extends Controller
{
    public function __construct()
    {
    }

    public function index(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = [];
        try {
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser()) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            if ($user_info->isApprover()) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            if ($user_info->isAdmin()) {
                $division = Division::where(
                    'hospitalId',
                    $user_info->getHospitalId()
                )->get();
            } else {
                $division = Division::where(
                    'hospitalId',
                    $user_info->getHospitalId()
                )
                    ->where('divisionId', $user_info->getDivisionId())
                    ->get();
            }

            $hospital_data = Hospital::where(
                'hospitalId',
                $user_info->getHospitalId()
            )->get();
            $hospital_data = $hospital_data->data->all();
            $useUnitPrice = $hospital_data[0]->invUnitPrice;

            $api_url = '%url/rel:mpgt:Inventory%';

            $content = $this->view(
                'NewJoyPla/view/InventoryContentEntry',
                [
                    'api_url' => $api_url,
                    'user_info' => $user_info,
                    'division' => $division,
                    'useUnitPrice' => $useUnitPrice,
                    'csrf_token' => Csrf::generate(16),
                ],
                false
            );
        } catch (Exception $ex) {
            $content = $this->view(
                'NewJoyPla/view/template/Error',
                [
                    'code' => $ex->getCode(),
                    'message' => $ex->getMessage(),
                    'csrf_token' => Csrf::generate(16),
                ],
                false
            );
        } finally {
            $head = $this->view(
                'NewJoyPla/view/template/parts/Head',
                [],
                false
            );
            $header = $this->view(
                'NewJoyPla/src/HeaderForMypage',
                [
                    'SPIRAL' => $SPIRAL,
                ],
                false
            );

            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view(
                'NewJoyPla/view/template/Template',
                [
                    'title' => 'JoyPla 棚卸内容入力',
                    'script' => '',
                    'content' => $content->render(),
                    'head' => $head->render(),
                    'header' => $header->render(),
                    'baseUrl' => '',
                ],
                false
            );
        }
    }

    public function inventoryRegistAPI()
    {
        global $SPIRAL;
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser()) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            if (
                $user_info->isUser() &&
                $SPIRAL->getParam('divisionId') !== $user_info->getDivisionId()
            ) {
                throw new Exception('invalid division was sent.', 999);
            }

            $inventory = $SPIRAL->getParam('inventory');
            $inventory = $this->requestUrldecode($inventory);
            $divisionId = $SPIRAL->getParam('divisionId');

            $isTemporaryData = $SPIRAL->getParam('isTemporaryData');

            if ($divisionId == '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $division = Division::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->where('divisionId', $divisionId)
                ->get();
            if ($division->count == 0) {
                throw new Exception('invalid division was sent.', 999);
            }

            $in_hospital_item = InHospitalItemView::where(
                'hospitalId',
                $user_info->getHospitalId()
            )->plain();

            foreach ($inventory as $key => $record) {
                $in_hospital_item->orWhere(
                    'inHospitalItemId',
                    $record['recordId']
                );
            }
            $in_hospital_item = $in_hospital_item->get();

            foreach ($inventory as $key => $record) {
                $exist = false;
                foreach ($in_hospital_item->data->all() as $in_hp_item) {
                    $lot_flag = 0;
                    if ($record['recordId'] == $in_hp_item->inHospitalItemId) {
                        $exist = true;
                        $lot_flag = (int) $in_hp_item->lotManagement;
                        $inventory[$key]['kakaku'] = $in_hp_item->price;
                        $inventory[$key]['irisu'] = $in_hp_item->quantity;
                        $inventory[$key]['unit'] = $in_hp_item->quantityUnit;
                        $inventory[$key]['itemUnit'] = $in_hp_item->itemUnit;
                        $inventory[$key]['unitPrice'] = $in_hp_item->unitPrice;
                        break;
                    }
                }

                if (!$exist) {
                    throw new Exception(
                        'Inventory item does not belong to the hospital.',
                        999
                    );
                }

                if ($record['lotDate']) {
                    if (
                        preg_match(
                            DateYearMonthDay::FORMAT_DELIMITER_SLASH,
                            $record['lotDate']
                        )
                    ) {
                        $inventory[$key]['lotDate'] = \App\Lib\changeDateFormat(
                            'Y/m/d',
                            $record['lotDate'],
                            'Y-m-d'
                        );
                    } elseif (
                        preg_match(
                            DateYearMonthDay::FORMAT_DELIMITER_HYPHEN,
                            $record['lotDate']
                        )
                    ) {
                        $inventory[$key]['lotDate'] = \App\Lib\changeDateFormat(
                            'Y-m-d',
                            $record['lotDate'],
                            'Y-m-d'
                        );
                    } elseif (
                        preg_match(
                            DateYearMonthDay::FORMAT_DELIMITER_JAPANESE_CHARACTER,
                            $record['lotDate']
                        )
                    ) {
                        $inventory[$key]['lotDate'] = \App\Lib\changeDateFormat(
                            'Y年m月d日',
                            $record['lotDate'],
                            'Y-m-d'
                        );
                    } else {
                        throw new Exception('invalid lot', 100);
                    }
                }

                $record['lotDate'] = $inventory[$key]['lotDate'];

                if ($record['countNum'] > 0) {
                    if (
                        $lot_flag &&
                        ($record['lotNumber'] == '' || $record['lotDate'] == '')
                    ) {
                        throw new Exception('invalid lot', 100);
                    }

                    if (
                        ($record['lotNumber'] != '' &&
                            $record['lotDate'] == '') ||
                        ($record['lotNumber'] == '' && $record['lotDate'] != '')
                    ) {
                        throw new Exception('invalid lotNumber input', 101);
                    }
                    if (
                        $record['lotNumber'] != '' &&
                        $record['lotDate'] != ''
                    ) {
                        //if ((!ctype_alnum($item['lotNumber'])) || (strlen($item['lotNumber']) > 20))
                        if (
                            !preg_match(
                                '/^[a-zA-Z0-9!-\/:-@¥[-`{-~]+$/',
                                $record['lotNumber']
                            ) ||
                            strlen($record['lotNumber']) > 20
                        ) {
                            throw new Exception(
                                'invalid lotNumber format',
                                102
                            );
                        }
                    }
                }
            }

            $inventoryEnd = InventoryEnd::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->where('inventoryStatus', '1')
                ->get();
            $invEndId = $this->makeId('09');
            if ($inventoryEnd->count == 0) {
                $result = InventoryEnd::create([
                    'hospitalId' => $user_info->getHospitalId(),
                    'inventoryEndId' => $invEndId,
                ]);
            } else {
                $invEndId = $inventoryEnd->data->get(0)->inventoryEndId;
            }

            $inventoryHistory = InventoryHistory::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->where('divisionId', $divisionId)
                ->where('inventoryEndId', $invEndId)
                ->get();

            $invHistId = $this->makeId('08');

            if ($inventoryHistory->count == 0) {
                $create_data = [];
                $create_data = [
                    'inventoryHId' => $invHistId,
                    'inventoryEndId' => $invEndId,
                    'hospitalId' => $user_info->getHospitalId(),
                    'divisionId' => $divisionId,
                    'itemsNumber' => 0,
                    'totalAmount' => 0,
                    'inventoryHStatus' => 1,
                ];
                $result = InventoryHistory::create($create_data);
                $inventoryHStatus = '1';
            } else {
                $invHistId = $inventoryHistory->data->get(0)->inventoryHId;
                $inventoryHStatus = $inventoryHistory->data->get(0)
                    ->inventoryHStatus;
            }

            if ($inventoryHStatus != '1') {
                throw new Exception(
                    '一時保存のステータスではないため、更新できませんでした。',
                    1
                );
            }

            $hospital_data = Hospital::where(
                'hospitalId',
                $user_info->getHospitalId()
            )->get();
            $hospital_data = $hospital_data->data->get(0);
            $useUnitPrice = (int) $hospital_data->invUnitPrice;
            $stock_taking_trdata = [];

            if ($isTemporaryData === 'true') {
                Inventory::getNewInstance()
                    ->where('hospitalId', $user_info->getHospitalId())
                    ->where('inventoryHId', $invHistId)
                    ->delete();
            }

            //在庫として存在するものを０で登録する
            /*
            $stock = StockView::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$divisionId);

            foreach ($inventory as $data)
            {
                //棚卸対象以外
                $stock->where('inHospitalItemId',$data['recordId'],"!=");
            }

            $stock = $stock->get();

            foreach ($stock->data->all() as $data)
            {

                $unitPrice = $useUnitPrice
                    ? (str_replace(',', '', $data->unitPrice))
                    : (
                        ((float)$data->price == 0 || (float)$data->quantity == 0)
                            ? 0
                            : (float)$data->price / (float)$data->quantity
                        );
                $stock_taking_trdata[] = [
                    'inventoryEndId' => $invEndId,
                    'inventoryHId' => $invHistId,
                    'inHospitalItemId' => $data->inHospitalItemId,
                    'hospitalId' => $user_info->getHospitalId(),
                    'divisionId' => $data->divisionId,
                    'price' => str_replace(',', '', $data->price),
                    'inventryNum' => 0, // 数は０で登録
                    'inventryAmount' => (float)$unitPrice * 0,
                    'quantity' => $data->quantity,
                    'quantityUnit' => $data->quantityUnit,
                    'itemUnit' => $data->itemUnit,
                    'unitPrice' => $unitPrice,
                    'invUnitPrice' => (float)$useUnitPrice,
                    'lotNumber' => '',
                    'lotDate' => '',
                    'lotUniqueKey' => $user_info->getHospitalId().$divisionId.$data->inHospitalItemId.''.''
                ];
            }
            */
            $tmpInventory = [];
            foreach ($inventory as $key => $record) {
                $exist = false;
                foreach ($tmpInventory as $key => $tmp) {
                    if (
                        $record['recordId'] === $tmp['recordId'] &&
                        $record['lotDate'] === $tmp['lotDate'] &&
                        $record['lotNumber'] === $tmp['lotNumber']
                    ) {
                        $tmpInventory[$key]['countNum'] =
                            (int) $tmp['countNum'] + (int) $record['countNum'];
                        $exist = true;
                    }
                }
                if (!$exist) {
                    $tmpInventory[] = $record;
                }
            }

            foreach ($tmpInventory as $data) {
                if ((int) $data['countNum'] >= 0) {
                    $unitPrice = $useUnitPrice
                        ? str_replace(',', '', $data['unitPrice'])
                        : ((float) str_replace(',', '', $data['kakaku']) == 0 ||
                        (float) $data['irisu'] == 0
                            ? 0
                            : (float) str_replace(',', '', $data['kakaku']) /
                                (float) $data['irisu']);
                    $stock_taking_trdata[] = [
                        'inventoryEndId' => $invEndId,
                        'inventoryHId' => $invHistId,
                        'inHospitalItemId' => $data['recordId'],
                        'hospitalId' => $user_info->getHospitalId(),
                        'divisionId' => $divisionId,
                        'price' => str_replace(',', '', $data['kakaku']),
                        'inventryNum' => (int) $data['countNum'],
                        'inventryAmount' =>
                            (float) $unitPrice * (float) $data['countNum'],
                        'quantity' => $data['irisu'],
                        'quantityUnit' => $data['unit'],
                        'itemUnit' => $data['itemUnit'],
                        'unitPrice' => $unitPrice,
                        'invUnitPrice' => (float) $useUnitPrice,
                        'lotNumber' => $data['lotNumber'],
                        'lotDate' => $data['lotDate'],
                        'lotUniqueKey' =>
                            $user_info->getHospitalId() .
                            $divisionId .
                            $data['recordId'] .
                            $data['lotNumber'] .
                            $data['lotDate'],
                    ];
                }
            }

            $result = StockTakingTransaction::insert($stock_taking_trdata);

            $inventory_history_data = Inventory::getNewInstance()
                ->where('hospitalId', $user_info->getHospitalId())
                ->where('inventoryHId', $invHistId)
                ->get();
            $inventory_history_data = $inventory_history_data->data->all();
            $history_ids = [];
            $history_total_amount = 0;
            foreach ($inventory_history_data as $val) {
                $history_total_amount =
                    $history_total_amount + (float) $val->inventryAmount;
                if (!in_array($val->inHospitalItemId, $history_ids)) {
                    $history_ids[] = $val->inHospitalItemId;
                }
            }

            $end_inventory_data = Inventory::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->where('inventoryEndId', $invEndId)
                ->get();
            $end_inventory_data = $end_inventory_data->data->all();
            $end_ids = [];
            $end_total_amount = 0;
            foreach ($end_inventory_data as $val) {
                $end_total_amount =
                    $end_total_amount + (float) $val->inventryAmount;
                if (!in_array($val->inHospitalItemId, $end_ids)) {
                    $end_ids[] = $val->inHospitalItemId;
                }
            }

            $result = InventoryHistory::where(
                'inventoryHId',
                $invHistId
            )->update([
                'updateTime' => 'now',
                'itemsNumber' => count($history_ids),
                'totalAmount' => $history_total_amount,
            ]);

            $result = InventoryEnd::where('inventoryEndId', $invEndId)
                ->where('hospitalId', $user_info->getHospitalId())
                ->update([
                    'itemsNumber' => count($end_ids),
                    'totalAmount' => $end_total_amount,
                ]);

            $content = new ApiResponse(
                $result->ids,
                $result->count,
                $result->code,
                $result->message,
                ['inventoryEntry']
            );
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['inventoryEntry']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function getTemporaryData()
    {
        global $SPIRAL;
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser()) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $divisionId = $SPIRAL->getParam('divisionId');

            if ($divisionId == '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $inventoryEnd = InventoryEnd::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->where('inventoryStatus', '1')
                ->value('inventoryEndId')
                ->get();
            if ($inventoryEnd->count == 0) {
                throw new Exception('取得する情報がありませんでした', 1);
            }

            $invEndId = $inventoryEnd->data->get(0)->inventoryEndId;
            $inventoryHistory = InventoryHistory::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->where('divisionId', $divisionId)
                ->where('inventoryEndId', $invEndId)
                ->value('inventoryHStatus')
                ->value('inventoryHId')
                ->get();
            if ($inventoryHistory->count == 0) {
                throw new Exception('取得する情報がありませんでした', 1);
            }

            $inventoryHistory = $inventoryHistory->data->get(0);
            if ($inventoryHistory->inventoryHStatus !== '1') {
                throw new Exception(
                    '一時保存のステータスではないため、取得しませんでした',
                    1
                );
            }

            $inventory = InventoryItemView::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->where('inventoryHId', $inventoryHistory->inventoryHId)
                ->plain()
                ->get();

            $in_hospital_item = InHospitalItemView::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->value('lotManagement')
                ->value('inHospitalItemId')
                ->plain();

            foreach ($inventory->data->all() as $stock_item) {
                $in_hospital_item->orWhere(
                    'inHospitalItemId',
                    $stock_item->inHospitalItemId
                );
            }
            foreach ($inventory->data->all() as $lot_item) {
                $in_hospital_item->orWhere(
                    'inHospitalItemId',
                    $lot_item->inHospitalItemId
                );
            }

            $in_hospital_item = $in_hospital_item->get();
            $data = [];
            foreach ($inventory->data->all() as $item) {
                $inventryNum = (int) $item->inventryNum;
                $lotManagement = 0;
                foreach ($in_hospital_item->data->all() as $in_hp_item) {
                    if (
                        $in_hp_item->inHospitalItemId == $item->inHospitalItemId
                    ) {
                        $lotManagement = $in_hp_item->lotManagement;
                        break;
                    }
                }

                if ($inventryNum != 0) {
                    $data[] = [
                        'divisionId' => '',
                        'maker' => $item->makerName,
                        'shouhinName' => $item->itemName,
                        'code' => $item->itemCode,
                        'kikaku' => $item->itemStandard,
                        'irisu' => $item->quantity,
                        'kakaku' => $item->price,
                        'jan' => $item->itemJANCode,
                        'oroshi' => $item->distributorName,
                        'recordId' => $item->inHospitalItemId,
                        'unit' => $item->quantityUnit,
                        'itemUnit' => $item->itemUnit,
                        'distributorId' => $item->distributorId,
                        'count' => (int) $inventryNum,
                        'countNum' => (int) $inventryNum,
                        'labelId' => $item->labelId,
                        'unitPrice' => $item->unitPrice,
                        'lotNumber' => $item->lotNumber,
                        'lotDate' => \App\Lib\changeDateFormat(
                            'Y年m月d日',
                            $item->lotDate,
                            'Y-m-d'
                        ),
                        'lotFlag' => $lotManagement == 1 ? 'はい' : '',
                        'lotFlagBool' => $lotManagement,
                    ];
                }
            }

            $content = new ApiResponse($data, count($data), 0, 'OK', [
                'getTemporaryData',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['getTemporaryData']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function getLotAndStockApi()
    {
        global $SPIRAL;
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser()) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $divisionId = $SPIRAL->getParam('divisionId');

            if ($divisionId == '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $inventoryEnd = InventoryEnd::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->where('inventoryStatus', '1')
                ->value('inventoryEndId')
                ->plain()
                ->get();
            if ($inventoryEnd->count != 0) {
                $invEndId = $inventoryEnd->data->get(0)->inventoryEndId;
                $inventoryHistory = InventoryHistory::where(
                    'hospitalId',
                    $user_info->getHospitalId()
                )
                    ->where('divisionId', $divisionId)
                    ->where('inventoryEndId', $invEndId)
                    ->value('inventoryHStatus')
                    ->plain()
                    ->get();
                if ($inventoryHistory->count != 0) {
                    throw new Exception(
                        'すでに登録された棚卸情報があるため、取得しませんでした',
                        1
                    );
                }
            }

            $stock = StockView::where('hospitalId', $user_info->getHospitalId())
                ->where('divisionId', $divisionId)
                ->where('stockQuantity', 0, '>')
                ->plain()
                ->get();

            $lot = Lot::where('hospitalId', $user_info->getHospitalId())
                ->where('divisionId', $divisionId)
                ->where('stockQuantity', 0, '>')
                ->plain()
                ->get();

            $list = [];

            $in_hospital_item = InHospitalItemView::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->plain()
                ->value('inHospitalItemId')
                ->value('lotManagement');

            foreach ($stock->data->all() as $stock_item) {
                $in_hospital_item->orWhere(
                    'inHospitalItemId',
                    $stock_item->inHospitalItemId
                );
            }
            foreach ($lot->data->all() as $lot_item) {
                $in_hospital_item->orWhere(
                    'inHospitalItemId',
                    $lot_item->inHospitalItemId
                );
            }

            $in_hospital_item = $in_hospital_item->get();
            $data = [];
            foreach ($stock->data->all() as $stock_item) {
                $stockQuantity = (int) $stock_item->stockQuantity;
                $lotManagement = 0;
                foreach ($in_hospital_item->data->all() as $in_hp_item) {
                    if (
                        $in_hp_item->inHospitalItemId ==
                        $stock_item->inHospitalItemId
                    ) {
                        $lotManagement = $in_hp_item->lotManagement;
                        break;
                    }
                }

                foreach ($lot->data->all() as $lot_item) {
                    if (
                        $lot_item->inHospitalItemId ==
                        $stock_item->inHospitalItemId
                    ) {
                        $stockQuantity =
                            $stockQuantity - (int) $lot_item->stockQuantity;
                        $data[] = [
                            'divisionId' => '',
                            'maker' => $stock_item->makerName,
                            'shouhinName' => $stock_item->itemName,
                            'code' => $stock_item->itemCode,
                            'kikaku' => $stock_item->itemStandard,
                            'irisu' => $stock_item->quantity,
                            'kakaku' => $stock_item->price,
                            'jan' => $stock_item->itemJANCode,
                            'oroshi' => $stock_item->distributorName,
                            'recordId' => $stock_item->inHospitalItemId,
                            'unit' => $stock_item->quantityUnit,
                            'itemUnit' => $stock_item->itemUnit,
                            'distributorId' => $stock_item->distributorId,
                            'count' => (int) $lot_item->stockQuantity,
                            'countNum' => (int) $lot_item->stockQuantity,
                            'labelId' => $stock_item->labelId,
                            'unitPrice' => $stock_item->unitPrice,
                            'lotNumber' => $lot_item->lotNumber,
                            'lotDate' => \App\Lib\changeDateFormat(
                                'Y年m月d日',
                                $lot_item->lotDate,
                                'Y-m-d'
                            ),
                            'lotFlag' => $lotManagement == 1 ? 'はい' : '',
                            'lotFlagBool' => $lotManagement,
                        ];
                    }
                }

                if ($stockQuantity != 0) {
                    $data[] = [
                        'divisionId' => '',
                        'maker' => $stock_item->makerName,
                        'shouhinName' => $stock_item->itemName,
                        'code' => $stock_item->itemCode,
                        'kikaku' => $stock_item->itemStandard,
                        'irisu' => $stock_item->quantity,
                        'kakaku' => $stock_item->price,
                        'jan' => $stock_item->itemJANCode,
                        'oroshi' => $stock_item->distributorName,
                        'recordId' => $stock_item->inHospitalItemId,
                        'unit' => $stock_item->quantityUnit,
                        'itemUnit' => $stock_item->itemUnit,
                        'distributorId' => $stock_item->distributorId,
                        'count' => (int) $stockQuantity,
                        'countNum' => (int) $stockQuantity,
                        'labelId' => $stock_item->labelId,
                        'unitPrice' => $stock_item->unitPrice,
                        'lotNumber' => '',
                        'lotDate' => '',
                        'lotFlag' => $lotManagement == 1 ? 'はい' : '',
                        'lotFlagBool' => $lotManagement,
                    ];
                }
            }

            $content = new ApiResponse($data, count($data), 0, 'OK', [
                'getLotAndStockApi',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['getLotAndStockApi']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function inventoryEndList()
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = [];
        try {
            $user_info = new UserInfo($SPIRAL);

            $head = $this->view(
                'NewJoyPla/view/template/parts/Head',
                [],
                false
            );
            $header = $this->view(
                'NewJoyPla/src/HeaderForMypage',
                [
                    'SPIRAL' => $SPIRAL,
                ],
                false
            );

            if ($user_info->isDistributorUser()) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $content = $this->view(
                'NewJoyPla/view/InventoryHistoryList',
                [
                    //'api_url' => $api_url,
                    'user_info' => $user_info,
                    'csrf_token' => Csrf::generate(16),
                ],
                false
            );
        } catch (Exception $ex) {
            $content = $this->view(
                'NewJoyPla/view/template/Error',
                [
                    'code' => $ex->getCode(),
                    'message' => $ex->getMessage(),
                ],
                false
            );
        } finally {
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view(
                'NewJoyPla/view/template/Template',
                [
                    'title' => 'JoyPla 棚卸履歴一覧',
                    'script' => '',
                    'content' => $content->render(),
                    'head' => $head->render(),
                    'header' => $header->render(),
                    'baseUrl' => '',
                ],
                false
            );
        }
    }

    public function inventoryMovement($SPIRAL)
    {
        //$stock = Distributor::value('distributorId')->plain()->get();

        $user_info = new UserInfo($SPIRAL);

        $hospital = Hospital::where('hospitalId', $user_info->getHospitalId())
            ->value('hospitalName')
            ->plain()
            ->get();
        $hospital = $hospital->data->get(0);

        if ($user_info->isUser()) {
            $division = Division::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->where('divisionId', $user_info->getDivisionId())
                ->value('divisionId')
                ->value('divisionName')
                ->plain()
                ->get();
            $division = $division->data->all();
        } else {
            $division = Division::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->value('divisionId')
                ->value('divisionName')
                ->plain()
                ->get();
            $division = $division->data->all();
        }

        $content = $this->view(
            'NewJoyPla/view/InventoryMovement',
            [
                'csrf_token' => Csrf::generate(16),
                'division' => $division,
                'hospitalName' => $hospital->hospitalName,
            ],
            false
        );

        $style = $this->view(
            'NewJoyPla/view/template/parts/DetailPrintCss',
            [],
            false
        )->render();
        $style .= $this->view(
            'NewJoyPla/view/template/parts/StyleCss',
            [],
            false
        )->render();

        $script = $this->view(
            'NewJoyPla/view/template/parts/Script',
            [],
            false
        )->render();
        $head = $this->view(
            'NewJoyPla/view/template/parts/Head',
            ['new' => true],
            false
        );
        $header = $this->view(
            'NewJoyPla/src/HeaderForMypage',
            [
                'SPIRAL' => $SPIRAL,
            ],
            false
        );

        return $this->view(
            'NewJoyPla/view/template/Template',
            [
                'title' => 'JoyPla 棚卸実績',
                'content' => $content->render(),
                'style' => $style,
                'script' => $script,
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],
            false
        );
    }

    public function inventoryMovementAll($SPIRAL)
    {
        //$stock = Distributor::value('distributorId')->plain()->get();

        $user_info = new UserInfo($SPIRAL);

        $hospital = Hospital::where('hospitalId', $user_info->getHospitalId())
            ->value('hospitalName')
            ->plain()
            ->get();

        $hospital = $hospital->data->get(0);

        if ($user_info->isUser()) {
            throw new Exception('Not Found.', 404);
        }

        $content = InventoryEnd::where(
            'hospitalId',
            $user_info->getHospitalId()
        )
            ->sort('id', 'desc')
            ->value('inventoryTime')
            ->value('inventoryStatus')
            ->value('inventoryEndId')
            ->plain()
            ->get();

        $data = [];

        foreach ($content->data->all() as $key => $c) {
            $date = $c->inventoryTime;
            if ($c->inventoryTime === '') {
                $date = date('Y年m月d日 H時i分s秒');
            }
            $data[] = [
                'inventoryTime' => $c->inventoryTime,
                'inventoryStatus' => $c->inventoryStatus,
                'inventoryEndId' => $c->inventoryEndId,
                'searchStartDate' => '',
                'searchEndDate' => $date,
            ];
        }

        foreach ($data as $key => &$d) {
            if (array_key_exists($key + 1, $data)) {
                $data[$key]['searchStartDate'] =
                    $data[$key + 1]['searchEndDate'];
            }
        }

        $content = $this->view(
            'NewJoyPla/view/InventoryMovementAll',
            [
                'csrf_token' => Csrf::generate(16),
                'historys' => $data,
                'hospitalName' => $hospital->hospitalName,
            ],
            false
        );

        $style = $this->view(
            'NewJoyPla/view/template/parts/DetailPrintCss',
            [],
            false
        )->render();
        $style .= $this->view(
            'NewJoyPla/view/template/parts/StyleCss',
            [],
            false
        )->render();

        $script = $this->view(
            'NewJoyPla/view/template/parts/Script',
            [],
            false
        )->render();
        $head = $this->view(
            'NewJoyPla/view/template/parts/Head',
            ['new' => true],
            false
        );
        $header = $this->view(
            'NewJoyPla/src/HeaderForMypage',
            [
                'SPIRAL' => $SPIRAL,
            ],
            false
        );

        return $this->view(
            'NewJoyPla/view/template/Template',
            [
                'title' => 'JoyPla 棚卸実績',
                'content' => $content->render(),
                'style' => $style,
                'script' => $script,
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],
            false
        );
    }

    public function hospitalItemsSelectApi($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $user_info = new UserInfo($SPIRAL);

            $hospitalId = $user_info->getHospitalId();
            //$divisionId = $SPIRAL->getParam('divisionId');
            //if($divisionId === '' || $hospitalId === '')
            if ($hospitalId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $hospital = Hospital::where('hospitalId', $hospitalId)
                ->value('hospitalName')
                ->value('hospitalId')
                ->value('invUnitPrice')
                ->plain()
                ->get();

            if ($hospital->count == 0) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $hospital = $hospital->data->get(0);

            $content = StockView::where('hospitalId', $hospital->hospitalId)
                ->sort('id', 'desc')
                ->value('inHospitalItemId')
                ->value('rackName')
                ->value('distributorName')
                ->value('divisionName')
                ->value('divisionId')
                ->plain()
                ->get();

            $data = $content->data->all();

            $inHospitalItems = InHospitalItemView::where(
                'hospitalId',
                $hospital->hospitalId
            )
                ->plain()
                ->value('inHospitalItemId')
                ->value('itemName')
                ->value('itemCode')
                ->value('itemStandard')
                ->value('itemJANCode')
                ->value('makerName')
                ->value('price')
                ->value('unitPrice')
                ->value('quantity')
                ->value('quantityUnit')
                ->value('category');

            foreach ($data as $d) {
                $inHospitalItems->orWhere(
                    'inHospitalItemId',
                    $d->inHospitalItemId
                );
            }

            $inHospitalItems = $inHospitalItems->get();
            $inHospitalItemsLabel = $inHospitalItems->label->all();
            foreach ($data as $key => $d) {
                foreach ($inHospitalItems->data->all() as $item) {
                    if ($item->inHospitalItemId == $d->inHospitalItemId) {
                        $data[$key]->itemName = $item->itemName;
                        $data[$key]->itemCode = $item->itemCode;
                        $data[$key]->itemStandard = $item->itemStandard;
                        $data[$key]->itemJANCode = $item->itemJANCode;
                        $data[$key]->makerName = $item->makerName;
                        $data[$key]->price = (float) $item->price;
                        $data[$key]->unitPrice = (float) $item->unitPrice;
                        $data[$key]->quantity = $item->quantity;
                        $data[$key]->quantityUnit = $item->quantityUnit;
                        $data[$key]->category = $item->category;
                        $data[$key]->categoryToString = $inHospitalItemsLabel[
                            'category'
                        ]->get($item->category);
                        break;
                    }
                }
                if ($hospital->invUnitPrice != '1') {
                    $data[$key]->unitPrice = 0;
                    if ((int) $d->quantity !== 0 && (int) $d->price !== 0) {
                        $data[$key]->unitPrice =
                            (int) $d->price / (int) $d->quantity;
                    }
                }
            }

            $content = new ApiResponse($data, $content->count, 0, 'OK', [
                'hospitalItemsSelectApi',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['hospitalItemsSelectApi']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function getInventoryItemNumsAllDivisionsApi($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $user_info = new UserInfo($SPIRAL);
            $hospitalId = $user_info->getHospitalId();
            //$divisionId = $SPIRAL->getParam('divisionId');
            //if($divisionId === '' || $hospitalId === '')
            if ($hospitalId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $hospital = Hospital::where('hospitalId', $hospitalId)
                ->value('hospitalName')
                ->value('hospitalId')
                ->plain()
                ->get();
            if ($hospital->count == 0) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }
            $hospital = $hospital->data->get(0);

            $inventoryEndId = $SPIRAL->getParam('inventoryEndId');
            if ($inventoryEndId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $content = Inventory::where('inventoryEndId', $inventoryEndId)
                ->where('hospitalId', $hospital->hospitalId)
                ->sort('id', 'desc')
                ->value('inHospitalItemId')
                ->value('inventryNum')
                ->value('divisionId')
                ->plain()
                ->get();

            $data = [];
            $data['record'] = [];
            foreach ($content->data->all() as $d) {
                $check = array_column($data['record'], 'uniqKey');
                $key = array_search(
                    $d->divisionId . '_' . $d->inHospitalItemId,
                    $check
                );
                if ($key === false) {
                    $data['record'][] = [
                        'inHospitalItemId' => $d->inHospitalItemId,
                        'count' => (int) $d->inventryNum,
                        'divisionId' => $d->divisionId,
                        'uniqKey' =>
                            $d->divisionId . '_' . $d->inHospitalItemId,
                    ];
                } else {
                    $data['record'][$key]['count'] =
                        $data['record'][$key]['count'] + (int) $d->inventryNum;
                }
            }

            $content = new ApiResponse($data, $content->count, 0, 'OK', [
                'getInventoryItemNumsApi',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['getInventoryItemNumsApi']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function divisonInventorySelectApi($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $user_info = new UserInfo($SPIRAL);
            $divisionId = $SPIRAL->getParam('divisionId');
            if ($divisionId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            if (
                $user_info->isUser() &&
                $user_info->getDivisionId() != $divisionId
            ) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $content = InventoryHistoryDivisionView::where(
                'divisionId',
                $divisionId
            )
                ->where('hospitalId', $user_info->getHospitalId())
                ->sort('id', 'desc')
                ->value('inventoryTime')
                ->value('inventoryStatus')
                ->value('inventoryHId')
                ->plain()
                ->get();

            $data = [];

            foreach ($content->data->all() as $key => $c) {
                $date = $c->inventoryTime;
                if ($c->inventoryTime === '') {
                    $date = date('Y年m月d日 H時i分s秒');
                }
                $data[] = [
                    'inventoryTime' => $c->inventoryTime,
                    'inventoryStatus' => $c->inventoryStatus,
                    'inventoryHId' => $c->inventoryHId,
                    'searchStartDate' => '',
                    'searchEndDate' => $date,
                ];
            }

            foreach ($data as $key => &$d) {
                if (array_key_exists($key + 1, $data)) {
                    $data[$key]['searchStartDate'] =
                        $data[$key + 1]['searchEndDate'];
                }
            }

            $content = new ApiResponse($data, $content->count, 0, 'OK', ['']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function divisonItemsSelectApi($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $user_info = new UserInfo($SPIRAL);
            $divisionId = $SPIRAL->getParam('divisionId');
            if ($divisionId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }
            if (
                $user_info->isUser() &&
                $user_info->getDivisionId() != $divisionId
            ) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $hospital = Hospital::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->value('invUnitPrice')
                ->plain()
                ->get();
            $hospital = $hospital->data->get(0);

            $content = StockView::where('divisionId', $divisionId)
                ->where('hospitalId', $user_info->getHospitalId())
                ->sort('id', 'desc')
                ->value('inHospitalItemId')
                ->value('rackName')
                ->value('distributorName')
                ->plain()
                ->get();

            $data = $content->data->all();

            $inHospitalItems = InHospitalItemView::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->plain()
                ->value('inHospitalItemId')
                ->value('itemName')
                ->value('itemCode')
                ->value('itemStandard')
                ->value('itemJANCode')
                ->value('makerName')
                ->value('price')
                ->value('unitPrice')
                ->value('quantity')
                ->value('quantityUnit')
                ->value('category');

            foreach ($data as $d) {
                $inHospitalItems->orWhere(
                    'inHospitalItemId',
                    $d->inHospitalItemId
                );
            }

            $inHospitalItems = $inHospitalItems->get();
            $inHospitalItemsLabel = $inHospitalItems->label->all();
            foreach ($data as $key => $d) {
                foreach ($inHospitalItems->data->all() as $item) {
                    if ($item->inHospitalItemId == $d->inHospitalItemId) {
                        $data[$key]->itemName = $item->itemName;
                        $data[$key]->itemCode = $item->itemCode;
                        $data[$key]->itemStandard = $item->itemStandard;
                        $data[$key]->itemJANCode = $item->itemJANCode;
                        $data[$key]->makerName = $item->makerName;
                        $data[$key]->price = (float) $item->price;
                        $data[$key]->unitPrice = (float) $item->unitPrice;
                        $data[$key]->quantity = $item->quantity;
                        $data[$key]->quantityUnit = $item->quantityUnit;
                        $data[$key]->category = $item->category;
                        $data[$key]->categoryToString = $inHospitalItemsLabel[
                            'category'
                        ]->get($item->category);
                        break;
                    }
                }
                if ($hospital->invUnitPrice != '1') {
                    $data[$key]->unitPrice = 0;
                    if ((int) $d->quantity !== 0 && (int) $d->price !== 0) {
                        $data[$key]->unitPrice =
                            (int) $d->price / (int) $d->quantity;
                    }
                }
            }

            $content = new ApiResponse($data, $content->count, 0, 'OK', [
                'divisonItemsSelectApi',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['divisonItemsSelectApi']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function getInventoryItemNumsApi($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $user_info = new UserInfo($SPIRAL);
            $inventoryHId = $SPIRAL->getParam('inventoryHId');
            if ($inventoryHId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $hospital = Hospital::where(
                'hospitalId',
                $user_info->getHospitalId()
            )
                ->value('invUnitPrice')
                ->plain()
                ->get();
            $hospital = $hospital->data->get(0);

            $content = Inventory::where('inventoryHId', $inventoryHId)
                ->where('hospitalId', $user_info->getHospitalId())
                ->sort('id', 'desc')
                ->value('inHospitalItemId')
                ->value('inventryNum')
                ->value('divisionId')
                ->plain()
                ->get();

            $data = [];
            $data['record'] = [];
            foreach ($content->data->all() as $d) {
                if (
                    $user_info->isUser() &&
                    $user_info->getDivisionId() != $d->divisionId
                ) {
                    throw new Exception(
                        FactoryApiErrorCode::factory(191)->getMessage(),
                        FactoryApiErrorCode::factory(191)->getCode()
                    );
                }

                $check = array_column($data['record'], 'inHospitalItemId');
                $key = array_search($d->inHospitalItemId, $check);
                if ($key === false) {
                    $data['record'][] = [
                        'inHospitalItemId' => $d->inHospitalItemId,
                        'count' => (int) $d->inventryNum,
                    ];
                } else {
                    $data['record'][$key]['count'] =
                        $data['record'][$key]['count'] + (int) $d->inventryNum;
                }
            }

            $content = new ApiResponse($data, $content->count, 0, 'OK', [
                'getInventoryItemNumsApi',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['getInventoryItemNumsApi']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function getBeforeInventoryItemNumsApi($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $user_info = new UserInfo($SPIRAL);
            $inventoryHId = $SPIRAL->getParam('inventoryHId');
            $divisionId = $SPIRAL->getParam('divisionId');
            if ($inventoryHId === '' || $divisionId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }
            if (
                $user_info->isUser() &&
                $user_info->getDivisionId() != $divisionId
            ) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $content = InventoryHistoryDivisionView::where(
                'divisionId',
                $divisionId
            )
                ->where('hospitalId', $user_info->getHospitalId())
                ->where('inventoryHId', $inventoryHId, '<')
                ->sort('id', 'desc')
                ->value('inventoryTime')
                ->value('inventoryStatus')
                ->value('inventoryHId')
                ->plain()
                ->get();

            $data = [];
            $data['record'] = [];
            $data['date'] = '';
            if ($content->count > 0) {
                $before = $content->data->get(0);
                $beforeInventoryHId = $before->inventoryHId;
                $data['date'] = $before->inventoryTime;

                $hospital = Hospital::where(
                    'hospitalId',
                    $user_info->getHospitalId()
                )
                    ->value('invUnitPrice')
                    ->plain()
                    ->get();
                $hospital = $hospital->data->get(0);

                $content = Inventory::where('inventoryHId', $beforeInventoryHId)
                    ->where('hospitalId', $user_info->getHospitalId())
                    ->sort('id', 'desc')
                    ->value('inHospitalItemId')
                    ->value('inventryNum')
                    ->plain()
                    ->get();

                foreach ($content->data->all() as $d) {
                    $check = array_column($data['record'], 'inHospitalItemId');
                    $key = array_search($d->inHospitalItemId, $check);
                    if ($key === false) {
                        $data['record'][] = [
                            'inHospitalItemId' => $d->inHospitalItemId,
                            'count' => (int) $d->inventryNum,
                        ];
                    } else {
                        $data['record'][$key]['count'] =
                            $data['record'][$key]['count'] +
                            (int) $d->inventryNum;
                    }
                }
            }

            $content = new ApiResponse($data, $content->count, 0, 'OK', [
                'getBeforeInventoryItemNumsApi',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['getBeforeInventoryItemNumsApi']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function getBeforeInventoryItemNumsAllDivisionsApi($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $user_info = new UserInfo($SPIRAL);
            $hospitalId = $user_info->getHospitalId();
            //$divisionId = $SPIRAL->getParam('divisionId');
            if ($hospitalId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $hospital = Hospital::where('hospitalId', $hospitalId)
                ->value('hospitalName')
                ->value('hospitalId')
                ->plain()
                ->get();
            if ($hospital->count == 0) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }
            $hospital = $hospital->data->get(0);

            $inventoryEndId = $SPIRAL->getParam('inventoryEndId');
            if ($inventoryEndId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $content = InventoryEnd::where(
                'inventoryEndId',
                $inventoryEndId,
                '!='
            )
                ->where('hospitalId', $hospital->hospitalId)
                ->sort('id', 'desc')
                ->value('inventoryTime')
                ->value('inventoryStatus')
                ->value('inventoryEndId')
                ->plain()
                ->get();

            $data = [];
            $data['record'] = [];
            $data['date'] = '';
            if ($content->count > 0) {
                $before = $content->data->get(0);
                $beforeInventoryEndId = $before->inventoryEndId;
                $data['date'] = $before->inventoryTime;

                $hospital = Hospital::where('hospitalId', $hospital->hospitalId)
                    ->value('invUnitPrice')
                    ->plain()
                    ->get();
                $hospital = $hospital->data->get(0);

                $content = Inventory::where(
                    'inventoryEndId',
                    $beforeInventoryEndId
                )
                    ->where('hospitalId', $hospital->hospitalId)
                    ->sort('id', 'desc')
                    ->value('inHospitalItemId')
                    ->value('inventryNum')
                    ->value('divisionId')
                    ->plain()
                    ->get();

                foreach ($content->data->all() as $d) {
                    $check = array_column($data['record'], 'uniqKey');
                    $key = array_search(
                        $d->divisionId . '_' . $d->inHospitalItemId,
                        $check
                    );
                    if ($key === false) {
                        $data['record'][] = [
                            'inHospitalItemId' => $d->inHospitalItemId,
                            'count' => (int) $d->inventryNum,
                            'divisionId' => $d->divisionId,
                            'uniqKey' =>
                                $d->divisionId . '_' . $d->inHospitalItemId,
                        ];
                    } else {
                        $data['record'][$key]['count'] =
                            $data['record'][$key]['count'] +
                            (int) $d->inventryNum;
                    }
                }
            }
            $content = new ApiResponse($data, $content->count, 0, 'OK', [
                'getBeforeInventoryItemNumsApi',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['getBeforeInventoryItemNumsApi']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function getReceivingItemNumsAllDivisionsApi($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $user_info = new UserInfo($SPIRAL);
            $hospitalId = $user_info->getHospitalId();
            //$divisionId = $SPIRAL->getParam('divisionId');
            if ($hospitalId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $hospital = Hospital::where('hospitalId', $hospitalId)
                ->value('hospitalName')
                ->value('hospitalId')
                ->plain()
                ->get();
            if ($hospital->count == 0) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }
            $hospital = $hospital->data->get(0);

            $startDate = $SPIRAL->getParam('startDate');
            $endDate = $SPIRAL->getParam('endDate');

            $content = ReceivingView::where('hospitalId', $hospital->hospitalId)
                ->sort('id', 'desc')
                ->value('inHospitalItemId')
                ->value('receivingCount')
                ->value('priceAfterAdj')
                ->value('quantity')
                ->value('divisionId')
                ->plain();

            if ($startDate) {
                $content = $content->where(
                    'registrationTime',
                    urldecode($startDate),
                    '>='
                );
            }
            if ($endDate) {
                $content = $content->where(
                    'registrationTime',
                    urldecode($endDate),
                    '<='
                );
            }

            $content = $content->get();
            $data = [];
            $data['record'] = [];

            if ($content->count > 0) {
                foreach ($content->data->all() as $d) {
                    $check = array_column($data['record'], 'uniqKey');
                    $key = array_search(
                        $d->divisionId . '_' . $d->inHospitalItemId,
                        $check
                    );
                    if ($key === false) {
                        $data['record'][] = [
                            'inHospitalItemId' => $d->inHospitalItemId,
                            'divisionId' => $d->divisionId,
                            'count' =>
                                (int) $d->receivingCount * (int) $d->quantity,
                            'price' => (float) $d->priceAfterAdj,
                            'uniqKey' =>
                                $d->divisionId . '_' . $d->inHospitalItemId,
                        ];
                    } else {
                        $data['record'][$key]['count'] =
                            $data['record'][$key]['count'] +
                            (int) $d->receivingCount * (int) $d->quantity;
                        $data['record'][$key]['price'] =
                            $data['record'][$key]['price'] +
                            (float) $d->priceAfterAdj;
                    }
                }
            }

            $content = new ApiResponse($data, $content->count, 0, 'OK', [
                'getReceivingItemNumsApi',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['getReceivingItemNumsApi']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function getReceivingItemNumsApi($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $user_info = new UserInfo($SPIRAL);
            $divisionId = $SPIRAL->getParam('divisionId');

            $startDate = $SPIRAL->getParam('startDate');
            $endDate = $SPIRAL->getParam('endDate');

            if ($divisionId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }
            if (
                $user_info->isUser() &&
                $user_info->getDivisionId() != $divisionId
            ) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $content = ReceivingView::where('divisionId', $divisionId)
                ->where('hospitalId', $user_info->getHospitalId())
                ->sort('id', 'desc')
                ->value('inHospitalItemId')
                ->value('receivingCount')
                ->value('priceAfterAdj')
                ->value('quantity')
                ->plain();

            if ($startDate) {
                $content = $content->where(
                    'registrationTime',
                    urldecode($startDate),
                    '>='
                );
            }
            if ($endDate) {
                $content = $content->where(
                    'registrationTime',
                    urldecode($endDate),
                    '<='
                );
            }

            $content = $content->get();
            $data = [];
            $data['record'] = [];

            if ($content->count > 0) {
                foreach ($content->data->all() as $d) {
                    $check = array_column($data['record'], 'inHospitalItemId');
                    $key = array_search($d->inHospitalItemId, $check);
                    if ($key === false) {
                        $data['record'][] = [
                            'inHospitalItemId' => $d->inHospitalItemId,
                            'count' =>
                                (int) $d->receivingCount * (int) $d->quantity,
                            'price' => (float) $d->priceAfterAdj,
                        ];
                    } else {
                        $data['record'][$key]['count'] =
                            $data['record'][$key]['count'] +
                            (int) $d->receivingCount * (int) $d->quantity;
                        $data['record'][$key]['price'] =
                            $data['record'][$key]['price'] +
                            (float) $d->priceAfterAdj;
                    }
                }
            }

            $content = new ApiResponse($data, $content->count, 0, 'OK', [
                'getReceivingItemNumsApi',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['getReceivingItemNumsApi']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function getConsumedItemNumsApi($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $user_info = new UserInfo($SPIRAL);
            $divisionId = $SPIRAL->getParam('divisionId');

            $startDate = $SPIRAL->getParam('startDate');
            $endDate = $SPIRAL->getParam('endDate');

            if ($divisionId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }
            if (
                $user_info->isUser() &&
                $user_info->getDivisionId() != $divisionId
            ) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $content = Billing::where('divisionId', $divisionId)
                ->where('hospitalId', $user_info->getHospitalId())
                ->sort('id', 'desc')
                ->value('inHospitalItemId')
                ->value('billingQuantity')
                ->value('billingAmount')
                ->value('quantity')
                ->plain();

            if ($startDate) {
                $content = $content->where(
                    'registrationTime',
                    urldecode($startDate),
                    '>='
                );
            }
            if ($endDate) {
                $content = $content->where(
                    'registrationTime',
                    urldecode($endDate),
                    '<='
                );
            }

            $content = $content->get();
            $data = [];
            $data['record'] = [];
            if ($content->count > 0) {
                foreach ($content->data->all() as $d) {
                    $check = array_column($data['record'], 'inHospitalItemId');
                    $key = array_search($d->inHospitalItemId, $check);
                    if ($key === false) {
                        $data['record'][] = [
                            'inHospitalItemId' => $d->inHospitalItemId,
                            'count' => (int) $d->billingQuantity,
                            'price' => (float) $d->billingAmount,
                        ];
                    } else {
                        $data['record'][$key]['count'] =
                            $data['record'][$key]['count'] +
                            (int) $d->billingQuantity;
                        $data['record'][$key]['price'] =
                            $data['record'][$key]['price'] +
                            (float) $d->billingAmount;
                    }
                }
            }

            $content = new ApiResponse($data, $content->count, 0, 'OK', [
                'getConsumedItemNumsApi',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['getConsumedItemNumsApi']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function getConsumedItemNumsAllDivisionsApi($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $user_info = new UserInfo($SPIRAL);
            $hospitalId = $user_info->getHospitalId();
            //$divisionId = $SPIRAL->getParam('divisionId');
            if ($hospitalId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $hospital = Hospital::where('hospitalId', $hospitalId)
                ->value('hospitalName')
                ->value('hospitalId')
                ->plain()
                ->get();
            if ($hospital->count == 0) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }
            $hospital = $hospital->data->get(0);

            $startDate = $SPIRAL->getParam('startDate');
            $endDate = $SPIRAL->getParam('endDate');

            $content = Billing::where('hospitalId', $hospital->hospitalId)
                ->sort('id', 'desc')
                ->value('divisionId')
                ->value('inHospitalItemId')
                ->value('billingQuantity')
                ->value('billingAmount')
                ->value('quantity')
                ->plain();

            if ($startDate) {
                $content = $content->where(
                    'registrationTime',
                    urldecode($startDate),
                    '>='
                );
            }
            if ($endDate) {
                $content = $content->where(
                    'registrationTime',
                    urldecode($endDate),
                    '<='
                );
            }

            $content = $content->get();
            $data = [];
            $data['record'] = [];
            if ($content->count > 0) {
                foreach ($content->data->all() as $d) {
                    $check = array_column($data['record'], 'uniqKey');
                    $key = array_search(
                        $d->divisionId . '_' . $d->inHospitalItemId,
                        $check
                    );
                    if ($key === false) {
                        $data['record'][] = [
                            'inHospitalItemId' => $d->inHospitalItemId,
                            'count' => (int) $d->billingQuantity,
                            'price' => (float) $d->billingAmount,
                            'divisionId' => $d->divisionId,
                            'uniqKey' =>
                                $d->divisionId . '_' . $d->inHospitalItemId,
                        ];
                    } else {
                        $data['record'][$key]['count'] =
                            $data['record'][$key]['count'] +
                            (int) $d->billingQuantity;
                        $data['record'][$key]['price'] =
                            $data['record'][$key]['price'] +
                            (float) $d->billingAmount;
                    }
                }
            }

            $content = new ApiResponse($data, $content->count, 0, 'OK', [
                'getConsumedItemNumsApi',
            ]);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['getConsumedItemNumsApi']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }

    public function getRackNames($SPIRAL)
    {
        try {
            $token = !isset($_POST['_csrf']) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);
            $divisionId = $SPIRAL->getParam('divisionId');

            if ($divisionId === '') {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }
            if (
                $user_info->isUser() &&
                $user_info->getDivisionId() != $divisionId
            ) {
                throw new Exception(
                    FactoryApiErrorCode::factory(191)->getMessage(),
                    FactoryApiErrorCode::factory(191)->getCode()
                );
            }

            $content = Stock::where('divisionId', $divisionId)
                ->where('hospitalId', $user_info->getHospitalId())
                ->sort('id', 'desc')
                ->value('inHospitalItemId')
                ->value('rackName')
                ->plain();

            $content = $content->get();
            $data = [];

            foreach ($content->data->all() as $item) {
                $data[] = [
                    'inHospitalItemId' => $item->inHospitalItemId,
                    'rackName' => $item->rackName,
                ];
            }

            $content = new ApiResponse($data, $content->count, 0, 'OK', ['']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse(
                [],
                0,
                $ex->getCode(),
                $ex->getMessage(),
                ['']
            );
            $content = $content->toJson();
        } finally {
            return $this->view(
                'NewJoyPla/view/template/ApiResponse',
                [
                    'content' => $content,
                ],
                false
            );
        }
    }
}

/***
 * 実行
 */
$InventoryController = new InventoryController();

$action = $SPIRAL->getParam('Action');

if ($action === 'inventoryRegistApi') {
    echo $InventoryController->inventoryRegistAPI()->render();
} elseif ($action === 'getTemporaryData') {
    echo $InventoryController->getTemporaryData()->render();
} elseif ($action === 'getLotAndStockApi') {
    echo $InventoryController->getLotAndStockApi()->render();
} elseif ($action === 'inventoryEndList') {
    echo $InventoryController->inventoryEndList()->render();
} elseif ($action === 'inventoryMovement') {
    echo $InventoryController->inventoryMovement($SPIRAL)->render();
} elseif ($action === 'inventoryMovementAll') {
    echo $InventoryController->inventoryMovementAll($SPIRAL)->render();
} elseif ($action === 'divisonInventorySelectApi') {
    echo $InventoryController->divisonInventorySelectApi($SPIRAL)->render();
} elseif ($action === 'divisonItemsSelectApi') {
    echo $InventoryController->divisonItemsSelectApi($SPIRAL)->render();
} elseif ($action === 'getInventoryItemNumsApi') {
    echo $InventoryController->getInventoryItemNumsApi($SPIRAL)->render();
} elseif ($action === 'getInventoryItemNumsAllDivisionsApi') {
    echo $InventoryController
        ->getInventoryItemNumsAllDivisionsApi($SPIRAL)
        ->render();
} elseif ($action === 'getBeforeInventoryItemNumsAllDivisionsApi') {
    echo $InventoryController
        ->getBeforeInventoryItemNumsAllDivisionsApi($SPIRAL)
        ->render();
} elseif ($action === 'getReceivingItemNumsAllDivisionsApi') {
    echo $InventoryController
        ->getReceivingItemNumsAllDivisionsApi($SPIRAL)
        ->render();
} elseif ($action === 'getBeforeInventoryItemNumsApi') {
    echo $InventoryController->getBeforeInventoryItemNumsApi($SPIRAL)->render();
} elseif ($action === 'getReceivingItemNumsApi') {
    echo $InventoryController->getReceivingItemNumsApi($SPIRAL)->render();
} elseif ($action === 'getConsumedItemNumsAllDivisionsApi') {
    echo $InventoryController
        ->getConsumedItemNumsAllDivisionsApi($SPIRAL)
        ->render();
} elseif ($action === 'getConsumedItemNumsApi') {
    echo $InventoryController->getConsumedItemNumsApi($SPIRAL)->render();
} elseif ($action == 'getRackNames') {
    echo $InventoryController->getRackNames($SPIRAL)->render();
} elseif ($action == 'hospitalItemsSelectApi') {
    echo $InventoryController->hospitalItemsSelectApi($SPIRAL)->render();
} else {
    echo $InventoryController->index()->render();
}
