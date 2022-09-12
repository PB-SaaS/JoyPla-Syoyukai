<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Hospital;
use App\Model\Division;
use App\Model\Order;
use App\Model\OrderDataView;
use App\Model\OrderedItemInfoView;
use App\Model\OrderHistory;
use App\Model\Receiving;
use App\Model\ReceivingHistory;
use App\Model\InventoryAdjustmentTransaction;

use ApiErrorCode\FactoryApiErrorCode;
use App\Model\DistributorAffiliationView;
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
    
    public function index($receipt_flg = false)
    {
        global $SPIRAL;

        $title = 'JoyPla 発注書';

        try {
            
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $card_Id = $SPIRAL->getCardId();
            if($card_Id == null)
            {   
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $card = OrderHistory::where('hospitalId',$user_info->getHospitalId())->find($card_Id)->get();
            $card = $card->data->get(0);
            
            if($user_info->isUser() && $card->divisionId != $user_info->getDivisionId())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $order_items = OrderedItemInfoView::where('hospitalId',$user_info->getHospitalId())->sort('id','asc')->where('orderNumber',$card->orderNumber)->get();
            $order_items = $order_items->data->all();

            if ($card->orderStatus != 8) // 貸出品以外
            {
/*
                $makeOrderData = [];
                foreach ($order_items as $data)
                {
                    if ($data->orderQuantity - $data->receivingNowCount <= 0)
                    {
                        $makeOrderData[] = [
                            $data->orderCNumber,
                            'now',
                            '1'
                        ];
                    }
                }
*/
                $pattern = $this->checkPattern($order_items , $card->orderStatus);
                $makeOrderHistoryData = [];
                $receivingTime = null;
                if ($pattern == 6) { $receivingTime = 'now'; }
               
                $result = OrderHistory::where('orderNumber',$card->orderNumber)->update([
                     'receivingTime' => $receivingTime,
                     'orderStatus' => $pattern,
                ]);
            }
            
            $api_url = '%url/card:page_263320%';

            if($receipt_flg){
                $link_title = "入荷照合";
            	$link = '%url/rel:mpgt:Order%&Action=arrivalVerification';
                if($user_info->isUser()){
                    if (preg_match("/Action=arrivalVerificationForDivision/", $_SERVER['HTTP_REFERER'])) {
                        $box = parse_url($_SERVER['HTTP_REFERER']);
                        $link = $box['path']."?".$box['query'];
                    }
                }
            } else {
                $link_title = "発注書一覧";
            	$link = '%url/rel:mpgt:Order%&Action=orderedList';
                if($user_info->isUser()){
                    if (preg_match("/Action=orderedListForDivision/", $_SERVER['HTTP_REFERER'])) {
                        $box = parse_url($_SERVER['HTTP_REFERER']);
                        $link = $box['path']."?".$box['query'];
                    }
                }
            }
            
            foreach($order_items as $key => $item)
            {
                $order_items[$key]->nowCount = 0;
                $order_items[$key]->possibleNumber = ((int)$item->orderQuantity - (int)$item->receivingNum);
                $order_items[$key]->max = ((int)$item->orderQuantity > 0)? $item->orderQuantity : 0;
                $order_items[$key]->min = ((int)$item->orderQuantity > 0)? 0 : $item->orderQuantity;
            }
            
            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
            $receipt_division = '';
            if($hospital->receivingTarget == '1'){ //大倉庫
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionType','1')->get();
                $division = $division->data->get(0);
                $receipt_division = $division->divisionName;
            }
            if($hospital->receivingTarget == '2'){ //発注部署
                $receipt_division = '%val:usr:divisionName%';
            }
            $content = $this->view('NewJoyPla/view/OrderSlipDetail', [
                'user_info' => $user_info,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'orderItems' => $order_items,
                'pattern' => $pattern,
                'link_title' => $link_title,
                'link' => $this->sanitize($link),
                'receipt_division' => $receipt_division
                //'cardId' => $cardId
            ] , false);

        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false);

        } finally {
            
            $style   = $this->view('NewJoyPla/view/template/parts/DetailPrintCss', [] , false)->render();
            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [] , false)->render();

            $script   = $this->view('NewJoyPla/view/template/parts/Script', [] , false)->render();
            $head = $this->view('NewJoyPla/view/template/parts/Head', ['new'=> true] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
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
            ],false);
        }
    }

    private function checkPattern(array $items , String $now_pattern)
    {
        $receiving_items = [];
        $receiving_fix = [];
        foreach ($items as $record)
        {
            if ($record->receivingNum != 0) { $receiving_items[] = $record; } //入庫OR一部入庫状態
            if ($record->receivingFlag) { $receiving_fix[] = $record; } //入庫完了
        }
        /*
            1 =>"未発注",
            2 =>"発注完了",
            3 =>"受注完了",
            4 =>"納期報告済",
            5 =>"一部入庫完了",
            6 =>"入庫完了",
            7 =>"納品取消",
            8 =>"貸出品",
        */
        if (count($items) == count($receiving_fix)) { return 6; }//
        if (count($receiving_items) != 0 ) {return 5; }
        return $now_pattern;
    }
    
    /**
     * 納品照合
     */
    public function receivingAPI(): View
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);

            $card_id = (int)$SPIRAL->getCardId();
            if($card_id == null)
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if($user_info->isApprover())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $receiving_items = $SPIRAL->getParam('receiving');
            $receiving_items = $this->requestUrldecode($receiving_items);

            foreach ($receiving_items as $item)
            {
                if ((int)$item['lotManagement'])
                {
                    if (($item['lotNumber'] == '') || ($item['lotDate'] == ''))
                    {
                        throw new Exception('invalid lotNumber',100);
                    }
                }
                if (($item['lotNumber'] != '' && $item['lotDate'] == '' ) || ($item['lotNumber'] == '' && $item['lotDate'] != ''))
                {
                    throw new Exception('invalid lotNumber input',101);
                }
                if (($item['lotNumber'] != '') && ($item['lotDate'] != '')) 
                {
                    //if ((!ctype_alnum($item['lotNumber'])) || (strlen($item['lotNumber']) > 20))
                    if ((!preg_match('/^[a-zA-Z0-9!-\/:-@¥[-`{-~]+$/', $item['lotNumber'])) || (strlen($item['lotNumber']) > 20))
                    {
                        throw new Exception('invalid lotNumber format',102);
                    }
                }
            }

            $card = OrderDataView::where('hospitalId',$user_info->getHospitalId())->find($card_id)->get();
            $card = $card->data->get(0);
            
            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
           
            if($hospital->receivingTarget == '1'){ //大倉庫
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionType','1')->get();
                $division = $division->data->get(0);
                $divisionId = $division->divisionId;
            }
            if($hospital->receivingTarget == '2'){ //発注部署
                $divisionId = $card->divisionId;
            }

            $insert_data = [];
            $history_data = [];
            $inventory_adjustment_trdata = [];
            
            $receiving_ary = [];
            foreach ($receiving_items as $key => $item)
            {
                $exist = false;
                foreach($receiving_ary as $ary_key => $ary)
                {
                    if(
                        $item['orderCNumber'] == $ary['orderCNumber'] &&
                        $item['lotNumber'] == $ary['lotNumber'] &&
                        $item['lotDate'] == $ary['lotDate'] 
                    )
                    {
                        $exist = true;
                        $receiving_ary[$ary_key]['countNum'] = (int)$ary['countNum'] + (int)$item['countNum'];
                    }
                }
                if(!$exist)
                {
                    $receiving_ary[] = $item;
                }
            }
            $receiving_history_id = $this->makeId('04');
            $receiving_items = $receiving_ary;
            $in_hospital_item_ids = [];
            $total_price = [];
            
            foreach ($receiving_items as $item) 
            {
                if ($item['countNum'] != 0)
                {
                    if (array_search($item['inHospitalItemId'], $in_hospital_item_ids) === false) {
                        $in_hospital_item_ids[] = $item['inHospitalItemId'];
                    }
                    $insert_data[] = [
                        'orderCNumber' => $item['orderCNumber'],
                        'receivingCount' => $item['countNum'],
                        'receivingHId' => $receiving_history_id,
                        'inHospitalItemId' => $item['inHospitalItemId'],
                        'price' => $item['price'],
                        'receivingPrice' => (float)$item['countNum'] * (float)$item['price'],
                        'hospitalId' => $user_info->getHospitalId(),
                        'distributorId' => $card->distributorId,
                        'divisionId' => $divisionId,
                        'lotNumber' => $item['lotNumber'],
                        'lotDate' => $item['lotDate']
                    ];
                    $total_price[] = (float)$item['countNum'] * (float)$item['price'];
                    
                    if ($item['lotNumber'] && $item['lotDate']) {
                        $inventory_adjustment_trdata[] = [
                            'divisionId' => $divisionId,
                            'inHospitalItemId' => $item['inHospitalItemId'],
                            'count' => (int)$item['quantity'] * (int)$item['countNum'],
                            'orderWithinCount' => ((int)$item['countNum'] < 0) ? 0 : -((int)$item['quantity'] * (int)$item['countNum']),
                            'hospitalId' => $user_info->getHospitalId(),
                            'lotUniqueKey' => $user_info->getHospitalId().$divisionId.$item['inHospitalItemId'].$item['lotNumber'].$item['lotDate'],
                            'lotNumber' => $item['lotNumber'],
                            'lotDate' => $item['lotDate'],
                            'pattern' => 3,
                            'stockQuantity' => (int)$item['quantity'] * (int)$item['countNum']
                        ];
                    } 
                    else 
                    {
                        $inventory_adjustment_trdata[] = [
                            'divisionId' => $divisionId,
                            'pattern' => 3,
                            'inHospitalItemId' => $item['inHospitalItemId'],
                            'count' => (int)$item['quantity'] * (int)$item['countNum'],
                            'orderWithinCount' => ((int)$item['countNum'] < 0) ? 0 : -((int)$item['quantity'] * (int)$item['countNum']),
                            'hospitalId' => $user_info->getHospitalId()
                        ];
                    }
                }
            }

            $history_data[] = [
                'receivingHId' => $receiving_history_id,
                'distributorId' => $card->distributorId,
                'orderHistoryId' => $card->orderNumber,
                'hospitalId' => $user_info->getHospitalId(),
                'itemsNumber' => count($in_hospital_item_ids),//院内商品マスタID数
                'totalAmount' => collect($total_price)->sum(),//院内商品マスタID数
            ];

            $update_data = [];
            $order_items = Order::where('hospitalId',$user_info->getHospitalId())->where('orderNumber',$card->orderNumber)->get();
            
            $check_ary = [];
            foreach ($receiving_items as $item)
            {
                if(! array_key_exists($item['orderCNumber'],$check_ary)){
                    $check_ary[$item['orderCNumber']] = 0;
                }
                $check_ary[$item['orderCNumber']] = $check_ary[$item['orderCNumber']] + (int)$item['countNum'];
            }
            
            foreach ($order_items->data->all() as $order_item)
            {
                foreach ($check_ary as $orderCNumber => $count)
                {
                    if($order_item->orderCNumber == $orderCNumber)
                    {
                        $receiving_flag = (abs($order_item->orderQuantity) - (abs((int)$order_item->receivingNum) + abs((int)$count)) <= 0) ? '1' : '0';
                        $update_data[] = [
                            'orderCNumber' => $order_item->orderCNumber,
                            'receivingTime' => 'now',
                            'receivingFlag' => $receiving_flag,
                            'receivingNum' => (int)$order_item->receivingNum + (int)$count
                        ];
                    }
                }
            }
            $result = ReceivingHistory::insert($history_data);
            $result = Receiving::insert($insert_data);
            $result = Order::bulkUpdate('orderCNumber', $update_data);
            $result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);

            $content = new ApiResponse(['historyId'=>$receiving_history_id], $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    public function orderedDeleteAPI()
    {
        global $SPIRAL;
        try{
            $user_info = new UserInfo($SPIRAL);
            
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            
            if($user_info->isApprover())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $card_id = (int)$SPIRAL->getCardId();
            if($card_id == null)
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $user_info = new UserInfo($SPIRAL);

            $card = OrderDataView::where('hospitalId',$user_info->getHospitalId())->find($card_id)->get();
            $card = $card->data->get(0);
            
            if($card->orderStatus != 2)
            {
                //can not delete
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }


            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
           
            if($hospital->receivingTarget == '1'){ //大倉庫
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionType','1')->get();
                $division = $division->data->get(0);
                $divisionId = $division->divisionId;
            }
            if($hospital->receivingTarget == '2'){ //発注部署
                $divisionId = $card->divisionId;
            }
            
            $order_item = Order::where('orderNumber',$card->orderNumber)->get();
            $order_item = $order_item->data->all();
            $inventory_adjustment_trdata = [];
            
            foreach ($order_item as $key => $item)
            {
                $count = (int)$item->orderQuantity * (int)$item->quantity;
                if ($count < 0) { continue; } //マイナス発注は発注中個数の計算をしない。
                if ($count != 0)
                {
                    $inventory_adjustment_trdata[] = [
                        'divisionId' => $divisionId,
                        'inHospitalItemId' => $item->inHospitalItemId,
                        'count' => 0,
                        'pattern' => 2,
                        'hospitalId' => $user_info->getHospitalId(),
                        'orderWithinCount' => -$count
                    ];
                }
            }
            
            $result = OrderHistory::where('orderNumber',$card->orderNumber)->delete();
            InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['delete']);
            $content = $content->toJson();

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['delete']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }

    public function correction()
    {
        
        global $SPIRAL;

        $title = 'JoyPla 発注書';

        try {
            
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser() || $user_info->isUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $card_Id = $SPIRAL->getCardId();
            if($card_Id == null)
            {   
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $card = OrderHistory::where('hospitalId',$user_info->getHospitalId())->find($card_Id)->get();
            $card = $card->data->get(0);
            
            if($user_info->isUser() && $card->divisionId != $user_info->getDivisionId())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $order_items = OrderedItemInfoView::where('hospitalId',$user_info->getHospitalId())->sort('id','asc')->where('orderNumber',$card->orderNumber)->get();
            $order_items = $order_items->data->all();

            foreach($order_items as &$item)
            {
                $item->orderQuantityCorrection = $item->orderQuantity;
                if($item->orderQuantity > 0){
                    $item->max = (int)$item->orderQuantity;
                    $item->min = (int)$item->receivingNum;
                } else 
                {
                    $item->max = (int)$item->receivingNum;
                    $item->min = (int)$item->orderQuantity;
                }
            }

            $content = $this->view('NewJoyPla/view/OrderCorrectionSlipDetail', [
                'user_info' => $user_info,
                'order_items' => $order_items,
                'csrf_token' => Csrf::generate(16),
                'link_title' => "発注書一覧",
            	'link' => '%url/rel:mpgt:Order%&Action=orderedList',
            ] , false);

        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false);

        } finally {
            
            $style   = $this->view('NewJoyPla/view/template/parts/DetailPrintCss', [] , false)->render();
            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [] , false)->render();

            $script   = $this->view('NewJoyPla/view/template/parts/Script', [] , false)->render();
            $head = $this->view('NewJoyPla/view/template/parts/Head', ['new'=> true] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
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
            ],false);
        }
    }

    public function orderItemUpdate($SPIRAL) : View
    {

        try{
            
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $card_Id = $SPIRAL->getCardId();
            if($card_Id == null)
            {   
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $card = OrderHistory::where('hospitalId',$user_info->getHospitalId())->find($card_Id)->plain()->get();
            $card = $card->data->get(0);
            
            $items = $SPIRAL->getParam('items');

            $request = new stdClass;
            $request->items = [];
            $request->orderNumber = $card->orderNumber;
            $request->hospitalId = $card->hospitalId;
            $request->orderStatus = $card->orderStatus;
            $request->ordercomment = urldecode($SPIRAL->getParam('comment'));

            foreach($items as $i)
            {
                $r = new stdClass ;
                $r->orderCNumber = $i['orderCNumber'];
                $r->orderQuantityCorrection = $i['orderQuantityCorrection'];   
                $request->items[] = $r;
            }

            $order_items = OrderedItemInfoView::where('hospitalId',$request->hospitalId)->sort('id','asc')->where('orderNumber',$request->orderNumber)->plain();

            if(count($request->items) === 0){
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            foreach($request->items as $r)
            {
                $order_items->orWhere('orderCNumber',$r->orderCNumber);
            }
            $order_items = $order_items->get();
            $order_items = $order_items->data->all();

            $search_values = array_column($request->items,'orderCNumber');
            $validation_msg = [];
            $exist = false;
            $update = [];

            if($request->ordercomment !== $card->ordercomment)
            {
                $exist = true;
                if(strlen($request->ordercomment) > 512)
                {
                    $validation_msg['comment'] ='半角256文字以内,全角512文字以内で入力してください';
                }
            }


            foreach($order_items as $key => &$o_item)
            {  
                $key = array_search($o_item->orderCNumber , $search_values);
                if($key === false)
                {
                    throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                }

                if((int)$o_item->orderQuantity === (int)$request->items[$key]->orderQuantityCorrection)
                {
                    continue;
                }
                else
                {
                    $exist = true;
                }

                if($o_item->orderQuantity > 0){
                    
                    if($request->items[$key]->orderQuantityCorrection > $o_item->orderQuantity )
                    {
                        $validation_msg[$key] = '発注数量を上回る変更はできません';
                        continue;
                    }
    
                    if($o_item->receivingNum > 0 && $request->items[$key]->orderQuantityCorrection < $o_item->receivingNum )
                    {
                        $validation_msg[$key] = '入庫数量を下回る変更はできません';
                        continue;
                    }

                    if( $request->items[$key]->orderQuantityCorrection < 0 || $request->items[$key]->orderQuantityCorrection == '')
                    {
                        $validation_msg[$key] = '0を下回る変更はできません';
                        continue;
                    }
                }
                else
                {
                    /* 
                    $validation_msg[$key] = 'マイナス発注の商品は数量を変更できません';
                    continue;
                    */
                    if($request->items[$key]->orderQuantityCorrection < $o_item->orderQuantity )
                    {
                        $validation_msg[$key] = '発注数量を下回る変更はできません';
                        continue;
                    }
    
                    if($o_item->receivingNum > 0 && $request->items[$key]->orderQuantityCorrection > $o_item->receivingNum )
                    {
                        $validation_msg[$key] = '入庫数量を上回る変更はできません';
                        continue;
                    }

                    if( $request->items[$key]->orderQuantityCorrection > 0  || $request->items[$key]->orderQuantityCorrection == '')
                    {
                        $validation_msg[$key] = '0を上回る変更はできません';
                        continue;
                    }
                }
                $o_item->orderQuantity = $request->items[$key]->orderQuantityCorrection;
                $o_item->orderPrice = ($o_item->price * $request->items[$key]->orderQuantityCorrection);
                $o_item->receivingFlag = (int)($o_item->orderQuantity == $o_item->receivingNum);
                $update[$key]['orderCNumber'] = $o_item->orderCNumber;
                $update[$key]['orderQuantity'] = $request->items[$key]->orderQuantityCorrection;
                $update[$key]['orderPrice'] = ($o_item->price * $request->items[$key]->orderQuantityCorrection);
                $update[$key]['receivingFlag'] = (int)($o_item->orderQuantity == $o_item->receivingNum);
            }

            if( count($validation_msg) > 0 )
            {
                $message = '';
                foreach($validation_msg as $key => $msg)
                {
                    if($key === 'comment'){
                        $message .=  "備考: ".$msg."<br>";
                    }
                    else{
                        $message .=  ( $key + 1 )."行目: ".$msg."<br>";
                    }
                }

                $content = new ApiResponse($validation_msg, 0, 102, $message, ['update']);
                $content = $content->toJson();
                return $content;
            }

            if( $exist === false )
            {
                throw new Exception("更新対象がありません",FactoryApiErrorCode::factory(191)->getCode());
            }

            
            $response = OrderHistory::where('orderNumber',$request->orderNumber)->update([
                'totalAmount' => collect(array_column($order_items,'orderPrice'))->sum(),
                'orderStatus' => $this->checkPattern($order_items, $request->orderStatus),
                'ordercomment' => $request->ordercomment
            ]);

            $result = Order::bulkUpdate('orderCNumber',$update);

            //ここからメール送信
            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())
                ->plain()
                ->value('hospitalName')
                ->value('postalCode')
                ->value('prefectures')
                ->value('address')
                ->get();
            $hospital = $hospital->data->get(0);

            $divison = Division::where('divisionId',$card->divisionId)
            ->plain()
            ->value('divisionName')
            ->get();
            $divison = $divison->data->get(0);

            $mail_body = $this->view('NewJoyPla/view/Mail/OrderItemUpdate', [
                'name' => '%val:usr:name%',
                'hospital_name' => $hospital->hospitalName,
                'postal_code' => $hospital->postalCode,
                'prefectures' => $hospital->prefectures,
                'address' => $hospital->address,
                'division_name' => $divison->divisionName,
                'order_date'=> $card->orderTime,
                'order_number'=> $card->orderNumber,
                'item_num'=> $card->itemsNumber,
                'total_price'=>"￥".number_format_jp(collect(array_column($order_items,'orderPrice'))->sum(),2),
                'comment' => $request->ordercomment,
                'slip_url' => OROSHI_OrderDetailAccess."?searchValue=".$card->orderNumber,
                'login_url' => OROSHI_LOGIN_URL,
            ] , false)->render();
            
            $select_name = $this->makeId($card->distributorId);

            $test = DistributorAffiliationView::selectName($select_name)
                ->rule(
                    ['name'=>'distributorId','label'=>'name_'.$card->distributorId,'value1'=>$card->distributorId,'condition'=>'matches']
                )
                ->rule(
                    ['name'=>'invitingAgree','label'=>'invitingAgree','value1'=>'t','condition'=>'is_boolean']
                )->filterCreate();

            $test = DistributorAffiliationView::selectRule($select_name)
                ->body($mail_body)
                ->subject("[JoyPla] 発注書に変更がありました")
                ->from(FROM_ADDRESS,FROM_NAME)
                ->send();

            $content = new ApiResponse($result->data, $result->count, 0, '更新が完了しました', ['update']);
            $content = $content->toJson();

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['update']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
}

/***
 * 実行
 */

$OrderSlipDetailControllerController = new OrderSlipDetailController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'orderedDeleteAPI')
    {
        echo $OrderSlipDetailControllerController->orderedDeleteAPI()->render();
    }
    else if($action === 'receivingAPI')
    {
        echo $OrderSlipDetailControllerController->receivingAPI()->render();
    }
    else if($action === 'receipt')
    {
        echo $OrderSlipDetailControllerController->index(true)->render();
    }
    else if($action === 'correction')
    {
        echo $OrderSlipDetailControllerController->correction()->render();
    }
    else if($action === 'orderItemUpdate'){
        echo $OrderSlipDetailControllerController->orderItemUpdate($SPIRAL)->render();
    }
    else
    {
        echo $OrderSlipDetailControllerController->index(false)->render();
    }
}