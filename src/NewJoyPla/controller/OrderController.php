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