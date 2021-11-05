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
use App\Model\StockView;
use App\Model\Stock;
use App\Model\Card;
use App\Model\InventoryAdjustmentTransaction;
use App\Model\Distributor;
use App\Model\Order;
use App\Model\OrderHistory;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class OrderController extends Controller
{
    
    public function __construct()
    {
    }
    
    public function regUnorderedAPI()
    {
        global $SPIRAL;
        $content = '';
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);

            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            if ($user_info->isHospitalUser() && $user_info->isApprover())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $order_data = $SPIRAL->getParam('ordered');
            $order_data = $this->requestUrldecode($order_data);
            $divisionId = $SPIRAL->getParam('divisionId');
            $order[$divisionId] = $order_data;
            $content = $this->unordered($order);

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    public function regUnorderedDivisionApi()
    {   
        global $SPIRAL;
        $content = '';
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);

            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            if ($user_info->isHospitalUser() && $user_info->isApprover())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $order_data = $SPIRAL->getParam('ordered');
            
			$stockview = StockView::where('notUsedFlag','0')->where('hospitalId', $user_info->getHospitalId());	
			
            foreach($order_data as $order_items)
            {
                if($order_items['orderQuantity'] == 0){continue;}
                $stockview->orWhere('id',$order_items['id']);
            }
            $stockview = $stockview->get();
            $order_array = [];
            foreach($order_data as $order_items)
            {
                if($order_items['orderQuantity'] == 0){continue;}
                foreach($stockview->data->all() as $item){
                    if($order_items['inHospitalItemId'] == $item->inHospitalItemId)
                    {
                        if (array_key_exists($item->divisionId, $order_array) === false) {
                            $order_array[$item->divisionId] = [];
                        } 
                        $order_array[$item->divisionId][] = [
            				"maker" => $item->makerName,
            				"shouhinName" => $item->itemName,
            				"code" => $item->itemCode,
            				"kikaku" => $item->itemStandard,
            				"irisu" => $item->quantity,
            				"kakaku" => $item->price,
            				"jan" => $item->itemJANCode,
            				"oroshi" => $item->distributorName,
            				"recordId" => $item->inHospitalItemId,
            				"unit" => $item->quantityUnit,
            				"itemUnit" => $item->itemUnit,
            				"distributorId" => $item->distributorId,
            				"catalogNo" => $item->catalogNo,
            				"labelId" => $item->labelId,
            				"unitPrice" => $item->unitPrice,
            				"lotFlag" => ($item->lotManagement == 1 )? "はい": "",
            				"lotFlagBool" => $item->lotManagement,
            				"itemId" => $item->itemId,
            				"countNum" => ((int)$order_items['orderQuantity'] * (int)$item->quantity)
            			];
                    }
                }
            }
            $content = $this->unordered($order_array);

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    private function unordered( $order_data )
    {
        global $SPIRAL;
        $user_info = new UserInfo($SPIRAL);
        $insert_data = [];
        $history_data = [];
        $distributorDB = Distributor::where('hospitalId',$user_info->getHospitalId());
        foreach( $order_data as $divisionId => $ordered )
        {
            foreach( $ordered as $key => $order)
            {
                $distributorDB->orWhere('distributorId',$order['distributorId']);
            }
        }
        $distributorDB = $distributorDB->get();
        $distributorDB = $distributorDB->data->all();
        //個数を合算する
        foreach( $order_data as $divisionId => $ordered )
        {
            $remaked = [];
            $arrayEachDistributor = [];
            foreach( $ordered as $key => $order)
            {
                if (array_key_exists($order['recordId'], $remaked) === false) {
                    $remaked[$order['recordId']] = $order;
                } 
                else 
                {
                    $remaked[$order['recordId']]['countNum'] = (int)$remaked[$order['recordId']]['countNum'] + (int)$order['orderQuantity'];
                }
            }
            
            $ordered = $remaked;
            
            foreach ($distributorDB as $distributor)
            {
                $distributorId = $distributor->distributorId;
                if (array_search($distributorId, $arrayEachDistributor) === false) {
                    $arrayEachDistributor[$distributorId] = [];
                }
                
                foreach ($ordered as $order)
                {
                    if ($order['distributorId'] == $distributorId) { $arrayEachDistributor[$distributorId][] = $order; }
                }
            }
            foreach ($arrayEachDistributor as $distributor_id => $order_data)
            {
                $in_hospital_item_ids = [];
                $total_amount = 0;
                $ordered_id = $this->makeId('03');
                foreach($order_data as $data)
                {
                    $sign = '';
                    $inHPItemid = $data['recordId'];
                    if ((int)$data['countNum'] < 0) { $sign = '-'; }
                    if (floor(abs((int)$data['countNum']) / (int)$data['irisu']) > 0)
                    {
                        $insert_data[] = [
                            'hospitalId' => $user_info->getHospitalId(),
                            'inHospitalItemId' => $inHPItemid,
                            'orderNumber' => $ordered_id,
                            'price' => str_replace(',', '', $data['kakaku']),
                            'orderQuantity' => $sign.floor(abs((int)$data['countNum']) / (int)$data['irisu']),
                            'orderPrice' => $sign.str_replace(',', '', $data['kakaku']) * floor(abs((int)$data['countNum']) / (int)$data['irisu']),
                            'receivingFlag' => '0',
                            'quantity' => $data['irisu'],
                            'quantityUnit' => $data['unit'],
                            'itemUnit' => $data['itemUnit'],
                            'divisionId' => $divisionId,
                            'distributorId' => $distributor_id,
                            'itemId' => $data['itemId']
                        ];
                        $total_amount = $total_amount + ($sign.str_replace(',', '', $data['kakaku']) * floor(abs((int)$data['countNum']) / (int)$data['irisu']));
                    }
                    if (array_search($inHPItemid, $in_hospital_item_ids) === false) {
                        $in_hospital_item_ids[] = $inHPItemid;
                    }
                }
                if (count($insert_data) == 0) {
                   continue;
                }
                $history_data[] = [
                    'orderNumber' => $ordered_id,
                    'hospitalId' => $user_info->getHospitalId(),
                    'divisionId' => $divisionId,
                    'itemsNumber' => count($in_hospital_item_ids),//院内商品マスタID数
                    'totalAmount' => $total_amount,
                    'orderStatus' => '1',
                    'hachuRarrival' => '未入庫',
                    'distributorId' => $distributor_id,
                    'ordererUserName' => $user_info->getName()
                ];
            }
        }
        if(count($history_data) == 0 ){
            $content = new ApiResponse([] , 0 , 0, '登録するデータがありませんでした', ['insert']);
            return $content->toJson();
        }
        $result = OrderHistory::insert($history_data);
        $result = Order::insert($insert_data);
        $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['insert']);
        return $content->toJson();
    }
    
    public function unorderedList()
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:Order%";
    
            if ($user_info->isHospitalUser() && ( $user_info->isAdmin() || $user_info->isApprover() ))
            {
                
                $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '未発注書一覧',
                    'table' => '%sf:usr:unorderedSlip:mstfilter%',
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
            } else {
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'table' => '%sf:usr:search99:table%',
                    'title' => '未発注書一覧 - 部署選択',
                    'param' => 'unorderedListForDivision',
                    ] , false);
            }
    
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 未発注書一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
        
    }
    
    
    public function unorderedListForDivision(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ($user_info->isHospitalUser() && ( $user_info->isApprover() || $user_info->isAdmin()) )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ( \App\lib\isMypage() )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
    
            $api_url = "%url/rel:mpgt:Order%";
            
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '未発注書一覧',
                    'table' => '%sf:usr:unorderedSlip:mstfilter%',
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
    
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 未発注書一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function orderedList($arrival_verification_flg = false)
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:Order%";
            
            $title = "発注書一覧";
            $division_param = "orderedListForDivision";
            $param = "";
            if($arrival_verification_flg)
            {
                $title = "入荷照合";
                $division_param = "arrivalVerificationForDivision";
                $param = "receipt";
            }
            
            if ($user_info->isHospitalUser() && ( $user_info->isAdmin() || $user_info->isApprover() ))
            {
                
                $content = $this->view('NewJoyPla/view/template/List', [
                        'title' => $title,
                        'table' => '%sf:usr:orederList:mstfilter%',
                        'param' => $param,
                        'csrf_token' => Csrf::generate(16)
                        ] , false);
            } else {
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'table' => '%sf:usr:search99:table%',
                    'title' => $title.' - 部署選択',
                    'param' => $division_param,
                    ] , false);
            }
    
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla '.$title,
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
        
    }
    
    public function orderedListForDivision($arrival_verification_flg = false): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ($user_info->isHospitalUser() && ( $user_info->isApprover() || $user_info->isAdmin()) )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ( \App\lib\isMypage() )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
    
            $api_url = "%url/rel:mpgt:Order%";
            
            $title = "発注書一覧";
            $param = "";
            if($arrival_verification_flg)
            {
                $title = "入荷照合";
                $param = "receipt";
            }
            
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => $title,
                    'table' => '%sf:usr:orederList:mstfilter%',
                    'param' => $param,
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
                    
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
                
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla '.$title,
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function orderAdjustment()
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:Order%";
    
            if ($user_info->isHospitalUser() && ( $user_info->isAdmin() || $user_info->isApprover() ))
            {
                $division_data = Division::where('hospitalId',$user_info->getHospitalId())->get();
                $is_exist_unorder = OrderHistory::where('hospitalId',$user_info->getHospitalId())->where('orderStatus','1')->count();
                $content = $this->view('NewJoyPla/view/OrderAdjustment', [
                    'divisionData' => $division_data->data->all(),
                    'isExistUnorder' => ($is_exist_unorder > 0),
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
            } else {
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'table' => '%sf:usr:search98:table%',
                    'title' => '定数発注 - 部署選択',
                    'param' => 'orderAdjustmentForDivision',
                    ] , false);
            }
    
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 定数発注',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function orderAdjustmentForDivision(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ($user_info->isHospitalUser() && ( $user_info->isApprover() || $user_info->isAdmin()) )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ( \App\lib\isMypage() )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $division_data = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            $is_exist_unorder = OrderHistory::where('hospitalId',$user_info->getHospitalId())->where('orderStatus','1')->count();
    
            $api_url = "%url/rel:mpgt:Order%";
            
            $content = $this->view('NewJoyPla/view/OrderAdjustment', [
                    'divisionData' => $division_data->data->all(),
                    'isExistUnorder' => ($is_exist_unorder > 0),
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
    
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 発注調整',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
}
/***
 * 実行
 */
$OrderController = new OrderController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'regUnorderedAPI')
    {
        echo $OrderController->regUnorderedAPI()->render();
    }
    else if($action === 'regUnorderedDivisionApi')
    {
        echo $OrderController->regUnorderedDivisionApi()->render();
    }
    else if($action === 'unorderedList')
    {
        echo $OrderController->unorderedList()->render();
    }
    else if($action === 'unorderedListForDivision')
    {
        echo $OrderController->unorderedListForDivision()->render();
    }
    else if($action === 'orderedList')
    {
        echo $OrderController->orderedList(false)->render();
    }
    else if($action === 'orderedListForDivision')
    {
        echo $OrderController->orderedListForDivision(false)->render();
    }
    else if($action === 'orderAdjustment')
    {
        echo $OrderController->orderAdjustment()->render();
    }
    else if($action === 'orderAdjustmentForDivision')
    {
        echo $OrderController->orderAdjustmentForDivision()->render();
    }
    else if($action === 'arrivalVerification')
    {
        echo $OrderController->orderedList(true)->render();
    }
    else if($action === 'arrivalVerificationForDivision')
    {
        echo $OrderController->orderedListForDivision(true)->render();
    }
    else 
    {
        echo $OrderController->unorderedList()->render();
    }
}