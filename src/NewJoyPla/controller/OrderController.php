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
use App\Model\InHospitalItem;
use App\Model\StockView;
use App\Model\Stock;
use App\Model\Card;
use App\Model\InventoryAdjustmentTransaction;
use App\Model\Distributor;
use App\Model\Order;
use App\Model\OrderHistory;
use App\Model\OrderedItemView;

use App\Model\Receiving;
use App\Model\ReceivingHistory;



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

			$in_hospital_item = InHospitalItem::where('notUsedFlag','0')->where('hospitalId', $user_info->getHospitalId());	

            $puls = [];
            $minus = [];
            foreach($order_data as $key => $order_items)
            {
                if($order_items['countNum'] == 0){continue;}
                $in_hospital_item->orWhere('inHospitalItemId',$order_items['recordId']);
                
                if($order_items['countNum'] > 0)
                {
                    if (array_key_exists($order_items['recordId'], $puls) === false) {
                        $puls[$order_items['recordId']] = $order_items;
                        $puls[$order_items['recordId']]['countNum'] = 0;
                    }
                    
                    $puls[$order_items['recordId']]['countNum'] += (int)$order_items['countNum'];
                }
                if($order_items['countNum'] < 0)
                {
                    if (array_key_exists($order_items['recordId'], $minus) === false) {
                        $minus[$order_items['recordId']] = $order_items;
                        $minus[$order_items['recordId']]['countNum'] = 0;
                    }
                    
                    $minus[$order_items['recordId']]['countNum'] += (int)$order_items['countNum'];
                }
            }

            $order_data = [];
            foreach($puls as $p)
            {
                $order_data[] = $p;
            }
            foreach($minus as $m)
            {
                $order_data[] = $m;
            }

            $in_hospital_item = $in_hospital_item->get();

            $order_array = [];
            foreach($order_data as $order_items)
            {
                if($order_items['countNum'] == 0){continue;}
                foreach($in_hospital_item->data->all() as $item){
                    if($order_items['recordId'] == $item->inHospitalItemId)
                    {
                        if (array_key_exists($divisionId, $order_array) === false) {
                            $order_array[$divisionId] = [];
                        }
                        $order_array[$divisionId][] = [
            				"makerName" => $item->makerName,
            				"itemName" => $item->itemName,
            				"itemCode" => $item->itemCode,
            				"itemStandard" => $item->itemStandard,
            				"quantity" => $item->quantity,
            				"price" => $item->price,
            				"itemJANCode" => $item->itemJANCode,
            				"distributorName" => $item->distributorName,
            				"inHospitalItemId" => $item->inHospitalItemId,
            				"quantityUnit" => $item->quantityUnit,
            				"itemUnit" => $item->itemUnit,
            				"distributorId" => $item->distributorId,
            				"catalogNo" => $item->catalogNo,
            				"labelId" => $item->labelId,
            				"unitPrice" => $item->unitPrice,
            				"lotFlag" => ($item->lotManagement == 1 )? "はい": "",
            				"lotManagement" => $item->lotManagement,
            				"itemId" => $item->itemId,
            				"orderQuantity" => ((int)$order_items['countNum'] > 0)? floor( (int)$order_items['countNum'] / (int)$item->quantity ) : '-'.floor( abs((int)$order_items['countNum']) / (int)$item->quantity )  
            			];
                    }
                }
            }
            $integrate = ( $SPIRAL->getParam('integrate') == 'true');
            $content = $this->unordered($order_array , $integrate);

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
            				"makerName" => $item->makerName,
            				"itemName" => $item->itemName,
            				"itemCode" => $item->itemCode,
            				"itemStandard" => $item->itemStandard,
            				"quantity" => $item->quantity,
            				"price" => $item->price,
            				"itemJANCode" => $item->itemJANCode,
            				"distributorName" => $item->distributorName,
            				"inHospitalItemId" => $item->inHospitalItemId,
            				"quantityUnit" => $item->quantityUnit,
            				"itemUnit" => $item->itemUnit,
            				"distributorId" => $item->distributorId,
            				"catalogNo" => $item->catalogNo,
            				"labelId" => $item->labelId,
            				"unitPrice" => $item->unitPrice,
            				"lotFlag" => ($item->lotManagement == 1 )? "はい": "",
            				"lotManagement" => $item->lotManagement,
            				"itemId" => $item->itemId,
            				"orderQuantity" => (int)$order_items['orderQuantity'] //定数発注の場合は個数単位
            			];
                    }
                }
            }
            $integrate = ( $SPIRAL->getParam('integrate') == 'true');
            $content = $this->unordered($order_array , $integrate);

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    private function unordered( $order_data , bool $integrate)
    {
        global $SPIRAL;
        $user_info = new UserInfo($SPIRAL);
        $unorder_historys = [];
        if($integrate)
        {
            $unorder_historys = OrderHistory::where('hospitalId',$user_info->getHospitalId())->where('orderStatus','1')->sort('id','desc')->get();
            $unorder_historys = $unorder_historys->data->all(); //未発注履歴
        }
        
        /** 対応する履歴IDを取得 */
        $history_ids = [];
        foreach($order_data as $divisionId => $items)
        {
            foreach($items as $key => $item)
            {
                if($item['orderQuantity'] == 0){ continue; }
                $exist = false;
                $divisionId; //search1
                $distributorId = $item['distributorId']; //search2
                $puls = ($item['orderQuantity'] > 0); //search3
                //既存の履歴の確認
                foreach($unorder_historys as $history)
                {
                    if($exist){ break; }
                    if(
                        $history->divisionId === $divisionId &&
                        $history->distributorId === $distributorId &&
                        ($history->totalAmount >= 0) === $puls
                    )
                    {
                        $history_ids[] = [
                            'divisionId' => $history->divisionId,
                            'distributorId' => $history->distributorId,
                            'puls' => ($history->totalAmount >= 0),
                            'orderNumber' => $history->orderNumber,
                        ];
                        $order_data[$divisionId][$key]['orderNumber'] = $history->orderNumber;
                        $exist = true;
                    }
                }
                //履歴IDから再検索
                foreach($history_ids as $history)
                {
                    if($exist){ break; }
                    if(
                        $history['divisionId'] === $divisionId &&
                        $history['distributorId'] === $distributorId &&
                        $history['puls'] === $puls
                    )
                    {
                        $order_data[$divisionId][$key]['orderNumber'] = $history['orderNumber'];
                        $exist = true;
                    }
                }

                //履歴IDを作成
                if(!$exist)
                {
                    $id = $this->makeId('03');
                    $history_ids[] = [
                        'divisionId' => $divisionId,
                        'distributorId' => $item['distributorId'],
                        'puls' => ($item['orderQuantity'] > 0),
                        'orderNumber' => $id,
                    ];
                    $order_data[$divisionId][$key]['orderNumber'] = $id;
                }
            }
        }

        $ordered_items = [];
        if(count($history_ids) > 0)
        {
            $ordered_items = OrderedItemView::where('hospitalId',$user_info->getHospitalId());
            foreach($history_ids as $h)
            {
                $ordered_items->orWhere('orderNumber',$h['orderNumber']);
            }
            $ordered_items = $ordered_items->get();
            $ordered_items = $ordered_items->data->all();
        }

        $upsert = [];
        foreach($ordered_items as $item)
        {
            $order_quantity = $item->orderQuantity;
            if($order_quantity  == 0){ continue; }
            $price = $item->price;
            if (array_key_exists($item->divisionId, $order_data) === true) {
                foreach($order_data[$item->divisionId] as $key => $i)
                {
                    if(
                        $i['inHospitalItemId'] === $item->inHospitalItemId &&
                        $i['orderNumber'] === $item->orderNumber &&
                        ($i['orderQuantity'] > 0) === ($item->orderQuantity > 0) &&
                        $i['distributorId'] === $item->distributorId
                    )
                    {
                        $order_quantity = (int)$order_quantity + (int)$i['orderQuantity'];
                        $price = $i['price'];
                        unset($order_data[$item->divisionId][$key]); //未発注商品と一致した場合、配列から削除
                        break;
                    }
                }
            }
            $upsert[] = [
                'registrationTime' => $item->registrationTime,
                'hospitalId' => $item->hospitalId,
                'inHospitalItemId' => $item->inHospitalItemId,
                'orderNumber' => $item->orderNumber,
                'orderCNumber' => $item->orderCNumber,
                'price' => $price,
                'orderQuantity' => $order_quantity,
                'orderPrice' => ($order_quantity * $price),
                'receivingFlag' => $item->receivingFlag,
                'quantity' => $item->quantity,
                'quantityUnit' => $item->quantityUnit,
                'itemUnit' => $item->itemUnit,
                'divisionId' => $item->divisionId,
                'distributorId' => $item->distributorId,
                'itemId' => $item->itemId,
            ];
        }
        //一致しなかったものを新規登録として作成
        foreach($order_data as $divisionId => $order_items)
        {
            foreach($order_items as $item)
            {
                if($item['orderQuantity']  == 0){ continue; }
                $upsert[] = [
                    'registrationTime' => 'now',
                    'hospitalId' => $user_info->getHospitalId(),
                    'inHospitalItemId' => $item['inHospitalItemId'],
                    'orderNumber' => $item['orderNumber'],
                    'orderCNumber' => '',
                    'price' => $item['price'],
                    'orderQuantity' => $item['orderQuantity'],
                    'orderPrice' => ($item['orderQuantity'] * $item['price']),
                    'receivingFlag' => '0',
                    'quantity' => $item['quantity'],
                    'quantityUnit' => $item['quantityUnit'],
                    'itemUnit' => $item['itemUnit'],
                    'divisionId' => $divisionId,
                    'distributorId' => $item['distributorId'],
                    'itemId' => $item['itemId'],
                ];
            }
        }

        $history = [];
        foreach($upsert as $item)
        {
            if (array_key_exists($item['orderNumber'], $history) === false)
            {
                $history[$item['orderNumber']] = 
                [
                    'registrationTime' => $item['registrationTime'],
                    'orderNumber' => $item['orderNumber'],
                    'hospitalId' => $user_info->getHospitalId(),
                    'divisionId' => $item['divisionId'],
                    'orderStatus' => '1',
                    'itemsNumber' => [],
                    'totalAmount' => 0,
                    'hachuRarrival' => '未入庫',
                    'distributorId' => $item['distributorId'],
                    'ordererUserName' => $user_info->getName()
                ];
            }
            if(array_search($item['inHospitalItemId'], $history[$item['orderNumber']]['itemsNumber']) === false)
            {
                $history[$item['orderNumber']]['itemsNumber'][] = $item['inHospitalItemId'];
            }
            $history[$item['orderNumber']]['totalAmount'] += (float)$item['orderPrice'];
        }

        foreach($history as $k => $h)
        {
            $history[$k]['itemsNumber'] = count($history[$k]['itemsNumber']);
        }

        $history = array_values($history);
        $upsert = array_values($upsert);
        if(count($history) !== 0 && count($upsert) !== 0){
            $result = OrderHistory::upsert('orderNumber',$history);
            $result = Order::upsert('orderCNumber',$upsert);
        }
        if(count($history) === 0 && count($upsert) === 0){
            $content = new ApiResponse([], 0 , 0, "is not data", ['insert']);
            return $content->toJson();
        }
        
        $content = new ApiResponse($history , $result->count , $result->code, $result->message, ['insert']);
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
            
            if ($user_info->isApprover())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
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
    
    
    public function individualEntry()
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $division = Division::where('hospitalId',$user_info->getHospitalId());
            
            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);

            if($hospital->receivingTarget == '1')
            {
                $division->where('divisionType','1');
            }
            
            if($hospital->receivingTarget == '2' && $user_info->isUser())
            {
                $division->where('divisionId',$user_info->getDivisionId());
            }

            $division = $division->get();
            $division = $division->data->all();
            
            $api_url = "%url/rel:mpgt:Order%";
            $content = $this->view('NewJoyPla/view/IndividualEntry', [
                    'csrf_token' => Csrf::generate(16),
                    'division' => $division,
                    'api_url' => $api_url 
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
                'title'     => 'JoyPla 個別入荷',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }

    
    public function individualRegistApi()
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
            
            if($user_info->isApprover())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $receiving_items = $SPIRAL->getParam('items');
            $receiving_items = $this->requestUrldecode($receiving_items);

            $re_items = [];
            foreach($receiving_items as &$i)
            {
                $i['lotNumber'] = (is_null($i['lotNumber']))? "" : $i['lotNumber'];
                $i['lotDate'] = (is_null($i['lotDate']))? "" : $i['lotDate'];
                $re_items = $this->uniqLotMerge($re_items , $i['lotNumber'] , $i['lotDate'] , $i['recordId'] , (int)$i['count']);
            }

            $receiving_items = $this->getInHospitalItems($re_items);

            $division_id = $SPIRAL->getParam('divisionId');
            //倉庫納品
            //    -　倉庫を選択していること
            //担当者の場合、発注書は自身の部署の発注書のみ
            //その他の場合、発注書はどれでも
            //部署納品
            //    -　部署を選択していること
            //発注書部署 = 納品先部署（ = 倉庫納品の担当者ロジック）
            
            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
            
            $order_item = OrderedItemView::where('hospitalId',$user_info->getHospitalId())->orWhere('receivingFlag','0')->orWhere('receivingFlag','0','ISNULL')->where('orderStatus','1','!=')->sort('id','asc');
            if( $user_info->isUser())
            {
                $order_item->where('divisionId',$user_info->getDivisionId());
            }
            else if($hospital->receivingTarget == '2')
            {
                $order_item->where('divisionId',$division_id);
            }

            foreach($receiving_items as $i)
            {
                $order_item->orWhere('inHospitalItemId',$i->inHospitalItemId);
            }
            
            $order_item = $order_item->get();
            $order_count = $order_item->count;
            $order_item = $order_item->data->all();//対象の発注商品
            
            //発注履歴ID 1..1 入庫履歴ID
            $recept_ids = $this->createReceiveIds($order_item);
            $result = $this->createReceiveData($order_item , $receiving_items, $recept_ids );

            $receivingTargetDivision = $division_id;
            if($hospital->receivingTarget == '1')
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionType','1')->get();
                $division = $division->data->get(0);
                $receivingTargetDivision = $division->divisionId;
            }

            Order::bulkUpdate('orderCNumber',$this->makeOrderUpdateArray($result['order_update_items']));
            OrderHistory::bulkUpdate('orderNumber',$this->makeOrderHistoryUpdateArray($result['order_update_items']));
            
            $tmp = $this->makeReceptionHistoryInsertArray($result['receiving_insert_items'] , $receivingTargetDivision );
            ReceivingHistory::insert($tmp);

            Receiving::insert($this->makeReceptionInsertArray($result['receiving_insert_items'] , $receivingTargetDivision ));

            InventoryAdjustmentTransaction::insert($this->makeInventoryAdjustmentTransaction($result['receiving_insert_items'] , $receivingTargetDivision));

            $message = implode('<br>',array_keys($tmp));

            $content = new ApiResponse($tmp,0,0,'登録が完了しました<br>生成された検収番号<br>'.$message,['insert']);
            $content = $content->toJson();

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }

    private function makeInventoryAdjustmentTransaction($receiving_items  , $target_division_id)
    {
        $inventory_adjustment_trdata = [];
        
        foreach($receiving_items as $i)
        {
            if($i['lotNumber'] && $i['lotDate']){
                $inventory_adjustment_trdata[] = [
                    'divisionId' => $target_division_id,
                    'pattern' => 3,
                    'inHospitalItemId' => $i['inHospitalItemId'],
                    'count' => (int)$i['quantity'] * (int)$i['receivingCount'],
                    'orderWithinCount' => ((int)$i['receivingCount'] < 0) ? 0 : -((int)$i['quantity'] * (int)$i['receivingCount']),
                    'hospitalId' => $i['hospitalId'],
                    'lotUniqueKey' => $i['hospitalId'].$target_division_id.$i['inHospitalItemId'].$i['lotNumber'].$i['lotDate'],
                    'lotNumber' => $i['lotNumber'],
                    'lotDate' => $i['lotDate'],
                ];
            }
            else
            {
                $inventory_adjustment_trdata[] = [
                    'divisionId' => $target_division_id,
                    'pattern' => 3,
                    'inHospitalItemId' => $i['inHospitalItemId'],
                    'count' => (int)$i['quantity'] * (int)$i['receivingCount'],
                    'orderWithinCount' => ((int)$i['receivingCount'] < 0) ? 0 : -((int)$i['quantity'] * (int)$i['receivingCount']),
                    'hospitalId' => $i['hospitalId'],
                ];
            }
        }

        return $inventory_adjustment_trdata;
    }

    private function getInHospitalItems($items)
    {
        global $SPIRAL;
        $user_info = new UserInfo($SPIRAL);
        $instance = InHospitalItem::where('hospitalId',$user_info->getHospitalId());
        foreach($items as $i)
        {
            $instance->orWhere('inHospitalItemId',$i->inHospitalItemId);
        }
        $instance = $instance->get();
        $instance = $instance->data->all();

        foreach($items as &$i)
        {
            foreach($instance as $ins)
            {
                if($i->inHospitalItemId === $ins->inHospitalItemId )
                {
                    $i->distributorId = $ins->distributorId;
                }
            }
        }

        return $items;
    }
    
    //ロット＋期限＋院内商品IDでユニーク化
    private function uniqLotMerge(array $items ,string $lotNumber = "",string $lotDate  = "" , string $inHospitalItemId ,int $count)
    {
        $exist = false;
        foreach($items as $i)
        {
            if($i->lotNumber == $lotNumber &&
                $i->lotDate == $lotDate &&
                $i->inHospitalItemId == $inHospitalItemId &&
                $i->count > 0
            )
            {
                $i->count += $count;
                $exist = true;
                break;
            }
            if($i->lotNumber == $lotNumber &&
                $i->lotDate == $lotDate &&
                $i->inHospitalItemId == $inHospitalItemId &&
                $i->count < 0
            )
            {
                $i->count += $count;
                $exist = true;
                break;
            }
        }
        if(!$exist)
        {
            $new = new stdClass;
            $new->lotNumber = $lotNumber;
            $new->lotDate = $lotDate;
            $new->inHospitalItemId = $inHospitalItemId;
            $new->count = $count;
            $items[] = $new;
        }
        
        return $items;
    }


    private function createReceiveIds($order_items)
    {
        $ids = [];
        foreach($order_items as $o)
        {
            if(! array_key_exists($o->orderNumber , $ids))
            {
                $temp = new stdClass;
                $temp->id = $this->makeId('04');
                $temp->create_flg = false;
                $ids[$o->orderNumber] = $temp;
            }
        }
        return $ids;
    } 

    private function createReceiveData($order_items , $receptive_items , $ids)
    {
        global $SPIRAL;
        $user_info = new UserInfo($SPIRAL);
        //Divisionid　は取得の時に抽出済み
        $receiving_insert_items = [];
        $order_update_items = [];
        foreach($receptive_items as $r)
        {
            foreach($order_items as &$o)
            {
                if($r->count === 0){ break; }
                if(
                    $o->inHospitalItemId == $r->inHospitalItemId &&
                    $o->distributorId == $r->distributorId &&
                    (
                        ( $o->orderQuantity < 0 && $r->count < 0 ) ||
                        ( $o->orderQuantity > 0 && $r->count > 0 )
                    )
                )
                {
                    $difference = (int)$o->orderQuantity - (int)$o->receivingNum;
                    if(
                        ($o->orderQuantity > 0 && $difference <= (int)$r->count ) ||
                        ($o->orderQuantity < 0 && $difference >= (int)$r->count ) 
                    )
                    {
                        $r->count = $r->count - $difference;//その後の入庫可能数
                        
                        //$o->receivingNum = (int)$receiving_num + (int)$o->receivingNum;
                        $o->receivingNum = (int)$difference + (int)$o->receivingNum;//入庫数

                        $o->receivingFlag = '1';
                        $order_update_items[] = [
                            'orderCNumber' => $o->orderCNumber,
                            'receivingTime' => 'now',
                            'orderNumber' => $o->orderNumber,
                            'orderQuantity' => $o->orderQuantity,
                            'receivingNum' => $o->receivingNum,
                            'receivingFlag' => $o->receivingFlag,
                        ];

                        $receiving_insert_items[] = [
                            'orderCNumber' => $o->orderCNumber,
                            'receivingCount' => (int)$difference,
                            'orderHistoryId' => $o->orderNumber,
                            'receivingHId' => $ids[$o->orderNumber]->id,
                            'inHospitalItemId' => $o->inHospitalItemId,
                            'quantity' => $o->quantity,
                            'price' => $o->price,
                            'receivingPrice' => $o->price * (int)$difference,
                            'hospitalId' => $user_info->getHospitalId(),
                            'divisionId' => $o->divisionId,
                            'distributorId' => $o->distributorId,
                            'lotNumber' => $r->lotNumber,
                            'lotDate' => $r->lotDate,
                            'itemId' => $o->itemId,
                        ];
                        $ids[$o->orderNumber]->create_flg = true;

                    }
                    else 
                    {
                        $o->receivingNum = (int)$o->receivingNum + (int)$r->count;//入庫数
                        $order_update_items[] = [
                            'orderCNumber' => $o->orderCNumber,
                            'receivingTime' => 'now',
                            'orderNumber' => $o->orderNumber,
                            'orderQuantity' => $o->orderQuantity,
                            'receivingNum' => $o->receivingNum,
                            'receivingFlag' => $o->receivingFlag,
                        ];
                        $receiving_insert_items[] = [
                            'orderCNumber' => $o->orderCNumber,
                            'receivingCount' => $r->count,
                            'orderHistoryId' => $o->orderNumber,
                            'receivingHId' => $ids[$o->orderNumber]->id,
                            'inHospitalItemId' => $o->inHospitalItemId,
                            'quantity' => $o->quantity,
                            'price' => $o->price,
                            'receivingPrice' => $o->price * $r->count,
                            'hospitalId' => $user_info->getHospitalId(),
                            'divisionId' => $o->divisionId,
                            'distributorId' => $o->distributorId,
                            'lotNumber' => $r->lotNumber,
                            'lotDate' => $r->lotDate,
                            'itemId' => $o->itemId,
                        ];
                        $ids[$o->orderNumber]->create_flg = true;
                        $r->count = 0;
                    }
                }
            }
            if($r->count !== 0)
            {
                throw new Exception('発注商品に対して入庫量が多いため、入庫できませんでした', 1 );
            }
        }
        //var_dump(['receiving_insert_items' => $receiving_insert_items , 'order_update_items' => $order_update_items , 'ids' => $ids]);
        return ['receiving_insert_items' => $receiving_insert_items , 'order_update_items' => $order_update_items , 'ids' => $ids];
    } 

    private function makeOrderUpdateArray($order_items)
    {
        $result = [];
        foreach($order_items as $o)
        {
            $result[] = $o;
        }
        return $result ;
    }

    private function makeOrderHistoryUpdateArray($order_items)
    {
        global $SPIRAL;
        $user_info = new UserInfo($SPIRAL);
        $result = [];
        $instance = OrderedItemView::where('hospitalId',$user_info->getHospitalId());
        foreach($order_items as $i)
        {
            $instance->orWhere('orderNumber', $i['orderNumber']);
        }
        $instance = $instance->get();
        $instance = $instance->data->all();

        
        foreach($instance as &$i)
        {
            $i->orderfixflg = ($i->orderQuantity == $i->receivingNum)? "OK" : "NG";
            if(! array_key_exists($i->orderNumber, $result))
            {
                $result[$i->orderNumber] = [
                    'orderNumber' => $i->orderNumber,
                    'receivingTime' => 'now',
                    'orderStatus' => '',
                    'hachuRarrival' => '',
                    'orderItemsStatus' => [],
                ];
            }
            $result[$i->orderNumber]['orderItemsStatus'][] = $i->orderfixflg;
        }

        foreach($result as &$r)
        {
            $r['orderStatus'] = $this->checkOrderStatus($r['orderItemsStatus']);
        }

        return $result ;
    }

    private function checkOrderStatus($s)
    {
        return (array_search( "NG" , $s ) === false )? 6 : 5;
    }

    private function makeReceptionInsertArray($receiving_items , $target_division_id)
    {
        $result = [];
        foreach($receiving_items as $r)
        {
            $result[] = [
                'orderCNumber' => $r['orderCNumber'],
                'receivingCount' => $r['receivingCount'],
                'receivingHId' => $r['receivingHId'],
                'inHospitalItemId' => $r['inHospitalItemId'],
                'price' => $r['price'],
                'receivingPrice' => $r['receivingPrice'],
                'hospitalId' => $r['hospitalId'],
                'divisionId' => $target_division_id,
                'distributorId' => $r['distributorId'],
                'lotNumber' => $r['lotNumber'],
                'lotDate' => $r['lotDate'],
                'itemId' => $r['itemId'],
            ];
        }
        return $result ;
    }
    
    private function makeReceptionHistoryInsertArray($receiving_items , $target_division_id)
    {
        $result = [];
        foreach($receiving_items as $r)
        {
            if(! array_key_exists($r['receivingHId'] , $result))
            {
                $result[$r['receivingHId']] = [
                    'receivingHId' => $r['receivingHId'],
                    'distributorId' => $r['distributorId'],
                    'orderHistoryId' => $r['orderHistoryId'],
                    'hospitalId' => $r['hospitalId'],
                    'divisionId' => $target_division_id,
                    'recevingStatus' => 1,
                    'itemsNumber' => [],
                    'totalAmount' => 0,
                ];
            }
            $result[$r['receivingHId']]['totalAmount'] += (int)$r['receivingPrice'];
            if( array_search( $r['inHospitalItemId'] ,$result[$r['receivingHId']]['itemsNumber']) === false)
            {
                $result[$r['receivingHId']]['itemsNumber'][] = $r['inHospitalItemId'];
            }
        }
        foreach($result as &$r)
        {
            $r['itemsNumber'] = count($r['itemsNumber']);
        }

        return $result ;
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
    else if($action === 'individualEntry')
    {
        echo $OrderController->individualEntry()->render();
    }
    else if($action === 'individualRegistApi')
    {
        echo $OrderController->individualRegistApi()->render();
    }
    else 
    {
        echo $OrderController->unorderedList()->render();
    }
}