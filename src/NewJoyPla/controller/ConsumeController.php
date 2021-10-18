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
use App\Model\Stock;
use App\Model\Billing;
use App\Model\BillingHistory;
use App\Model\InventoryAdjustmentTransaction;
use App\Model\Distributor;
use App\Model\Order;
use App\Model\OrderHistory;

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
        $param = array();
        try {

            $user_info = new UserInfo($SPIRAL);
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if ($user_info->isHospitalUser() && $user_info->getUserPermission() == '1')
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
    
            $content = $this->view('NewJoyPla/view/GoodsBillingContentEntry', [
                'api_url' => $api_url,
                'userInfo' => $user_info,
                'divisionData'=> $divisionData,
                'useUnitPrice'=> $useUnitPrice,
                'csrf_token' => Csrf::generate(16)
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                'csrf_token' => Csrf::generate(16)
                ] , false);
        } finally {
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

            $getBilling = $SPIRAL->getParam('billing');
            $divisionId = $SPIRAL->getParam('divisionId');
            $billingData = $this->requestUrldecode($getBilling);

            foreach ($billingData as $rows)
            {
                foreach ($rows as $record)
                {
                    if ((int)$record['lotFlag'])
                    {
                        if (($record['lotNumber'] == '') || ($record['lotDate'] == ''))
                        {
                            throw new Exception('invalid lotNumber',100);
                        }
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
                }
            }

            $useUnitPrice = '';
            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $useUnitPrice = $hospital_data->billingUnitPrice;
            $billing_id = $this->makeId('02');

            $insert_data = [];
            $history_data = [];
            $inventory_adjustment_trdata = [];
            $billingItemData = [];
            $total_amount = 0;

            foreach ($billingData as $inHPitem) {
                foreach ($inHPitem as $key => $data) {
                    if ((int)$data['countNum']  > 0) {
                        $unitPrice = $useUnitPrice
                            ? (str_replace(',', '', $data['unitPrice']))
                            : (str_replace(',', '', $data['kakaku']) / $data['irisu']);
                        $insert_data[] = [
                            'registrationTime' => 'now',
                            'updateTime' => '',
                            'inHospitalItemId' => $data['recordId'],
                            'billingNumber' => $billing_id,
                            'price' => str_replace(',', '', $data['kakaku']),
                            'billingQuantity' => (int)$data['countNum'],
                            'billingAmount' => (int)$unitPrice * (int)$data['countNum'],
                            'hospitalId' => $user_info->getHospitalId(),
                            'divisionId' => $divisionId,
                            'quantity' => $data['irisu'],
                            'quantityUnit' => $data['unit'],
                            'itemUnit' => $data['itemUnit'],
                            'lotNumber' => $data['lotNumber'],
                            'lotDate' => $data['lotDate'],
                            'unitPrice' => $unitPrice,
                            'lotManagement' => (int)$data['lotFlag']
                        ];
                        $total_amount = $total_amount + ((int)$unitPrice * (int)$data['countNum']);

                        if ($data['lotNumber'] && $data['lotDate']) {
                            $inventory_adjustment_trdata[] = [
                                'registrationTime' => 'now',
                                'divisionId' => $divisionId,
                                'inHospitalItemId' => $data['recordId'],
                                'count' => 0,
                                'hospitalId' => $user_info->getHospitalId(),
                                'lotUniqueKey' => $user_info->getHospitalId().$divisionId.$data['recordId'].$key,
                                'lotNumber' => $data['lotNumber'],
                                'lotDate' => $data['lotDate'],
                                'stockQuantity' => -(int)$data['countNum']
                            ];
                        }
                    }
                    if (array_key_exists($data['recordId'], $billingItemData)) {
                        $billingItemData[$data['recordId']]['countNum'] += (int)$data['countNum'];
                    } else {
                        $billingItemData[$data['recordId']] = [];
                        $billingItemData[$data['recordId']] = ['countNum' => (int)$data['countNum']];
                    }
                }
            }

            $history_data[] = [
                'registrationTime' => 'now',
                'updateTime' => '',
                'billingNumber' => $billing_id,
                'hospitalId' => $user_info->getHospitalId(),
                'divisionId' => $divisionId,
                'itemsNumber' => count($insert_data),
                'totalAmount' => $total_amount
            ];

            Stock::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$divisionId);
            foreach($billingData as $id => $record)
            {
                Stock::orWhere('inHospitalItemId',$id);
            }
            $stockData = Stock::get();

            foreach ($billingItemData as $inHpitemId => $item) {
                $checkFlg = false;
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

            $result = BillingHistory::insert($history_data);
            $result = Billing::insert($insert_data);
            $result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);

            $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['payoutRegistApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
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

            $getOrdered = $SPIRAL->getParam('ordered');
            $ordered = $this->requestUrldecode($getOrdered);
            $divisionId = $SPIRAL->getParam('divisionId');
            $ordered_id = $this->makeId('03');

            $distributorDB = Distributor::where('hospitalId',$user_info->getHospitalId())->get();
            $distributorDB = $distributorDB->data->all();

            foreach ($distributorDB as $distributor)
            {
                $arrayEachDistributor = [];
                $distributorId = $distributor->distributorId;

                foreach ($ordered as $key => $order)
                {
                    if ($order['distributorId'] == $distributorId) { $arrayEachDistributor[$key] = $order; }
                }

                $insert_data = [];
                $history_data = [];
                $total_amount = 0;

                foreach ($arrayEachDistributor as $inHPItemid => $data)
                {
                    $sign = '';
                    if ((int)$data['countNum'] < 0) { $sign = '-'; }
                    if (floor(abs((int)$data['countNum']) / (int)$data['irisu']) != 0)
                    {
                        $insert_data[] = [
                            'registrationTime' => 'now',
                            'updateTime' => '',
                            'receivingTime' => '',
                            'dueDate' => '',
                            'orderCNumber' => '',
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
                            'distributorId' => $distributorId
                        ];
                        $total_amount = $total_amount + ($sign.str_replace(',', '', $data['kakaku']) * floor(abs((int)$data['countNum']) / (int)$data['irisu']));
                    }
                }

                if (count($insert_data) == 0) {
                   continue;
                }

                $history_data[] = [
                    'registrationTime' => 'now',
                    'orderTime' => '',
                    'receivingTime' => '',
                    'orderNumber' => $ordered_id,
                    'hospitalId' => $user_info->getHospitalId(),
                    'divisionId' => $divisionId,
                    'itemsNumber' => count($insert_data),
                    'totalAmount' => $total_amount,
                    'orderStatus' => '1',
                    'hachuRarrival' => '未入庫',
                    'distributorId' => $distributor->distributorId,
                    'ordererUserName' => $user_info->getName()
                ];
          
                $result = OrderHistory::insert($history_data);
                $result = Order::insert($insert_data);
                $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['insert']);
                $content = $content->toJson();
            }

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
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
$ConsumeController = new ConsumeController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'regGoodsBillingAPI')
    {
        echo $ConsumeController->regGoodsBillingAPI()->render();
    }
    else if($action === 'regUnorderedAPI')
    {
        echo $ConsumeController->regUnorderedAPI()->render();
    }
    else 
    {
        echo $ConsumeController->index()->render();
    }
}
