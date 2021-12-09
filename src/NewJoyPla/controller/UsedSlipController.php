<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\UsedSlipHistoy;
use App\Model\Borrowing;
use App\Model\InHospitalItem;
use App\Model\Division;
use App\Model\BillingHistory;
use App\Model\Billing;
use App\Model\OrderHistory;
use App\Model\Order;
use App\Model\ReceivingHistory;
use App\Model\Receiving;
use App\Model\Hospital;
use App\Model\HospitalUser;
use App\Model\Distributor;
use App\Model\DistributorUser;
use App\Model\AssociationTR;
use App\Model\InHospitalItemView;
use App\Model\DistributorAffiliationView;

use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class UsedSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;

        $title = '';

        try {
            
            $user_info = new UserInfo($SPIRAL);

            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            $cardId = $SPIRAL->getCardId();
            if($cardId == null)
            {   
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $used_slip_history = UsedSlipHistoy::where('id',$cardId)->get();
            $used_slip_history = $used_slip_history->data->all()[0];

            $borrowing = Borrowing::where('usedSlipId',$used_slip_history->usedSlipId)->get();
            $borrowing = $borrowing->data->all();

            $instance = InHospitalItem::getInstance();
            foreach($borrowing as $data)
            {
                $instance::orWhere('inHospitalItemId',$data->inHospitalItemId);
            }

            $in_Hospital_item = $instance::get();

            foreach($borrowing as $borrowing_key => $data)
            {
                foreach($in_Hospital_item->data->all() as $item)
                {
                    if($item->inHospitalItemId === $data->inHospitalItemId)
                    {
                        $borrowing[$borrowing_key]->makerName = $item->makerName;
                        $borrowing[$borrowing_key]->itemName = $item->itemName;
                        $borrowing[$borrowing_key]->itemCode = $item->itemCode;
                        $borrowing[$borrowing_key]->itemStandard = $item->itemStandard;
                        $borrowing[$borrowing_key]->itemJANCode = $item->itemJANCode;
                        break;
                    }
                }
            }

            $api_url = "%url/card:page_176719%";
            $base = "%url/rel:mpgt:Borrowing%";
            
            if($user_info->isDistributorUser())
            {
                $base = "%url/rel:mpgt:BorrowingForD%";
            }
    

            $association = [];
            if($used_slip_history->usedSlipStatus == '2')
            {
                $link = $base."&Action=approvedUsedSlip";
            
                if($user_info->isUser())
                {
                    if (preg_match("/Action=approvedUsedSlipDivision/", $_SERVER['HTTP_REFERER'])) 
                    {
                	    $link = $_SERVER['HTTP_REFERER'];
                    }
                }
                $link_name = "貸出伝票一覧";
                $title = 'JoyPla 貸出伝票一覧';

                if( $user_info->isHospitalUser())
                {
                    $association = AssociationTR::where('usedSlipId',$used_slip_history->usedSlipId)->get();
                    $association = $association->data->all();
                }
            }
            else 
            {
                $link = $base."&Action=unapprovedUsedSlip";
                if($user_info->isUser())
                {
                    if (preg_match("/Action=unapprovedUsedSlipDivision/", $_SERVER['HTTP_REFERER'])) 
                    {
                	    $link = $_SERVER['HTTP_REFERER'];
                    }
                }
                $link_name = "使用申請一覧";
                $title = 'JoyPla 使用申請一覧';
            }

            $content = $this->view('NewJoyPla/view/UsedSlip', [
                'user_info' => $user_info,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'used_slip_history' => $used_slip_history,
                'borrowing' => $borrowing,
                'link' => $link ,
                'link_name' => $link_name,
                'association' => $association,
                'current_name' => '貸出伝票',
            ] , false);

        } catch ( Exception $ex ) {
            $title = 'JoyPla エラー';
            $header = $this->view('NewJoyPla/src/Header', [], false);
            
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false);

        } finally {
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
    
    
    public function usedSlipApprovalApi()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            
            $record_id = (int)$SPIRAL->getCardId();

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(203)->getMessage(),FactoryApiErrorCode::factory(203)->getCode());
            }

            $used_slip_history = UsedSlipHistoy::find($record_id)->where('hospitalId',$user_info->getHospitalId())->get();

            if($used_slip_history->count == 0 )
            {
                throw new Exception(FactoryApiErrorCode::factory(203)->getMessage(),FactoryApiErrorCode::factory(203)->getCode());
            }

            $used_slip_history = $used_slip_history->data->get(0);

            if($used_slip_history->usedSlipStatus != 1 )
            {
                throw new Exception(FactoryApiErrorCode::factory(900)->getMessage(),FactoryApiErrorCode::factory(900)->getCode());
            }

            $borrowing = Borrowing::where('usedSlipId' , $used_slip_history->usedSlipId)->get();
            $borrowing = $borrowing->data->all();

            $all_create_data = $this->usedReportApi($borrowing);

            $used_slip_create_data = ['ids' => []];
            foreach($borrowing as $item)
            {
                if(! isset($used_slip_create_data['ids'][$item->divisionId . $item->distributorId . $item->usedDate]) || 
                ! $used_slip_create_data['ids'][$item->divisionId . $item->distributorId . $item->usedDate])
                {
                    $used_slip_create_data['ids'][$item->divisionId . $item->distributorId . $item->usedDate] = [ 
                        'usedSlipId' => $item->usedSlipId,
                    ];
                }
            }
            
            $result = $this->association($used_slip_create_data['ids'],$all_create_data['ids']);
            
            
            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
            
            /** メールを作成 */
            foreach($used_slip_create_data['ids'] as $history)
            {  
                $mail_body = $this->view('NewJoyPla/view/Mail/UsingRegistration', [
                    'name' => '%val:usr:name%',
                    'hospital_name' => $hospital->hospitalName,
                    'hospital_user_name' => $user_info->getName(),
                    'used_date' => $used_slip_history->usedTime,
                    'used_slip_number' => $used_slip_history->usedSlipId,
                    'used_item_num' => $used_slip_history->itemsNumber,
                    'total_price' => "￥".number_format((float)$used_slip_history->totalAmount,2),
                    'login_url' => OROSHI_LOGIN_URL,
                ] , false)->render();
                
                $select_name = $this->makeId($used_slip_history->distributorId);
                
                $test = DistributorAffiliationView::selectName($select_name)->rule(
                    ['name'=>'distributorId','label'=>'name_'.$used_slip_history->distributorId,'value1'=>$used_slip_history->distributorId,'condition'=>'matches']
                    )
                    ->rule(
                        ['name'=>'invitingAgree','label'=>'invitingAgree','value1'=>'t','condition'=>'is_boolean']
                    )->filterCreate();

                $test = DistributorAffiliationView::selectRule($select_name)
                    ->body($mail_body)
                    ->subject("[JoyPla] 貸出品の使用登録がありました")
                    ->from(FROM_ADDRESS,FROM_NAME)
                    ->send();
            }

            $result = UsedSlipHistoy::find($record_id)->where('hospitalId',$user_info->getHospitalId())->update([
                'usedSlipStatus' => 2
                ]);

            $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();
         
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    private function usedReportApi(array $borrowing_data)
    {
        global $SPIRAL;
        $all_create_data = [ 'ids' => [] , 'borrowingData' => []];

        $user_info = new UserInfo($SPIRAL);
        
        $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
        $hospital = $hospital->data->all();
        if($this->in_hospital_items == null)
        {
            $in_hospital_items_instance = InHospitalItem::getInstance();
            foreach($borrowing_data as $data)
            {
                $in_hospital_items_instance = $in_hospital_items_instance::orWhere('inHospitalItemId',$data->inHospitalItemId);
            }
            $this->in_hospital_items = $in_hospital_items_instance::get();
        }
        $used_report = [];
        foreach($borrowing_data as $borrowing_key => $data)
        {
            $used_report[$borrowing_key] = new StdClass;
            foreach($this->in_hospital_items->data as $key => $val)
            {
                if($val->inHospitalItemId == $data->inHospitalItemId)
                {
                    $used_report[$borrowing_key]->inHospitalItemId = $val->inHospitalItemId;
                    $used_report[$borrowing_key]->hospitalId = $val->hospitalId;
                    $used_report[$borrowing_key]->distributorId = $val->distributorId;
                    $used_report[$borrowing_key]->catalogNo = $val->catalogNo;
                    $used_report[$borrowing_key]->serialNo = $val->serialNo;
                    $used_report[$borrowing_key]->quantity = $val->quantity;
                    $used_report[$borrowing_key]->quantityUnit = $val->quantityUnit;
                    $used_report[$borrowing_key]->itemUnit = $val->itemUnit;
                    $used_report[$borrowing_key]->notUsedFlag = $val->notUsedFlag;
                    $used_report[$borrowing_key]->itemId = $val->itemId;
                    $used_report[$borrowing_key]->itemName = $val->itemName;
                    $used_report[$borrowing_key]->itemCode = $val->itemCode;
                    $used_report[$borrowing_key]->itemStandard = $val->itemStandard;
                    $used_report[$borrowing_key]->itemJANCode = $val->itemJANCode;
                    $used_report[$borrowing_key]->notice = $val->notice;
                    $used_report[$borrowing_key]->makerName = $val->makerName;
                    $used_report[$borrowing_key]->price = $val->price;
                    $used_report[$borrowing_key]->unitPrice = $val->unitPrice;

                    $used_report[$borrowing_key]->countNum = $data->borrowingNum;
                    $used_report[$borrowing_key]->lotNumber = $data->lotNumber;
                    $used_report[$borrowing_key]->lotDate = $data->lotDate;
                    $used_report[$borrowing_key]->divisionId = $data->divisionId;
                    $used_report[$borrowing_key]->usedDate = $data->usedDate;
                }
            }
        }
        $all_create_data['borrowingData'] = $used_report;

        $history_ids = [];
        foreach($used_report as $item)
        {
            if(! isset($history_ids[$item->divisionId . $item->distributorId . $item->usedDate]) || ! $history_ids[$item->divisionId . $item->distributorId . $item->usedDate])
            {
                $order_id = $this->makeId('03');
                $receiving_id = $this->makeId('04');
                $billing_id = $this->makeId('02');
                $history_ids[$item->divisionId . $item->distributorId . $item->usedDate] = [ 
                    'orderHistoryId' => $order_id,
                    'receivingHistoryId' => $receiving_id,
                    'billingHistoryId' => $billing_id,
                    'divisionId' => $item->divisionId,
                    'distributorId' => $item->distributorId,
                    'usedDate' => $item->usedDate,
                ];
            }
        }
        $all_create_data['ids'] = $history_ids;
        /****** 発注登録 ******/

        $order_insert_data = [];
        foreach($used_report as $item)
        {
            if(!$order_insert_data[$item->inHospitalItemId])
            {
                $order_insert_data[$item->inHospitalItemId] = [
                    'registrationTime' => $item->usedDate,
                    'orderTime' => $item->usedDate,
                    'receivingTime' => $item->usedDate,
                    'orderCNumber' => $this->makeId('BO'),
                    'hospitalId' => $item->hospitalId,
                    'inHospitalItemId' => $item->inHospitalItemId,
                    'orderNumber' => $history_ids[$item->divisionId . $item->distributorId . $item->usedDate]['orderHistoryId'],
                    'price' => $item->price,
                    'orderQuantity' => 0,
                    'orderPrice' => 0,
                    'receivingFlag' => 1,
                    'receivingNum' => 0,
                    'quantity' => $item->quantity,
                    'quantityUnit' => $item->quantityUnit,
                    'itemUnit' => $item->itemUnit,
                    'divisionId' => $item->divisionId,
                    'distributorId' => $item->distributorId,
                ];
            }
            $order_insert_data[$item->inHospitalItemId]['orderQuantity'] = $order_insert_data[$item->inHospitalItemId]['orderQuantity'] + (float)$item->countNum;
            $order_insert_data[$item->inHospitalItemId]['orderPrice'] = $order_insert_data[$item->inHospitalItemId]['orderPrice'] + ( (float)$item->price * (float)$item->countNum );
            $order_insert_data[$item->inHospitalItemId]['receivingNum'] = $order_insert_data[$item->inHospitalItemId]['receivingNum'] + (float)$item->countNum;
        }

        $order_history_insert_data = [];
        foreach($history_ids as $divisionId_distributorId_usedDate => $history_data)
        {
            $order_price = [];
            $in_hospital_item_ids = [];
            foreach($order_insert_data as $insert_record)
            {
                if($insert_record['orderNumber'] === $history_data['orderHistoryId'])
                {
                    $order_price[] = (float)$insert_record['orderPrice'];
                    if(! in_array($insert_record['inHospitalItemId'], $in_hospital_item_ids))
                    {
                        $in_hospital_item_ids[] = $insert_record['inHospitalItemId'];
                    }
                }
            }
            $order_history_insert_data[] = [
                'registrationTime' => $history_data['usedDate'],
                'orderTime' => $history_data['usedDate'],
                'receivingTime' => $history_data['usedDate'],
                'orderNumber' => $history_data['orderHistoryId'],
                'hospitalId' => $user_info->getHospitalId(),
                'divisionId' => $history_data['divisionId'],
                'itemsNumber' => collect($in_hospital_item_ids)->count(),
                'totalAmount' => collect($order_price)->sum(),
                'orderStatus' => 8,
                'distributorId' => $history_data['distributorId'],
                'ordererUserName' => $user_info->getName(),
                'ordercomment' => '貸出品',
            ];
        }
        /****** 発注登録 ******/
        /****** 入荷登録 ******/

        $receiving_insert_data = [];
        foreach($used_report as $item)
        {
            foreach($order_insert_data as $order_data)
            {
                $orderCNumber = '';
                if($item->inHospitalItemId == $order_data['inHospitalItemId'])
                {
                    $orderCNumber = $order_data['orderCNumber'];
                    break; //ループ終了
                }
            }
            $receiving_insert_data[] = [
                'registrationTime' => $item->usedDate,
                'orderCNumber' => $orderCNumber,
                'receivingCount' => (float)$item->countNum,
                'receivingHId' => $history_ids[$item->divisionId . $item->distributorId . $item->usedDate]['receivingHistoryId'],
                'inHospitalItemId' => $item->inHospitalItemId,
                'price' => (float)$item->price,
                'receivingPrice' => (float)$item->price * (float)$item->countNum,
                'hospitalId' => $item->hospitalId,
                'totalReturnCount' => 0,
                'divisionId' => $item->divisionId,
                'distributorId' => $item->distributorId,
                'adjAmount' => 0,
                'priceAfterAdj' => 0,
                'lotNumber' => $item->lotNumber,
                'lotDate' => $item->lotDate,
            ];
        }

        $receiving_history_insert_data = [];
        foreach($history_ids as $divisionId_distributorId_usedDate => $history_data)
        {
            $receiving_price = [];
            $in_hospital_item_ids = [];
            foreach($receiving_insert_data as $insert_record)
            {
                if($insert_record['receivingHId'] === $history_data['receivingHistoryId'])
                {
                    $receiving_price[] = (float)$insert_record['receivingPrice'];
                    if(! in_array($insert_record['inHospitalItemId'], $in_hospital_item_ids))
                    {
                        $in_hospital_item_ids[] = $insert_record['inHospitalItemId'];
                    }
                }
            }
            $receiving_history_insert_data[] = [
                'registrationTime' => $history_data['usedDate'],
                'receivingHId' => $history_data['receivingHistoryId'],
                'distributorId' => $history_data['distributorId'],
                'orderHistoryId' => $history_data['orderHistoryId'],
                'hospitalId' => $user_info->getHospitalId(),
                'itemsNumber' => collect($in_hospital_item_ids)->count(),
                'totalAmount' => collect($receiving_price)->sum(),
                'divisionId' => $history_data['divisionId'],
                'recevingStatus' => 2,
            ];
        }

        /****** 入荷登録 ******/
        /****** 消費登録 ******/
        $billing_insert_data = [];
        foreach($used_report as $item)
        {
            $unitPrice = ($hospital[0]->billingUnitPrice)
                ?(float)$item->unitPrice 
                :((float)$item->price == 0 || (float)$item->quantity == 0)
                    ? 0 
                    : (float)$item->price / (float)$item->quantity ;
            $billing_insert_data[] = [
                'registrationTime' => $item->usedDate,
                'inHospitalItemId' => $item->inHospitalItemId,
                'billingNumber' => $history_ids[$item->divisionId . $item->distributorId . $item->usedDate]['billingHistoryId'],
                'price' => (float)$item->price,
                'billingQuantity' => ((float)$item->quantity * (float)$item->countNum),
                'billingAmount' => $unitPrice * ((float)$item->quantity * (float)$item->countNum),
                'hospitalId' => $item->hospitalId,
                'divisionId' => $item->divisionId,
                'quantity' => $item->quantity,
                'quantityUnit' => $item->quantityUnit,
                'itemUnit' => $item->itemUnit,
                'lotNumber' => $item->lotNumber,
                'lotDate' => $item->lotDate,
                'unitPrice' => $item->unitPrice,
            ];
        }

        $billing_history_insert_data = [];
        foreach($history_ids as $divisionId_distributorId_usedDate => $history_data)
        {
            $total_amount = [];
            $in_hospital_item_ids = [];
            foreach($billing_insert_data as $insert_record)
            {
                if($insert_record['billingNumber'] === $history_data['billingHistoryId'])
                {
                    $total_amount[] = (float)$insert_record['billingAmount'];
                    if(! in_array($insert_record['inHospitalItemId'], $in_hospital_item_ids))
                    {
                        $in_hospital_item_ids[] = $insert_record['inHospitalItemId'];
                    }
                }
            }
            $billing_history_insert_data[] = [
                'registrationTime' => $history_data['usedDate'],
                'billingNumber' => $history_data['billingHistoryId'],
                'hospitalId' => $user_info->getHospitalId(),
                'divisionId' => $history_data['divisionId'],
                'itemsNumber' => collect($in_hospital_item_ids)->count(),
                'totalAmount' => collect($total_amount)->sum(),
                'billingStatus' => 2
            ];
        }

        /****** 貸出更新 ******/
        $result = OrderHistory::insert($order_history_insert_data);
        
        $result = Order::insert($order_insert_data);

        $result = ReceivingHistory::insert($receiving_history_insert_data);
        
        $result = Receiving::insert($receiving_insert_data);

        $result = BillingHistory::insert($billing_history_insert_data);
        
        $result = Billing::insert($billing_insert_data);
        
        return $all_create_data;
    }
    
    private function association(array $used_slip_ids , array $other_ids)
    {
        $insert_data = [];
        foreach($used_slip_ids as $divisionId_distributorId_usedDate => $data)
        {
            $insert_data[] = [
                'usedSlipId' => $used_slip_ids[$divisionId_distributorId_usedDate]['usedSlipId'],
                'orderNumber' => $other_ids[$divisionId_distributorId_usedDate]['orderHistoryId'],
                'receivingHId' => $other_ids[$divisionId_distributorId_usedDate]['receivingHistoryId'],
                'billingNumber' => $other_ids[$divisionId_distributorId_usedDate]['billingHistoryId'],
            ];
        }

        $result = AssociationTR::insert($insert_data);

        return $result;
    }
    
    public function cancelApi()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            
            $record_id = (int)$SPIRAL->getCardId();
            
            $user_info = new UserInfo($SPIRAL);    

            $used_slip_history = UsedSlipHistoy::find($record_id)->where('hospitalId',$user_info->getHospitalId())->get();

            if($used_slip_history->count == 0 )
            {
                throw new Exception(FactoryApiErrorCode::factory(203)->getMessage(),FactoryApiErrorCode::factory(203)->getCode());
            }

            $used_slip_history = $used_slip_history->data->all()[0];

            if($used_slip_history->usedSlipStatus != 1 )
            {
                throw new Exception(FactoryApiErrorCode::factory(900)->getMessage(),FactoryApiErrorCode::factory(900)->getCode());
            }

            $result = Borrowing::where('usedSlipId' , $used_slip_history->usedSlipId)->update(['usedSlipId' => '']);
            
            $result = UsedSlipHistoy::find($record_id)->where('hospitalId',$user_info->getHospitalId())->delete();

            $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();
         
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
}

/***
 * 実行
 */
$UsedSlipController = new UsedSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'usedSlipApprovalApi')
    {
        //承認
        echo $UsedSlipController->usedSlipApprovalApi()->render();
    }
    else if($action === 'cancelApi')
    {
        //キャンセル
        echo $UsedSlipController->cancelApi()->render();
    }
    else
    {
        echo $UsedSlipController->index()->render();
    }
}