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
use App\Model\PayoutHistory;
use App\Model\Payout;
use App\Model\PayoutView;
use App\Model\Card;
use App\Model\Stock;
use App\Model\Distributor;
use App\Model\InventoryAdjustmentTransaction;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class InventoryController extends Controller
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
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            if (($user_info->isHospitalUser() && $user_info->getUserPermission() == '1')) 
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            } 
            else
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }

            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->all();
            $useUnitPrice = $hospital_data[0]->invUnitPrice;
    
            $api_url = "%url/rel:mpgt:Inventory%";
    
            
            $content = $this->view('NewJoyPla/view/InventoryContentEntry', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'division'=> $division,
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
                'title'     => 'JoyPla 棚卸内容入力',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function inventoryRegistAPI()
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
            
    
            $content = new ApiResponse(['payoutHistoryId'=>$payout_id,'labelCreateFlg' => $label_create_flg] , $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['payoutRegistApi']);
            $content = $content->toJson();
        }finally {
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
}

/***
 * 実行
 */
$InventoryController = new InventoryController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'inventoryRegistAPI')
    {
        echo $InventoryController->inventoryRegistAPI()()->render();
    }
    else 
    {
        echo $InventoryController->index()->render();
    }
}
