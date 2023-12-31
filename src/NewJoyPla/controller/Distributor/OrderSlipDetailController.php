<?php

namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Hospital;
use App\Model\Distributor;
use App\Model\HospitalUser;
use App\Model\Division;
use App\Model\Order;
use App\Model\OrderedItemView;
use App\Model\OrderHistory;

use ApiErrorCode\FactoryApiErrorCode;
use App\Model\DistributorAffiliationView;
use App\Model\DistributorUser;
use stdClass;
use Exception;


/**
 * 発注書
 */
class OrderSlipDetailController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;

        $title = 'JoyPla 発注書';

        try {
            
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isHospitalUser()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $card_Id = (int)$SPIRAL->getCardId();
            if ($card_Id == null) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }
            $card = OrderHistory::find($card_Id)->get();
            $card = $card->data->get(0);
            
            $affiliations = DistributorAffiliationView::where('loginId',$user_info->getLoginId())->where('distributorId',$card->distributorId)->where('invitingAgree','1')->get();
                        
            if($affiliations->count == '0'){
                  throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $affiliations = $affiliations->data->get(0);

            DistributorUser::where('loginId',$user_info->getLoginId())->update([
                  'affiliationId' => $affiliations->affiliationId
            ]);
            
            $order_items = OrderedItemView::where('orderNumber',$card->orderNumber)->get();
            $order_items = $order_items->data->all();
            
            foreach ($order_items as $key => $item) {
                $order_items[$key]->dueDate = \App\lib\changeDateFormat("Y年m月d日", $order_items[$key]->dueDate, 'Y-m-d');
                $order_items[$key]->dueDateStyle = [];
            }

            $api_url = '%url/card:page_266218%';

            $link_title = "発注書一覧";
            $link = '%url/rel:mpgt:OrderD%';

            $hospital = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
            $receipt_division = '';
            
            if ($hospital->receivingTarget == '1') { //大倉庫
                $division = Division::where('hospitalId', $user_info->getHospitalId())->where('divisionType', '1')->get();
                $division = $division->data->get(0);
                $receipt_division = $division->divisionName;
            }
            if ($hospital->receivingTarget == '2') { //発注部署
                $receipt_division = '%val:usr:divisionName%';
            }

            $content = $this->view('NewJoyPla/view/Distributor/OrderSlipDetail', [
                'user_info' => $user_info,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'orderResetButton' => (int)($card->orderStatus == 3 || $card->orderStatus == 4),
                'orderFixingButton' => (int)($card->orderStatus == 2),
                'orderItems' => $order_items,
                /*'pattern' => $pattern,*/
                'link_title' => $link_title,
                'link' => $link,
                'receipt_division' => $receipt_division
                /*'cardId' => $cardId*/
            ], false);
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ], false);
        } finally {
            $style   = $this->view('NewJoyPla/view/template/parts/DetailPrintCss', [], false)->render();
            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [], false)->render();

            $script   = $this->view('NewJoyPla/view/template/parts/Script', [], false)->render();
            $head = $this->view('NewJoyPla/view/template/parts/Head', ['new'=> true], false);

            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL,
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => $title,
                'content'   => $content->render(),
                'style' => $style,
                'script' => $script,
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }
    
    public function orderResetApi()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            
            $user_info = new UserInfo($SPIRAL);
            
            $record_id = (int)$SPIRAL->getCardId();
            
            $order_history = OrderHistory::where('distributorId', $user_info->getDistributorId())->find($record_id)->get();
            $order_history = $order_history->data->get(0);
            
            if (! ($order_history->orderStatus == 3 || $order_history->orderStatus == 4)) {
                //コードはそのうち考える
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $comment = ($SPIRAL->getParam('distrComment')) ? urldecode($SPIRAL->getParam('distrComment')) : '';
            if ($comment) {
                $comment = $this->html($comment);
            }

            $length = strlen(mb_convert_encoding($comment, 'SJIS', 'UTF-8'));
            if ($length > 512) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            $result = OrderHistory::where('distributorId', $user_info->getDistributorId())->find($record_id)->update([
                    'orderStatus' => 2, 'distrComment' => $comment
                ]);

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }
    
    public function orderFixingApi()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);
            
            $user_info = new UserInfo($SPIRAL);
            
            $record_id = (int)$SPIRAL->getCardId();
            
            $order_history = OrderHistory::where('distributorId', $user_info->getDistributorId())->find($record_id)->get();
            $order_history = $order_history->data->get(0);
            
            if (! ($order_history->orderStatus == 2)) {
                //コードはそのうち考える
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            $comment = ($SPIRAL->getParam('distrComment')) ? urldecode($SPIRAL->getParam('distrComment')) : '';
            if ($comment) {
                $comment = $this->html($comment);
            }

            $length = strlen(mb_convert_encoding($comment, 'SJIS', 'UTF-8'));
            if ($length > 512) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $order_items = $SPIRAL->getParam('lists');
            $update_data = [];
            $orderStatus = 3;
            
            foreach ($order_items as $item) {
                if ($item['dueDate'] != '') {
                    $orderStatus = 4;
                }
                $update_data[] = [
                    'orderCNumber' => $item['orderCNumber'],
                    'dueDate' => $item['dueDate'],
                    ];
            }
            
            Order::where('orderNumber', $order_history->orderNumber)->bulkUpdate('orderCNumber', $update_data);
            
            $result = OrderHistory::where('distributorId', $user_info->getDistributorId())->find($record_id)->update([
                    'orderStatus' => $orderStatus, 'distrComment' => $comment
                ]);
            
            $distributor = Distributor::where('distributorId', $user_info->getDistributorId())->get();
            $distributor = $distributor->data->get(0);
            
            $subject = "[JoyPla] 受注されました";
            $text = "受注";
            if ($orderStatus == 4) {
                $subject = "[JoyPla] 受注（納期報告）されました";
                $text = "受注（納期報告）";
            }
            
            $mail_body = $this->view('NewJoyPla/view/Mail/DistOrderFixing', [
                'name' => '%val:usr:name%',
                'text' => $text,
                'distributorName' => $distributor->distributorName,
                'staffName' => $user_info->getName(),
                'orderTime' => $order_history->orderTime,
                'itemsNumber' => $order_history->itemsNumber,
                'orderNumber' => $order_history->orderNumber,
                'totalAmount' => "￥".number_format_jp((float)$order_history->totalAmount, 2),
                'url' => LOGIN_URL,
            ], false)->render();
            
            $hospital_user = HospitalUser::getNewInstance();
            
            $select_name = $this->makeId($order_history->hospitalId);
            $test = $hospital_user::selectName($select_name)
                ->rule(['name'=>'hospitalId','label'=>'name_'.$order_history->hospitalId,'value1'=>$order_history->hospitalId,'condition'=>'matches'])
                ->rule(['name'=>'userPermission','label'=>'permission_admin2','value1'=>'1,3','condition'=>'contains'])
                ->filterCreate();
                
            $test = $hospital_user::selectRule($select_name)
                ->body($mail_body)
                ->subject($subject)
                ->from(FROM_ADDRESS, FROM_NAME)
                ->send();
                
            $hospital_user = HospitalUser::getNewInstance();
            $select_name = $this->makeId($order_history->hospitalId);
            $test = $hospital_user::selectName($select_name)
                ->rule(['name'=>'hospitalId','label'=>'name_'.$order_history->hospitalId,'value1'=>$order_history->hospitalId,'condition'=>'matches'])
                ->rule(['name'=>'userPermission','label'=>'permission_admin2','value1'=>'2','condition'=>'contains'])
                ->rule(['name'=>'divisionId','label'=>'permission_division','value1'=>$order_history->divisionId,'condition'=>'matches'])
                ->filterCreate();
            $test = $hospital_user::selectRule($select_name)
                ->body($mail_body)
                ->subject($subject)
                ->from(FROM_ADDRESS, FROM_NAME)
                ->send();
                
            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }

    public function distrCommentApi()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            $record_id = (int)$SPIRAL->getCardId();

            $order_history = OrderHistory::where('distributorId', $user_info->getDistributorId())->find($record_id)->get();
            $order_history = $order_history->data->get(0);

            $comment = ($SPIRAL->getParam('distrComment')) ? urldecode($SPIRAL->getParam('distrComment')) : '';
            if ($comment) {
                $comment = $this->html($comment);
            }

            $length = strlen(mb_convert_encoding($comment, 'SJIS', 'UTF-8'));
            if ($length > 512) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            $result = OrderHistory::where('distributorId', $user_info->getDistributorId())->find($record_id)->update([
                    'distrComment' => $comment
                ]);

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['update']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['update']);
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
$OrderSlipDetailController = new OrderSlipDetailController();

$action = $SPIRAL->getParam('Action');

{
    if ($action === 'orderResetApi') {
        echo $OrderSlipDetailController->orderResetApi()->render();
    } elseif ($action === 'orderFixingApi') {
        echo $OrderSlipDetailController->orderFixingApi()->render();
    } elseif ($action === 'distrCommentApi') {
        echo $OrderSlipDetailController->distrCommentApi()->render();
    } else {
        echo $OrderSlipDetailController->index()->render();
    }
}