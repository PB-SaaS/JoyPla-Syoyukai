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
use App\Model\Payout;
use App\Model\PayoutView;
use App\Model\PayoutHistory;
use App\Model\InventoryAdjustmentTransaction;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class PayoutSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
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
            
            $card_data = PayoutHistory::where('hospitalId',$user_info->getHospitalId())->find($record_id)->get();
            $card_data = $card_data->data->get(0);
            
            $payout_data = PayoutView::where('hospitalId',$user_info->getHospitalId())->where('payoutHistoryId',$card_data->payoutHistoryId)->get();
            $payout_data = $payout_data->data->all();
            
        	$link = '%url/rel:mpgt:Payout%&Action=payoutList';
            if($user_info->isUser()){
                if (preg_match("/&Action=payoutListForDivision/", $_SERVER['HTTP_REFERER'])) {
            	    $link = $_SERVER['HTTP_REFERER'];
                }
            }
    
            $api_url = "%url/card:page_263580%";
    
            $content = $this->view('NewJoyPla/view/PayoutSlip', [
                'api_url' => $api_url,
                'userInfo' => $user_info,
                'payoutData' => $payout_data,
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
                'title'     => 'JoyPla 払出伝票',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function payoutSlipDeleteApi()
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
            
            $record_id = (int)$SPIRAL->getCardId();
            
            $card_data = PayoutHistory::where('hospitalId',$user_info->getHospitalId())->find($record_id)->get();
            $card_data = $card_data->data->get(0);
            
            if($card_data->payoutHistoryId == '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $payout_data = Payout::where('hospitalId',$user_info->getHospitalId())->where('payoutHistoryId',$card_data->payoutHistoryId)->get();
            $payout_data = $payout_data->data->all();
            $inventory_adjustment_trdata = [];
            foreach($payout_data as $record)
    		{
    		    if($record->lotNumber && $record->lotDate)
    		    {
                    $lot_date = \App\Lib\changeDateFormat('Y年m月d日',$record->lotDate,'Y-m-d');
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record->targetDivisionId,
                        'inHospitalItemId' => $record->inHospitalItemId,
                        'count' => -$record->payoutQuantity,
                        'pattern' => 5,
                        'hospitalId' => $user_info->getHospitalId(),
        		        'lotUniqueKey' => $user_info->getHospitalId().$record->targetDivisionId.$record->inHospitalItemId.$record->lotNumber.$lot_date,
        		        'stockQuantity' => -$record->payoutQuantity,
                        'lotNumber' =>  $record->lotNumber,
                        'lotDate' =>    $lot_date,
        		    ];
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record->sourceDivisionId,
                        'inHospitalItemId' => $record->inHospitalItemId,
                        'count' => $record->payoutQuantity,
                        'pattern' => 4,
                        'hospitalId' => $user_info->getHospitalId(),
        		        'lotUniqueKey' => $user_info->getHospitalId().$record->sourceDivisionId.$record->inHospitalItemId.$record->lotNumber.$lot_date,
        		        'stockQuantity' => $record->payoutQuantity,
                        'lotNumber' =>  $record->lotNumber,
                        'lotDate' =>    $lot_date,
        		    ];
    		    }
    		    else
    		    {
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record->targetDivisionId,
                        'inHospitalItemId' => $record->inHospitalItemId,
                        'pattern' => 5,
                        'count' => -$record->payoutQuantity,
                        'hospitalId' => $user_info->getHospitalId(),
        		    ];   
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record->sourceDivisionId,
                        'inHospitalItemId' => $record->inHospitalItemId,
                        'pattern' => 4,
                        'count' => $record->payoutQuantity,
                        'hospitalId' => $user_info->getHospitalId(),
        		    ];   
    		    }
    		}
    		
    		if(count($inventory_adjustment_trdata) !== 0)
    		{
                $result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);
    		}
    		
            $result = PayoutHistory::destroy($card_data->id);
            
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
$PayoutSlipController = new PayoutSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'payoutSlipDeleteApi')
    {
        echo $PayoutSlipController->payoutSlipDeleteApi()->render();
    }
    else 
    {
        echo $PayoutSlipController->index()->render();
    }
}
