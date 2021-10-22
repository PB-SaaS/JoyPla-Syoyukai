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
use App\Model\Billing;
use App\Model\BillingView;
use App\Model\BillingHistory;
use App\Model\InventoryAdjustmentTransaction;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class ConsumeSlipController extends Controller
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
            $record_id = (int)$SPIRAL->getCardId();
            
            $card_data = BillingHistory::where('hospitalId',$user_info->getHospitalId())->find($record_id)->get();
            $card_data = $card_data->data->get(0);
            
            $billing_data = BillingView::where('hospitalId',$user_info->getHospitalId())->where('billingNumber',$card_data->billingNumber)->get();
            $billing_data = $billing_data->data->all();
            
        	$link = '%url/rel:mpgt:Consume%&Action=consumeList';
            if($user_info->isUser()){
                if (preg_match("/Action=consumeListForDivision/", $_SERVER['HTTP_REFERER'])) {
            	    $link = $_SERVER['HTTP_REFERER'];
                }
            }
    
            $api_url = "%url/card:page_263400%";
    
            $content = $this->view('NewJoyPla/view/GoodsBillingDetail', [
                'api_url' => $api_url,
                'userInfo' => $user_info,
                'billingData' => $billing_data,
                'link'=> $link,
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
                'title'     => 'JoyPla 消費物品',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function consumeSlipDeleteApi()
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
            if ($user_info->isApprover())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $record_id = (int)$SPIRAL->getCardId();
            
            $card_data = BillingHistory::where('hospitalId',$user_info->getHospitalId())->find($record_id)->get();
            $card_data = $card_data->data->get(0);
            
            if($card_data->billingNumber == '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $billing_data = Billing::where('hospitalId',$user_info->getHospitalId())->where('billingNumber',$card_data->billingNumber)->get();
            $billing_data = $billing_data->data->all();
            
            foreach($billing_data as $record)
    		{
    		    if($record->lotNumber && $record->lotDate)
    		    {
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record->divisionId,
                        'inHospitalItemId' => $record->inHospitalItemId,
                        'count' => $record->billingQuantity,
                        'pattern' => 1,
                        'hospitalId' => $user_info->getHospitalId(),
        		        'lotUniqueKey' => $user_info->getHospitalId().$record->divisionId.$record->inHospitalItemId.$record->lotNumber.$record->lotDate,
        		        'stockQuantity' => $record->billingQuantity,
                        'lotNumber' =>  $record->lotNumber,
                        'lotDate' =>    $record->lotDate,
        		    ];
    		    }
    		    else
    		    {
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record->divisionId,
                        'pattern' => 1,
                        'inHospitalItemId' => $record->inHospitalItemId,
                        'count' => $record->billingQuantity,
                        'hospitalId' => $user_info->getHospitalId(),
        		    ];   
    		    }
    		}
            BillingHistory::destroy($card_data->id);
            $result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);
            
            $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['delete']);
            $content = $content->toJson();

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['delete']);
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
$ConsumeController = new ConsumeSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'consumeSlipDeleteApi')
    {
        echo $ConsumeController->consumeSlipDeleteApi()->render();
    }
    else 
    {
        echo $ConsumeController->index()->render();
    }
}
