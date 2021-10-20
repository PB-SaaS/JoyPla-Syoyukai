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
use App\Model\Receiving;
use App\Model\ReceivingHistory;
use App\Model\Order;
use App\Model\OrderHistory;
use App\Model\InventoryAdjustmentTransaction;
use App\Model\OrderDataView;
use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class ReceiptController extends Controller
{
    private $in_hospital_items = null ;
    public function __construct()
    {
    }
    
    /**
     * 発注書一覧
     */
    public function OrederList(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = array();

        $user_info = new UserInfo($SPIRAL);

        if ($user_info->isDistributorUser())
        {
            throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
        }

        $myPageID = $SPIRAL->getParam('MyPageID');
        if ($user_info->getUserPermission() != '1' && $myPageID != '')
        {
            App\Lib\viewNotPossible();
            exit;
        }

        $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
        $header = $this->view('NewJoyPla/src/HeaderForMypage', [
            'SPIRAL' => $SPIRAL
        ], false);

        $content = $this->view('NewJoyPla/view/PurchaseOrderList', [
            'userInfo' => $user_info,
            'csrf_token' => Csrf::generate(16)
            ] , false);
        
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        return $this->view('NewJoyPla/view/template/Template', [
            'title'     => 'JoyPla 発注書一覧',
            'content'   => $content->render(),
            'head' => $head->render(),
            'header' => $header->render(),
            'baseUrl' => '',
        ],false);
    }

    /**
     * 納品照合
     */
    public function RegReceivingAPI(): View
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);

            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $divisionIdCrypt = $SPIRAL->getParam('divisionIdCrypt');
            if($divisionIdCrypt == null)
            {
                throw new Exception(FactoryApiErrorCode::factory(900)->getMessage(),FactoryApiErrorCode::factory(900)->getCode());
            }

            $getReceiving = $SPIRAL->getParam('receiving');
            $receiving = $this->requestUrldecode($getReceiving);
            $getReceivingItem = $SPIRAL->getParam('receivingItem');
            $receivingItem = $this->requestUrldecode($getReceivingItem);

            foreach ($receiving as $rows)
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

            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $receivingTarget = $hospital_data->receivingTarget;
            $store = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionType','1')->get();
            $store = $store->data->get(0);

            $divisionId = '';
            if ($receivingTarget == '1')
            {
                $divisionId = $store->divisionId;
            }
            if ($receivingTarget == '2')
            {
                $crypt = $SPIRAL->getSpiralCryptOpenSsl();
                $divisionId = $crypt->decrypt($divisionIdCrypt, 'JoyPla');
            }

            $insert_data = [];
            $history_data = [];
            $inventory_adjustment_trdata = [];
            $receivingHist_id = $this->makeId('04');

            foreach ($receiving as $rows)
            {
                foreach ($rows as $key => $data)
                {
                    if ($data['receivingCount'] != 0)
                    {
                        $insert_data[] = [
                            'registrationTime' => 'now',
                            'orderCNumber' => $data['orderCNumber'],
                            'receivingCount' => $data['receivingCount'],
                            'receivingHId' => $receivingHist_id,
                            'inHospitalItemId' => $data['inHPItemid'],
                            'price' => $data['price'],
                            'receivingPrice' => $data['receivingCount'] * $data['price'],
                            'hospitalId' => $user_info->getHospitalId(),
                            'divisionId' => $divisionId,
                            'lotNumber' => $data['lotNumber'],
                            'lotDate' => $data['lotDate']
                        ];

                        if ($data['lotNumber'] && $data['lotDate']) {
                            $inventory_adjustment_trdata[] = [
                                'registrationTime' => 'now',
                                'divisionId' => $divisionId,
                                'inHospitalItemId' => $data['inHPItemid'],
                                'count' => 0,
                                'hospitalId' => $user_info->getHospitalId(),
                                'lotUniqueKey' => $user_info->getHospitalId().$divisionId.$data['inHPItemid'].$key,
                                'lotNumber' => $data['lotNumber'],
                                'lotDate' => $data['lotDate'],
                                'stockQuantity' => (int)$data['quantity'] * (int)$data['receivingCount']
                            ];
                        }
                    }
                }
            }

            $history_data[] = [
                'registrationTime' => 'now',
                'receivingHId' => $receivingHist_id,
                'distributorId' => $SPIRAL->getParam('distributorId'),
                'orderHistoryId' => $SPIRAL->getParam('orderHistoryId'),
                'hospitalId' => $user_info->getHospitalId(),
                'itemsNumber' => count($insert_data)
            ];

            $update_data = [];
            foreach ($receivingItem as $inHPItemid => $item)
            {
                $receivingFlag = ($item['orderQuantity'] - ((int)$item['receivingNowCount'] + (int)$item['receivingCount']) <= 0) ? '1' : '0';
                $update_data[] = [
                    'orderCNumber' => $item['orderCNumber'],
                    'receivingTime' => 'now',
                    'receivingFlag' => $receivingFlag,
                    'receivingNum' => (int)$item['receivingNowCount'] + (int)$item['receivingCount']
                ];

                $count = (int)$item['quantity'] * (int)$item['receivingCount'];
                if ($count != 0)
                {
                    $inventory_adjustment_trdata[] = [
                        'registrationTime' => 'now',
                        'divisionId' => $divisionId,
                        'inHospitalItemId' => $inHPItemid,
                        'count' => $count,
                        'hospitalId' => $user_info->getHospitalId(),
                        'orderWithinCount' => 0
                    ];
                    if ($count <= 0) {
                        continue; //マイナス発注は発注中個数の計算をしない。
                    } else {
                        $inventory_adjustment_trdata[] = [
                            'registrationTime' => 'now',
                            'divisionId' => $divisionId,
                            'inHospitalItemId' => $inHPItemid,
                            'count' => 0,
                            'hospitalId' => $user_info->getHospitalId(),
                            'orderWithinCount' => -$count
                        ];
                    }
                }
            }

            $result = ReceivingHistory::insert($history_data);
            $result = Receiving::insert($insert_data);
            $result = Order::bulkUpdate('orderCNumber', $update_data);
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


    /**
     * 発注取消
     */
    public function OrderedDeleteAPI()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $auth_key = $SPIRAL->getParam('orderAuthKey');
            if($auth_key == null)
            {
                throw new Exception(FactoryApiErrorCode::factory(900)->getMessage(),FactoryApiErrorCode::factory(900)->getCode());
            }
            
            $cardId = $SPIRAL->getParam('OrderDataViewId');
            if($cardId == null)
            {
                throw new Exception('ページが存在しません',404);
            }

            $user_info = new UserInfo($SPIRAL);

            $card = OrderDataView::where('id',$cardId)->get();
            $card = $card->data->all()[0];

            $orderNumber = $card->orderNumber;
            $crypt = $SPIRAL->getSpiralCryptOpenSsl();
            $orderAuthKey = $crypt->decrypt($auth_key, 'JoyPla');
            $divisionId = $card->divisionId;

            $result = OrderHistory::where('orderNumber',$orderNumber)->where('orderAuthKey',$orderAuthKey)->delete();

            $countFlg = $SPIRAL->getParam('countFlg');
            if ($countFlg)
            {
                $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
                $hospital_data = $hospital_data->data->get(0);
                $receivingTarget = $hospital_data->receivingTarget;
                $store = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionType','1')->get();
                $store = $store->data->get(0);

                if ($receivingTarget == '1')
                {
                    $divisionId = $store->divisionId;
                }

                $orderWithinCount = $SPIRAL->getParam('orderData');
                $inventory_adjustment_trdata = [];
                foreach ($orderWithinCount as $inHPItemid => $item)
                {
                    $count = (int)$item['quantity'] * (int)$item['countNum'];
                    if ($count <= 0) { continue; } //マイナス発注は発注中個数の計算をしない。
                    if ($count != 0)
                    {
                        $inventory_adjustment_trdata[] = [
                            'registrationTime' => 'now',
                            'divisionId' => $divisionId,
                            'inHospitalItemId' => $inHPItemid,
                            'count' => 0,
                            'hospitalId' => $user_info->getHospitalId(),
                            'orderWithinCount' => -$count
                        ];
                    }
                }
                $result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);
           }

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
$ReceiptController = new ReceiptController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'OrederList')
    {
        echo $ReceiptController->OrederList()->render();
    } 
    else if($action === 'RegReceivingAPI')
    {
        echo $ReceiptController->RegReceivingAPI()->render();
    }
    else if($action === 'OrderedDeleteAPI')
    {
        echo $ReceiptController->OrderedDeleteAPI()->render();
    }
}
