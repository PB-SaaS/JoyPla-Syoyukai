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
use App\Model\Stock;
use App\Model\Card;
use App\Model\Billing;
use App\Model\BillingHistory;
use App\Model\InventoryAdjustmentTransaction;
use App\Model\Distributor;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class ConsumeController extends Controller
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

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ($user_info->isHospitalUser() && $user_info->isApprover())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ($user_info->isHospitalUser() && $user_info->isAdmin())
            {
                 $divisionData = Division::where('hospitalId',$user_info->getHospitalId())->get();
            } else {
                 $divisionData = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }
            
            $useUnitPrice = '';
            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $useUnitPrice = $hospital_data->billingUnitPrice;
    
            $api_url = "%url/rel:mpgt:Consume%";
            $order_api_url = "%url/rel:mpgt:Order%";
    
            $content = $this->view('NewJoyPla/view/GoodsBillingContentEntry', [
                'api_url' => $api_url,
                'order_api_url' => $order_api_url,
                'userInfo' => $user_info,
                'divisionData'=> $divisionData,
                'useUnitPrice'=> $useUnitPrice,
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
                'title'     => 'JoyPla 消費登録/個別発注',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function consumeList(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:Consume%";
    
            if ($user_info->isHospitalUser() && ( $user_info->isAdmin() || $user_info->isApprover() ))
            {
                $content = $this->view('NewJoyPla/view/template/List', [
                        'title' => '消費一覧',
                        'table' => '%sf:usr:goodsBillingList:mstfilter%',
                        'csrf_token' => Csrf::generate(16),
                        ] , false);
            } else {
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'table' => '%sf:usr:search96:table%',
                    'title' => '消費一覧 - 部署選択',
                    'param' => 'consumeListForDivision',
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
                'title'     => 'JoyPla 消費一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function consumeListForDivision(): View
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
    
            $api_url = "%url/rel:mpgt:Consume%";
            
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '消費一覧',
                    'table' => '%sf:usr:goodsBillingList:mstfilter%',
                    'csrf_token' => Csrf::generate(16),
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
                'title'     => 'JoyPla 消費一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
        
    public function regGoodsBillingAPI()
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

            $consume_date = ($SPIRAL->getParam('consumeDate') == "")? 'now' : $SPIRAL->getParam('consumeDate') ;
            $getBilling = $SPIRAL->getParam('billing');
            $divisionId = $SPIRAL->getParam('divisionId');
            $billingData = $this->requestUrldecode($getBilling);
            
            $in_hospital_item = InHospitalItemView::where('hospitalId', $user_info->getHospitalId());
            foreach($billingData as $key => $record)
            {
                $in_hospital_item->orWhere('inHospitalItemId',$record['recordId']);
            }
            $in_hospital_item = $in_hospital_item->get();
            
            $card_update = [];
            foreach ($billingData as $record)
            {
                foreach($in_hospital_item->data->all() as $in_hp_item)
                {
                    $lot_flag = 0;
                    if($record['recordId'] == $in_hp_item->inHospitalItemId)
                    {
                        $lot_flag = (int)$in_hp_item->lotManagement;
                        break;
                    }
                }
                if($lot_flag && ($record['lotNumber'] == '' || $record['lotDate'] == '' ))
                {
                    throw new Exception('invalid lot',100);
                }
                
                if (($record['lotNumber'] != '' && $record['lotDate'] == '' ) || ($record['lotNumber'] == '' && $record['lotDate'] != ''))
                {
                    throw new Exception('invalid lotNumber input',101);
                }
                if (($record['lotNumber'] != '') && ($record['lotDate'] != '')) 
                {
                    if ((!ctype_alnum($record['lotNumber'])) || (strlen($record['lotNumber']) > 20))
                    {
                        throw new Exception('invalid lotNumber format',102);
                    }
                }
                if($record['card']){
                    $card_update[] = [
                        'cardId' => $record['card'],
                        'payoutId' => '',
                        ];
                }
            }
            

            $useUnitPrice = '';
            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $useUnitPrice = $hospital_data->billingUnitPrice;
            $billing_id = $this->makeId('02');

            $insert_data = [];
            $history_data = [];
            $in_hospital_item_ids = [];
            $inventory_adjustment_trdata = [];
            $billingItemData = [];
            $total_amount = 0;

            foreach ($billingData as $key => $data) {
                if ((int)$data['countNum']  > 0) {
                    $unitPrice = $useUnitPrice
                        ? (str_replace(',', '', $data['unitPrice']))
                        : (str_replace(',', '', $data['kakaku']) / $data['irisu']);
                    $insert_data[] = [
                        'registrationTime' => $consume_date,
                        'inHospitalItemId' => $data['recordId'],
                        'billingNumber' => $billing_id,
                        'price' => str_replace(',', '', $data['kakaku']),
                        'billingQuantity' => (int)$data['countNum'],
                        'billingAmount' => (float)$unitPrice * (int)$data['countNum'],
                        'hospitalId' => $user_info->getHospitalId(),
                        'divisionId' => $divisionId,
                        'quantity' => $data['irisu'],
                        'quantityUnit' => $data['unit'],
                        'itemUnit' => $data['itemUnit'],
                        'lotNumber' => $data['lotNumber'],
                        'lotDate' => $data['lotDate'],
                        'unitPrice' => $unitPrice,
                        'lotManagement' => (int)$data['lotFlagBool']
                    ];
                    $total_amount = $total_amount + ((float)$unitPrice * (int)$data['countNum']);

                }
                if (array_search($data['recordId'], $in_hospital_item_ids) === false) {
                    $in_hospital_item_ids[] = $data['recordId'];
                }
            }
            
            foreach($insert_data as $record)
    		{
    		    if($record['lotNumber'] && $record['lotDate'])
    		    {
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record['divisionId'],
                        'inHospitalItemId' => $record['inHospitalItemId'],
                        'count' => -$record['billingQuantity'],
                        'pattern' => 1,
                        'hospitalId' => $user_info->getHospitalId(),
        		        'lotUniqueKey' => $user_info->getHospitalId().$record['divisionId'].$record['inHospitalItemId'].$record['lotNumber'].$record['lotDate'],
        		        'stockQuantity' => -$record['billingQuantity'],
                        'lotNumber' =>  $record['lotNumber'],
                        'lotDate' =>    $record['lotDate'],
        		    ];
    		    }
    		    else
    		    {
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record['divisionId'],
                        'pattern' => 1,
                        'inHospitalItemId' => $record['inHospitalItemId'],
                        'count' => -$record['billingQuantity'],
                        'hospitalId' => $user_info->getHospitalId(),
        		    ];   
    		    }
    		}

            $history_data[] = [
                'registrationTime' => $consume_date,
                'billingNumber' => $billing_id,
                'hospitalId' => $user_info->getHospitalId(),
                'divisionId' => $divisionId,
                'itemsNumber' => count($in_hospital_item_ids),//院内商品マスタID数
                'totalAmount' => $total_amount
            ];
/*
            Stock::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$divisionId);
            foreach($billingData as $id => $record)
            {
                Stock::orWhere('inHospitalItemId',$id);
            }
            $stockData = Stock::get();

            foreach ($billingItemData as $item) {
                $checkFlg = false;
                $inHpitemId = $item['recordId'];
                foreach ($stockData->data->all() as $stock) {
                    if ($inHpitemId == $stock->inHospitalItemId) {
                        $num = (int)$stock->stockQuantity - (int)$billingItemData[$inHpitemId]['countNum'];
                        if ($num > 0) { $num = 0; }
                        $billingItemData[$inHpitemId]['countNum'] = (int)$billingItemData[$inHpitemId]['countNum'] + $num;
                        $checkFlg = true;
                    }
                }
                if ($checkFlg == false) { $billingItemData[$inHpitemId]['countNum'] = 0; }
            }

            foreach ($billingItemData as $inHPid => $val)
            {
                $inventory_adjustment_trdata[] = [
                    'registrationTime' => 'now',
                    'divisionId' => $divisionId,
                    'inHospitalItemId' => $inHPid,
                    'count' => -(int)$val['countNum'],
                    'hospitalId' => $user_info->getHospitalId(),
                    'orderWithinCount' => 0
                ];
            }
*/
            BillingHistory::insert($history_data);
            Billing::insert($insert_data);
            if(count($card_update) != 0){
                Card::bulkUpdate('cardId',$card_update);
            }
            $result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);

            $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['consumeRegistApi']);
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
$ConsumeController = new ConsumeController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'regGoodsBillingAPI')
    {
        echo $ConsumeController->regGoodsBillingAPI()->render();
    }
    else if($action === 'consumeList')
    {
        echo $ConsumeController->consumeList()->render();
    }
    else if($action === 'consumeListForDivision')
    {
        echo $ConsumeController->consumeListForDivision()->render();
    }
    else 
    {
        echo $ConsumeController->index()->render();
    }
}
