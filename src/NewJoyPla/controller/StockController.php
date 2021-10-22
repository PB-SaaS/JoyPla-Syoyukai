<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\Division;
use App\Model\Stock;
use App\Model\InventoryAdjustmentTransaction;
use App\Model\InHospitalItem;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class StockController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if($user_info->isUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
    
            $api_url = "%url/rel:mpgt:Stock%";
    
            $content = $this->view('NewJoyPla/view/StockContent', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'division'=> $division,
                'csrf_token' => Csrf::generate(16)
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 在庫調整',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function stockSearchApi(): View
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $division_id = $SPIRAL->getParam('divisionId');
            $in_hospital_item_id = $SPIRAL->getParam('inHospitalItemId');
            
            if( $user_info->isHospitalUser() )
            {
                if( $user_info->isUser() && $user_info->getDivisionId() != $division_id )
                {
                    throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                }
                $stock = Stock::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$division_id)->where('inHospitalItemId',$in_hospital_item_id)->get();
            } 
            else 
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $content = new ApiResponse($stock->data->get(0) , $stock->count , $stock->code, $stock->message, ['stockSearch']);
            $content = $content->toJson();
            /** TODO
             *  spiralDatabaseのレスポンスをApiResponseに変更 
             **/
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['stockSearch']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    public function stockRegisterApi(): View
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $division_id = $SPIRAL->getParam('divisionId');
            if($division_id == '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $stocks = $SPIRAL->getParam('stocks');
            
            $insert_data = [];
            $stock_instance = Stock::where('hospitalId',$user_info->getHospitalId());
            $items = [];
            foreach($stocks as $stock)
            {
                $insert_data[] = [
                    'divisionId'=>$division_id,
                    'inHospitalItemId'=>$stock['recordId'],
                    'count'=>$stock['stockCountNum'],
                    'hospitalId'=>$user_info->getHospitalId(),
                    'orderWithinCount'=>'',
                    'pattern'=>'10',
                    'lotUniqueKey'=>'',
                    'lotNumber'=>'',
                    'lotDate'=>'',
                    'stockQuantity'=>'',
                    'rackName'=>$stock['rackName'],
                    'constantByDiv'=>$stock['constantByDiv'],
                    'loginId'=>$user_info->getLoginId(),
                    'previousStock' => $stock['stock'],
                ];
                if(array_search($stock['recordId'], $items) === false)
                {
                    $items[] = $stock['recordId'];
                    $stock_instance->orWhere('inHospitalItemId',$stock['recordId']);
                }
            }
            
            $result = InventoryAdjustmentTransaction::insert($insert_data);
            
            $stock_instance = $stock_instance->get();//InventoryAdjustmentTransactionの後にすることが重要
            
            $update_data = [];
            
            foreach($items as $in_hp_id)
            {
                $count = 0;
                foreach($stock_instance->data->all() as $stock)
                {
                    if($stock->inHospitalItemId == $in_hp_id)
                    {
                        $count = $count + (int)$stock->stockQuantity;
                    }
                }
                $update_data[] = ['inHospitalItemId' => $in_hp_id , 'HPstock' => $count ];
            }
            
            InHospitalItem::bulkUpdate('inHospitalItemId',$update_data);
            
            $content = new ApiResponse($result->ids, $result->count , $result->code, $result->message, ['stockRegisterApi']);
            $content = $content->toJson();
            /** TODO
             *  spiralDatabaseのレスポンスをApiResponseに変更 
             **/
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['stockRegisterApi']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    public function stockManagementList() :View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:Stock%";
            if($user_info->isUser())
            {
                $title = "在庫管理表 - 部署選択";
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'title' => '在庫管理表 - 部署選択',
                    'table' => '%sf:usr:search84:table%',
                    'api_url' => $api_url,
                    'user_info' => $user_info,
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
            }
            else
            {
                $title = "在庫管理表";
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
                $content = $this->view('NewJoyPla/view/InventoryControlTable', [
                    'api_url' => $api_url,
                    'user_info' => $user_info,
                    'division'=> $division->data->all(),
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
            }
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => $title,
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function stockManagementListForDivision()
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if(! $user_info->isUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if ( \App\lib\isMypage() )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            $api_url = "%url/rel:mpgt:Stock%";
            $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
               
            $title = "在庫管理表";
            $content = $this->view('NewJoyPla/view/InventoryControlTable', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'division' => $division->data->all(),
                'csrf_token' => Csrf::generate(16)
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => $title,
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function adjustmentHistory()
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if( $user_info->isHospitalUser() && $user_info->isUser() )
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
    
            $api_url = "%url/rel:mpgt:Stock%";
            
    
            $content = $this->view('NewJoyPla/view/StockAdjustmentHistory', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'csrf_token' => Csrf::generate(16)
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 在庫調整ログ',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
}

$StockController = new StockController();


$action = $SPIRAL->getParam('Action');

{
    if($action == "stockSearch")
    {
        echo $StockController->stockSearchApi()->render();
    }
    else if($action == "stockRegister")
    {
        echo $StockController->stockRegisterApi()->render();
    }
    else if($action == "stockManagementList")
    {
        echo $StockController->stockManagementList()->render();
    }
    else if($action == "stockManagementListForDivision")
    {
        echo $StockController->stockManagementListForDivision()->render();
    }
    else if($action == "adjustmentHistory")
    {
        echo $StockController->adjustmentHistory()->render();
    }
    else
    {
        echo $StockController->index()->render();
    }
}