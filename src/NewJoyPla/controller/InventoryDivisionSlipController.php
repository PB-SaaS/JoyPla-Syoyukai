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
use App\Model\Inventory;
use App\Model\InventoryEnd;
use App\Model\InventoryHistory;
use App\Model\InventoryItemView;
use App\Model\StockView;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class InventoryDivisionSlipController extends Controller
{
    public function __construct()
    {
    }

    public function itemIndex(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = array();
        try {
            $user_info = new UserInfo($SPIRAL);

            $record_id = (int)$SPIRAL->getCardId();

            $division_history_slip = InventoryHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->get();
            $division_history_slip = $division_history_slip->data->get(0);

            $end_slip = InventoryEnd::where('hospitalId', $user_info->getHospitalId())->where('inventoryEndId', $division_history_slip->inventoryEndId)->get();
            $end_slip = $end_slip->data->get(0);

            $inventory_items = InventoryItemView::where('hospitalId', $user_info->getHospitalId())->where('inventoryHId', $division_history_slip->inventoryHId)->get();
            $inventory_items = $inventory_items->data->all();

            $inventory_remake_items = [];
            /*
            $stock = StockView::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$division_history_slip->divisionId);

            foreach($inventory_items->data->all() as $item )
            {
                $stock->orWhere('inHospitalItemId',$item->inHospitalItemId);
            }

            $stock = $stock->get();
            */
            if ($end_slip->inventoryStatus != 2) {
                //$prices = [];
                $total_amount = 0;
                $in_hospital_item_ids = [];
                foreach ($inventory_items as $inventory_item) {
                    //$prices[] = (float)$inventory_item->inventryAmount;
                    $total_amount = $total_amount + (float)$inventory_item->inventryAmount;
                    if (!in_array($inventory_item->inHospitalItemId, $in_hospital_item_ids)) {
                        $in_hospital_item_ids[] = $inventory_item->inHospitalItemId;
                    }
                }

                $update_time = $division_history_slip->updateTime;
                if ($division_history_slip->totalAmount != $total_amount || $division_history_slip->itemsNumber != collect($in_hospital_item_ids)->count()) {
                    $update_time = "now";
                }

                InventoryHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->update([
                    //"totalAmount" => collect($prices)->sum(),
                    "updateTime" => $update_time,
                    "totalAmount" => $total_amount,
                    "itemsNumber" => collect($in_hospital_item_ids)->count(),
                ]);

                $history_items = Inventory::where('hospitalId', $user_info->getHospitalId())->where('inventoryEndId', $division_history_slip->inventoryEndId)->get();

                //$prices = [];
                $total_amount = 0;
                $in_hospital_item_ids = [];
                foreach ($history_items->data->all() as $history_item) {
                    //$prices[] = (float)$history_item->inventryAmount;
                    $total_amount = $total_amount + (float)$history_item->inventryAmount;
                    if (!in_array($history_item->inHospitalItemId, $in_hospital_item_ids)) {
                        $in_hospital_item_ids[] = $history_item->inHospitalItemId;
                    }
                }
                InventoryEnd::where('hospitalId', $user_info->getHospitalId())->where('inventoryEndId', $division_history_slip->inventoryEndId)->update([
                    'totalAmount' => (string)$total_amount,
                    "itemsNumber" => collect($in_hospital_item_ids)->count(),
                ]);
            }

            $link = "%url/rel:mpgt:Inventory%&Action=inventoryEndList";

            if ($end_slip->inventoryStatus == 2 || $user_info->isApprover() || $division_history_slip->inventoryHStatus != "1") {
                $table = "%sf:usr:search25:mstfilter:table%";//修正不可能
            } else {
                $table = "%sf:usr:search24:mstfilter:table%";//修正可能
            }

            if ($user_info->isUser() && $division_history_slip->divisionId != $user_info->getDivisionId()) {
                $table = "%sf:usr:search25:mstfilter:table%";//修正不可能
            }

            $delete_button_view_flg = false;

            if ($end_slip->inventoryStatus == 1 && $division_history_slip->inventoryHStatus == 1) {
                if ($user_info->isAdmin() || $user_info->isApprover()) {
                    $delete_button_view_flg = true;
                } elseif ($user_info->isUser() && $user_info->getDivisionId() == $division_history_slip->divisionId) {
                    $delete_button_view_flg = true;
                }
            }

            $updateSaving = false;
            if ($end_slip->inventoryStatus == 1 && $division_history_slip->inventoryHStatus == 1) {
                if ($user_info->isAdmin()) {
                    $updateSaving = true;
                } elseif ($user_info->isUser() && $user_info->getDivisionId() == $division_history_slip->divisionId) {
                    $updateSaving = true;
                }
            }

            $updateTemporarySaving = false;
            if ($end_slip->inventoryStatus == 1 && $division_history_slip->inventoryHStatus != 1) {
                if ($user_info->isAdmin()) {
                    $updateTemporarySaving = true;
                } elseif ($user_info->isUser() && $user_info->getDivisionId() == $division_history_slip->divisionId) {
                    $updateTemporarySaving = true;
                }
            }

            //2021/11/19 合計金額の修正
            foreach ($inventory_items as $item) {
                if (array_key_exists($item->inHospitalItemId, $inventory_remake_items) === false) {
                    $inventory_remake_items[$item->inHospitalItemId] = $item;
                    /*
                    foreach($stock->data->all() as $stock_item){
                        if($stock_item->inHospitalItemId == $item->inHospitalItemId)
                        {
                            $inventory_remake_items[$item->inHospitalItemId]->inventryNum = $item->inventryNum;
                            $inventory_remake_items[$item->inHospitalItemId]->inventryAmount = $item->inventryAmount;
                            break;
                        }
                    }
                    */
                } else {
                    $inventory_remake_items[$item->inHospitalItemId]->inventryNum = (int)$inventory_remake_items[$item->inHospitalItemId]->inventryNum + (int)$item->inventryNum;
                    $inventory_remake_items[$item->inHospitalItemId]->inventryAmount = (float)$inventory_remake_items[$item->inHospitalItemId]->inventryAmount + (float)$item->inventryAmount;
                }
            }

            if (count($inventory_remake_items) != 0) {
                array_multisort(array_column($inventory_remake_items, 'inHospitalItemId'), SORT_ASC, $inventory_remake_items);
            }

            $hospital_data = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $useUnitPrice = $hospital_data->invUnitPrice;


            $api_url = "%url/card:page_263646%";
            $content = $this->view('NewJoyPla/view/InventorySlipItem', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'link' => $link,
                'updateSaving' => ($updateSaving),
                'updateTemporarySaving' => ($updateTemporarySaving),
                'delete_button_view_flg' => $delete_button_view_flg,
                'end_flg' => ($end_slip->inventoryStatus == 2),
                'useUnitPrice'=> $useUnitPrice,
                'inventory_total_items' => $inventory_remake_items,
                'table' => $table,
                'csrf_token' => Csrf::generate(16)
                ], false);

            $style   = $this->view('NewJoyPla/view/template/parts/DetailPrintCss', [], false)->render();
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 棚卸結果報告',
                'script' => '',
                'content'   => $content->render(),
                'style' => $style,
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    public function lotIndex(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = array();
        try {
            $user_info = new UserInfo($SPIRAL);

            $record_id = (int)$SPIRAL->getCardId();

            $division_history_slip = InventoryHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->get();
            $division_history_slip = $division_history_slip->data->get(0);

            $end_slip = InventoryEnd::where('hospitalId', $user_info->getHospitalId())->where('inventoryEndId', $division_history_slip->inventoryEndId)->get();
            $end_slip = $end_slip->data->get(0);

            $inventory_items = InventoryItemView::where('hospitalId', $user_info->getHospitalId())->where('inventoryHId', $division_history_slip->inventoryHId)->get();
            $inventory_items = $inventory_items->data->all();

            $inventory_remake_items = [];
            /*
            $stock = StockView::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$division_history_slip->divisionId);

            foreach($inventory_items->data->all() as $item )
            {
                $stock->orWhere('inHospitalItemId',$item->inHospitalItemId);
            }

            $stock = $stock->get();
            */
            if ($end_slip->inventoryStatus != 2) {
                //$prices = [];
                $total_amount = 0;
                $in_hospital_item_ids = [];
                foreach ($inventory_items as $inventory_item) {
                    //$prices[] = (float)$inventory_item->inventryAmount;
                    $total_amount = $total_amount + (float)$inventory_item->inventryAmount;
                    if (!in_array($inventory_item->inHospitalItemId, $in_hospital_item_ids)) {
                        $in_hospital_item_ids[] = $inventory_item->inHospitalItemId;
                    }
                }

                $update_time = $division_history_slip->updateTime;
                if ($division_history_slip->totalAmount != $total_amount || $division_history_slip->itemsNumber != collect($in_hospital_item_ids)->count()) {
                    $update_time = "now";
                }

                InventoryHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->update([
                    //"totalAmount" => collect($prices)->sum(),
                    "updateTime" => $update_time,
                    "totalAmount" => $total_amount,
                    "itemsNumber" => collect($in_hospital_item_ids)->count(),
                ]);

                $history_items = Inventory::where('hospitalId', $user_info->getHospitalId())->where('inventoryEndId', $division_history_slip->inventoryEndId)->get();

                //$prices = [];
                $total_amount = 0;
                $in_hospital_item_ids = [];
                foreach ($history_items->data->all() as $history_item) {
                    //$prices[] = (float)$history_item->inventryAmount;
                    $total_amount = $total_amount + (float)$history_item->inventryAmount;
                    if (!in_array($history_item->inHospitalItemId, $in_hospital_item_ids)) {
                        $in_hospital_item_ids[] = $history_item->inHospitalItemId;
                    }
                }
                InventoryEnd::where('hospitalId', $user_info->getHospitalId())->where('inventoryEndId', $division_history_slip->inventoryEndId)->update([
                    'totalAmount' => (string)$total_amount,
                    "itemsNumber" => collect($in_hospital_item_ids)->count(),
                ]);
            }

            $link = "%url/rel:mpgt:Inventory%&Action=inventoryEndList";

            if ($end_slip->inventoryStatus == 2 || $user_info->isApprover() || $division_history_slip->inventoryHStatus != "1") {
                $table = "%sf:usr:search25:mstfilter:table%";//修正不可能
            } else {
                $table = "%sf:usr:search24:mstfilter:table%";//修正可能
            }

            if ($user_info->isUser() && $division_history_slip->divisionId != $user_info->getDivisionId()) {
                $table = "%sf:usr:search25:mstfilter:table%";//修正不可能
            }

            $delete_button_view_flg = false;
            if ($end_slip->inventoryStatus == 1 && $division_history_slip->inventoryHStatus == 1) {
                if ($user_info->isAdmin() || $user_info->isApprover()) {
                    $delete_button_view_flg = true;
                } elseif ($user_info->isUser() && $user_info->getDivisionId() == $division_history_slip->divisionId) {
                    $delete_button_view_flg = true;
                }
            }

            $updateSaving = false;
            if ($end_slip->inventoryStatus == 1 && $division_history_slip->inventoryHStatus == 1) {
                if ($user_info->isAdmin()) {
                    $updateSaving = true;
                } elseif ($user_info->isUser() && $user_info->getDivisionId() == $division_history_slip->divisionId) {
                    $updateSaving = true;
                }
            }

            $updateTemporarySaving = false;
            if ($end_slip->inventoryStatus == 1 && $division_history_slip->inventoryHStatus != 1) {
                if ($user_info->isAdmin()) {
                    $updateTemporarySaving = true;
                } elseif ($user_info->isUser() && $user_info->getDivisionId() == $division_history_slip->divisionId) {
                    $updateTemporarySaving = true;
                }
            }

            //2021/11/19 合計金額の修正
            foreach ($inventory_items as $item) {
                if (array_key_exists($item->inHospitalItemId, $inventory_remake_items) === false) {
                    $inventory_remake_items[$item->inHospitalItemId] = $item;
                    /*
                    foreach($stock->data->all() as $stock_item){
                        if($stock_item->inHospitalItemId == $item->inHospitalItemId)
                        {
                            $inventory_remake_items[$item->inHospitalItemId]->inventryNum = $item->inventryNum;
                            $inventory_remake_items[$item->inHospitalItemId]->inventryAmount = $item->inventryAmount;
                            break;
                        }
                    }
                    */
                } else {
                    $inventory_remake_items[$item->inHospitalItemId]->inventryNum = (int)$inventory_remake_items[$item->inHospitalItemId]->inventryNum + (int)$item->inventryNum;
                    $inventory_remake_items[$item->inHospitalItemId]->inventryAmount = (float)$inventory_remake_items[$item->inHospitalItemId]->inventryAmount + (float)$item->inventryAmount;
                }
            }

            if (count($inventory_remake_items) != 0) {
                array_multisort(array_column($inventory_remake_items, 'inHospitalItemId'), SORT_ASC, $inventory_remake_items);
            }

            $hospital_data = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $useUnitPrice = $hospital_data->invUnitPrice;


            $api_url = "%url/card:page_204569%";
            $content = $this->view('NewJoyPla/view/InventorySlipLot', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'link' => $link,
                'updateSaving' => ($updateSaving),
                'updateTemporarySaving' => ($updateTemporarySaving),
                'delete_button_view_flg' => $delete_button_view_flg,
                'end_flg' => ($end_slip->inventoryStatus == 2),
                'useUnitPrice'=> $useUnitPrice,
                'inventory_total_items' => $inventory_remake_items,
                'table' => $table,
                'csrf_token' => Csrf::generate(16)
                ], false);

            $style   = $this->view('NewJoyPla/view/template/parts/DetailPrintCss', [], false)->render();
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 棚卸結果報告',
                'script' => '',
                'content'   => $content->render(),
                'style' => $style,
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    public function updateTemporarySaving(): View
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            $record_id = (int)$SPIRAL->getCardId();

            $inventory_history = InventoryHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->get();
            $inventory_history = $inventory_history->data->get(0);

            if ($inventory_history->inventoryHStatus === '1') {
                throw new Exception('完了のステータスではないため、更新できませんでした。', 1);
            }

            $end_slip = InventoryEnd::where('hospitalId', $user_info->getHospitalId())->where('inventoryEndId', $inventory_history->inventoryEndId)->get();
            $end_slip = $end_slip->data->get(0);

            if ($end_slip->inventoryStatus == 2 && ($user_info->isUser() && $user_info->getDivisionId() !== $inventory_history->divisionId) && $user_info->isApprover()) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            $result = InventoryHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->update(
                [
                    'inventoryHStatus' => "1"
                ]
            );

            $content = new ApiResponse($result->ids, $result->count, $result->code, $result->message, ['']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }
    public function updateSaving(): View
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            $record_id = (int)$SPIRAL->getCardId();

            $inventory_history = InventoryHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->get();
            $inventory_history = $inventory_history->data->get(0);

            if ($inventory_history->inventoryHStatus !== '1') {
                throw new Exception('一時保存のステータスではないため、更新できませんでした。', 1);
            }

            $end_slip = InventoryEnd::where('hospitalId', $user_info->getHospitalId())->where('inventoryEndId', $inventory_history->inventoryEndId)->get();
            $end_slip = $end_slip->data->get(0);

            if ($end_slip->inventoryStatus == 2 && ($user_info->isUser() && $user_info->getDivisionId() !== $inventory_history->divisionId) && $user_info->isApprover()) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            $result = InventoryHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->update(
                [
                    'inventoryHStatus' => "2"
                ]
            );

            $content = new ApiResponse($result->ids, $result->count, $result->code, $result->message, ['']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }

    public function deleteSlipApi(): View
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);
            /*
            if($user_info->isUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            */
            $record_id = (int)$SPIRAL->getCardId();

            $inventory_history = InventoryHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->get();
            $inventory_history = $inventory_history->data->get(0);

            if ($inventory_history->inventoryHStatus !== '1') {
                throw new Exception('一時保存のステータスではないため、削除できませんでした。', 1);
            }

            $end_slip = InventoryEnd::where('hospitalId', $user_info->getHospitalId())->where('inventoryEndId', $inventory_history->inventoryEndId)->get();
            $end_slip = $end_slip->data->get(0);

            if ($end_slip->inventoryStatus == 2 && ($user_info->isUser() && $user_info->getDivisionId() !== $inventory_history->divisionId)) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            $result = InventoryHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->delete();

            $content = new ApiResponse($result->ids, $result->count, $result->code, $result->message, ['slipDeleteApi']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['slipDeleteApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }
}

/***
 * 実行
 */
$InventoryDivisionSlipController = new InventoryDivisionSlipController();

$action = $SPIRAL->getParam('Action');

{
    if ($action === 'updateSaving') {
        echo $InventoryDivisionSlipController->updateSaving()->render();
    } elseif ($action === 'updateTemporarySaving') {
        echo $InventoryDivisionSlipController->updateTemporarySaving()->render();
    } elseif ($action === 'deleteSlipApi') {
        echo $InventoryDivisionSlipController->deleteSlipApi()->render();
    } elseif ($action === 'ItemIndex') {
        echo $InventoryDivisionSlipController->itemIndex()->render();
    } elseif ($action === 'LotIndex') {
        echo $InventoryDivisionSlipController->lotIndex()->render();
    }
}
