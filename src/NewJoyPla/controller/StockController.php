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
            
            if( $user_info->isHospitalUser() && $user_info->isAdmin() )
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            } 
            else 
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }
    
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
                'title'     => 'JoyPla カード内容入力',
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

            $user_info = new UserInfo($SPIRAL);
            
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
    else
    {
        echo $StockController->index()->render();
    }
}