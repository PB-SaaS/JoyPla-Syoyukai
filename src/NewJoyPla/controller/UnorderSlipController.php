<?php

namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Division;
use App\Model\HospitalUser;
use App\Model\Hospital;
use App\Model\OrderedItemView;
use App\Model\OrderDataView;
use App\Model\Order;
use App\Model\OrderHistory;
use App\Model\InventoryAdjustmentTransaction;
use App\Model\DistributorAffiliationView;


use ApiErrorCode\FactoryApiErrorCode;
use App\Model\Distributor;
use stdClass;
use Exception;

class UnorderSlipController extends Controller
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
        try {
            $ItemsToJs = [];
            $order_data = [];
            $is_deleted = false;
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }
            $record_id = (int)$SPIRAL->getCardId();

            $card_data = OrderHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->get();
            $card_data = $card_data->data->get(0);

            $order_data = OrderedItemView::where('hospitalId', $user_info->getHospitalId())->where('orderNumber', $card_data->orderNumber)->get();

            $order_data = $order_data->data->all();
            $is_deleted = (count($order_data) == 0);

            if ($is_deleted) {
                $card_data->destroy($card_data->id);
            } elseif ($card_data->orderStatus != "1") {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            } else {
                $total_price = 0;
                $in_hospital_item_ids = [];
                foreach ($order_data as $order_item) {
                    $total_price = $total_price + (float)$order_item->orderPrice;
                    if (array_search($order_item->inHospitalItemId, $in_hospital_item_ids) === false) {
                        $in_hospital_item_ids[] = $order_item->inHospitalItemId;
                    }
                }
                OrderHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->update([
                    'totalAmount'=>$total_price,
                    'itemsNumber' => count($in_hospital_item_ids),//院内商品マスタID数
                    ]);

                foreach ($order_data as $record) {
                    $ItemsToJs[$record->inHospitalItemId] = [
                        "countNum"=> $record->orderQuantity,
                        "quantity" => $record->quantity
                    ];
                }
            }

            $link = '%url/rel:mpgt:Order%&Action=unorderedList';
            if ($user_info->isUser()) {
                if (preg_match("/Action=unorderedListForDivision/", $_SERVER['HTTP_REFERER'])) {
                    $box = parse_url($_SERVER['HTTP_REFERER']);
                    $link = $box['path']."?".$box['query'];
                }
            }
            $api_url = "%url/card:page_262926%";
            $content = $this->view('NewJoyPla/view/UnorderedSlipDetail', [
                'api_url' => $api_url,
                'userInfo' => $user_info,
                'is_deleted' => $is_deleted,
                'order_data' => $order_data,
                'ItemsToJs' => $ItemsToJs,
                'link'=> $this->sanitize($link),
                'csrf_token' => Csrf::generate(16)
                ], false);
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
                'title'     => 'JoyPla 未発注書',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    public function orderCommentApi()
    {
        global $SPIRAL;

        $content = '';

        try {
            $user_info = new UserInfo($SPIRAL);
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            if ($user_info->isApprover()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $id = (int)$SPIRAL->getCardId();
            $comment = ($SPIRAL->getParam('ordercomment')) ? urldecode($SPIRAL->getParam('ordercomment')) : '';
            if ($comment) {
                $comment = $this->html($comment);
            }

            if (strlen($comment) > 512) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            $result = OrderHistory::where('hospitalId', $user_info->getHospitalId())->find($id)->update(['ordercomment'=>$comment]);

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            /** TODO
             *  spiralDatabaseのレスポンスをApiResponseに変更
             **/
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }

    public function itemDelete()
    {
        global $SPIRAL;

        $content = '';

        try {
            $user_info = new UserInfo($SPIRAL);
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            if ($user_info->isApprover()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $id = (int)$SPIRAL->getCardId();
            $items = $SPIRAL->getParam('data');

            $order_instance = Order::where('hospitalId', $user_info->getHospitalId());

            foreach ($items as $item) {
                $order_instance->orWhere('orderCNumber', $item['orderCNumber']);
            }

            $result = $order_instance->delete();

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            /** TODO
             *  spiralDatabaseのレスポンスをApiResponseに変更
             **/
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }

    public function itemUpdate()
    {
        global $SPIRAL;

        $content = '';

        try {
            $user_info = new UserInfo($SPIRAL);
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            if ($user_info->isApprover()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $id = (int)$SPIRAL->getCardId();
            $items = $SPIRAL->getParam('data');

            $order_instance = Order::where('hospitalId', $user_info->getHospitalId());
            $update = [];
            foreach ($items as $item) {
                $update[] = [
                    'orderCNumber' => $item['orderCNumber'],
                    'orderQuantity' => $item['num'],
                ];
            }

            $result = $order_instance->bulkUpdate('orderCNumber', $update);

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            /** TODO
             *  spiralDatabaseのレスポンスをApiResponseに変更
             **/
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }

    public function orderDelete()
    {
        global $SPIRAL;

        $content = '';

        try {
            $user_info = new UserInfo($SPIRAL);
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            if ($user_info->isApprover()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $id = (int)$SPIRAL->getCardId();

            $result = OrderHistory::where('hospitalId', $user_info->getHospitalId())->find($id)->delete();

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            /** TODO
             *  spiralDatabaseのレスポンスをApiResponseに変更
             **/
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }

    public function orderFix()
    {
        global $SPIRAL;

        $content = '';

        try {
            $user_info = new UserInfo($SPIRAL);
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $id = (int)$SPIRAL->getCardId();
            $comment = ($SPIRAL->getParam('ordercomment')) ? urldecode($SPIRAL->getParam('ordercomment')) : '';
            if ($comment) {
                $comment = $this->html($comment);
            }
            $adjustment = ($SPIRAL->getParam('adjustment') === '1' || $SPIRAL->getParam('adjustment') === '2') ? $SPIRAL->getParam('adjustment') : "";

            if (strlen($comment) > 512) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            $result = OrderHistory::where('hospitalId', $user_info->getHospitalId())->find($id)->update([
                'ordercomment'=> $comment,
                'orderStatus' => 2,
                'orderTime' => 'now',
                'adjustment' => $adjustment,
            ]);

            $order_history = OrderDataView::where('hospitalId', $user_info->getHospitalId())->find($id)->get();
            $order_history = $order_history->data->get(0);

            $hospital = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);

            if ($hospital->receivingTarget == '1') { //大倉庫
                $division = Division::where('hospitalId', $user_info->getHospitalId())->where('divisionType', '1')->get();
                $division = $division->data->get(0);
                $divisionId = $division->divisionId;
                $deliveryDestCode = $division->deliveryDestCode;
            }
            if ($hospital->receivingTarget == '2') { //発注部署
                $divisionId = $order_history->divisionId;
                $division = Division::where('hospitalId', $user_info->getHospitalId())->where('divisionId', $divisionId)->get();
                $division = $division->data->get(0);
                $deliveryDestCode = $division->deliveryDestCode;
            }

            $order_items = OrderedItemView::where('hospitalId', $user_info->getHospitalId())->where('orderNumber', $order_history->orderNumber)->get();
            $inventory_adjustment_trdata = [];
            foreach ($order_items->data->all() as $item) {
                if ($item->orderQuantity <= 0) {
                    continue;
                }
                $inventory_adjustment_trdata[] = [
                    'divisionId' => $divisionId,
                    'pattern' => 2,
                    'inHospitalItemId' => $item->inHospitalItemId,
                    'count' => 0,
                    'orderWithinCount' => ((float)$item->orderQuantity * (float)$item->quantity),
                    'hospitalId' => $user_info->getHospitalId(),
                ];
            }

            {
                $mail_body = $this->view('NewJoyPla/view/Mail/OrderFixForDistributor', [
                    'name' => '%val:usr:name%',
                    'hospital_name' => $hospital->hospitalName,
                    'postal_code' => $hospital->postalCode,
                    'prefectures' => $hospital->prefectures,
                    'address' => $hospital->address,
                    'division_name' => $order_history->divisionName,
                    'order_date' => $order_history->orderTime,
                    'order_number' => $order_history->orderNumber,
                    'item_num' => $order_history->itemsNumber,
                    'total_price' => '￥'.number_format_jp((float)$order_history->totalAmount),
                    'slip_url' => OROSHI_OrderDetailAccess."?searchValue=".$order_history->orderNumber,
                    'login_url' => OROSHI_LOGIN_URL,
                ], false)->render();
                $select_name = $this->makeId($order_history->distributorId);

                $test = DistributorAffiliationView::selectName($select_name)
                    ->rule(['name'=>'distributorId','label'=>'name_'.$order_history->distributorId,'value1'=>$order_history->distributorId,'condition'=>'matches'])
                    ->rule(['name'=>'invitingAgree','label'=>'invitingAgree','value1'=>'t','condition'=>'is_boolean'])
                    ->filterCreate();

                $test = DistributorAffiliationView::selectRule($select_name)
                    ->body($mail_body)
                    ->subject("[JoyPla] 発注が行われました")
                    ->from(FROM_ADDRESS, FROM_NAME)
                    ->send();
            }


            {
                $distributor = Distributor::where('distributorId', $order_history->distributorId)->plain()
                ->value('distributorName')
                ->value('postalCode')
                ->value('prefectures')
                ->value('address')
                ->get();
                $distributor = $distributor->data->get(0);

                $mail_body = $this->view('NewJoyPla/view/Mail/OrderFix', [
                    'name' => '%val:usr:name%',
                    'distributor_name' => $distributor->distributorName,
                    'distributor_postal_code' => $distributor->postalCode,
                    'distributor_prefectures' => $distributor->prefectures,
                    'distributor_address' => $distributor->address,
                    'hospital_name' => $hospital->hospitalName,
                    'postal_code' => $hospital->postalCode,
                    'prefectures' => $hospital->prefectures,
                    'address' => $hospital->address,
                    'division_name' => $order_history->divisionName,
                    'order_date' => $order_history->orderTime,
                    'order_number' => $order_history->orderNumber,
                    'item_num' => $order_history->itemsNumber,
                    'total_price' => '￥'.number_format_jp((float)$order_history->totalAmount),
                    'login_url' => LOGIN_URL,
                ], false)->render();

                $hospital_user = HospitalUser::getNewInstance();
                $select_name = $this->makeId($hospital->hospitalId);
                $test = $hospital_user::selectName($select_name)
                    ->rule(['name'=>'hospitalId','label'=>'name_'.$hospital->hospitalId,'value1'=>$hospital->hospitalId,'condition'=>'matches'])
                    ->rule(['name'=>'userPermission','label'=>'permission_admin2','value1'=>'1,3','condition'=>'contains'])
                    ->filterCreate();

                $test = $hospital_user::selectRule($select_name)
                    ->body($mail_body)
                    ->subject("[JoyPla] 発注が行われました")
                    ->from(FROM_ADDRESS, FROM_NAME)
                    ->send();

                $hospital_user = HospitalUser::getNewInstance();
                $select_name = $this->makeId($hospital->hospitalId);
                $test = $hospital_user::selectName($select_name)
                    ->rule(['name'=>'hospitalId','label'=>'name_'.$hospital->hospitalId,'value1'=>$hospital->hospitalId,'condition'=>'matches'])
                    ->rule(['name'=>'userPermission','label'=>'permission_admin2','value1'=>'2','condition'=>'contains'])
                    ->rule(['name'=>'divisionId','label'=>'permission_division','value1'=>$order_history->divisionId,'condition'=>'matches'])
                    ->filterCreate();
                $test = $hospital_user::selectRule($select_name)
                    ->body($mail_body)
                    ->subject("[JoyPla] 発注が行われました")
                    ->from(FROM_ADDRESS, FROM_NAME)
                    ->send();
            }

            $result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);
            $result = Order::where('hospitalId', $user_info->getHospitalId())->where('orderNumber', $order_history->orderNumber)->update([
                'updateTime' => 'now',
                'deliveryDestCode' => $deliveryDestCode
            ]);

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            /** TODO
             *  spiralDatabaseのレスポンスをApiResponseに変更
             **/
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }

    private function html($string = '')
    {
        return htmlspecialchars($string, REPLACE_FLAGS, CHARSET);
    }
}
/***
 * 実行
 */
$UnorderSlipController = new UnorderSlipController();

$action = $SPIRAL->getParam('Action');

{
    if ($action === 'orderCommentApi') {
        echo $UnorderSlipController->orderCommentApi()->render();
    } elseif ($action === 'itemDelete') {
        echo $UnorderSlipController->itemDelete()->render();
    } elseif ($action === 'itemUpdate') {
        echo $UnorderSlipController->itemUpdate()->render();
    } elseif ($action === 'orderDelete') {
        echo $UnorderSlipController->orderDelete()->render();
    } elseif ($action === 'orderFix') {
        echo $UnorderSlipController->orderFix()->render();
    } else {
        echo $UnorderSlipController->index()->render();
    }
}
