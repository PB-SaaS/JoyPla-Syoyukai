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
use App\Model\OrderedItemView;
use App\Model\OrderHistory;
use App\Model\Receiving;
use App\Model\ReceivingHistory;
use App\Model\InventoryAdjustmentTransaction;

use ApiErrorCode\FactoryApiErrorCode;

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
            
            $order_items = OrderedItemView::where('hospitalId',$user_info->getHospitalId())->where('orderNumber',$card->orderNumber)->get();
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

        	$link = '%url/rel:mpgt:Order%&Action=orderedList';
            if($user_info->isUser()){
                if (preg_match("/Action=orderedListForDivision/", $_SERVER['HTTP_REFERER'])) {
            	    $link = $_SERVER['HTTP_REFERER'];
                }
            }
            
            foreach($order_items as $key => $item)
            {
                $order_items[$key]->nowCount = 0;
                $order_items[$key]->possibleNumber = ((int)$item->orderQuantity - (int)$item->receivingNum);
                $order_items[$key]->max = ((int)$item->orderQuantity > 0)? $item->orderQuantity : 0;
                $order_items[$key]->min = ((int)$item->orderQuantity > 0)? 0 : $item->orderQuantity;
            }
            
            $content = $this->view('NewJoyPla/view/OrderSlipDetail', [
                'user_info' => $user_info,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'orderItems' => $order_items,
                'pattern' => $pattern,
                'link' => $link,
                'cardId' => $cardId
            ] , false);

        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false);

        } finally {

            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => $title,
                'content'   => $content->render(),
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
                    if ((!ctype_alnum($item['lotNumber'])) || (strlen($item['lotNumber']) > 20))
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
                        'divisionId' => $divisionId,
                        'lotNumber' => $item['lotNumber'],
                        'lotDate' => $item['lotDate']
                    ];

                    if ($data['lotNumber'] && $data['lotDate']) {
                        $inventory_adjustment_trdata[] = [
                            'divisionId' => $divisionId,
                            'inHospitalItemId' => $item['inHospitalItemId'],
                            'count' => (int)$item['quantity'] * (int)$item['countNum'],
                            'orderWithinCount' => ((int)$item['countNum'] < 0) ? 0 : -((int)$item['quantity'] * (int)$item['countNum']),
                            'hospitalId' => $user_info->getHospitalId(),
                            'lotUniqueKey' => $user_info->getHospitalId().$divisionId.$item['inHospitalItemId'].$item['lotNumber'].$item['lotDate'],
                            'lotNumber' => $item['lotNumber'],
                            'lotDate' => $item['lotDate'],
                            'stockQuantity' => (int)$item['quantity'] * (int)$item['countNum']
                        ];
                    } 
                    else 
                    {
                        $inventory_adjustment_trdata[] = [
                            'divisionId' => $divisionId,
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
                        $receiving_flag = ($order_item->orderQuantity - ((int)$order_item->receivingNum + (int)$count) <= 0) ? '1' : '0';
                        $update_data[] = [
                            'orderCNumber' => $item['orderCNumber'],
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
                if ($count <= 0) { continue; } //マイナス発注は発注中個数の計算をしない。
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
            
            OrderHistory::where('orderNumber',$card->orderNumber)->delete();
            $result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['insert']);
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
    
    private function makeId($id = '00')
    {
        /*
        '02' => HP_BILLING_PAGE,
        '03_unorder' => HP_UNORDER_PAGE,
        '03_order' => HP_ORDER_PAGE,
        '04' => HP_RECEIVING_PAGE,
        '06' => HP_RETERN_PAGE,
        '05' => HP_PAYOUT_PAGE,
        */
        $id .= date("ymdHis");
        $id .= str_pad(substr(rand(),0,3) , 4, "0"); 
        
        return $id;
    }
    private function requestUrldecode(array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->requestUrldecode($value);
            } else {
                $result[$key] = urldecode($value);
            }
        }
        return $result;
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
    else
    {
        echo $OrderSlipDetailControllerController->index()->render();
    }
}