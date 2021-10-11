<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\Division;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class PayoutController extends Controller
{
    public function __construct()
    {
    }
    
    /**
     * 貸出品登録
     */
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
            $source_division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            if( ($user_info->isHospitalUser() && $user_info->getUserPermission() == '1')) 
            {
                $target_division = $source_division;
            } 
            else 
            {
                $target_division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }
    
            $api_url = "%url/rel:mpgt:PayoutApi%";
    
            
            $content = $this->view('NewJoyPla/view/PayoutContent', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'source_division'=> $source_division,
                'target_division'=> $target_division,
                'csrf_token' => Csrf::generate(16)
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
                ] , false);
        } finally {
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 払出登録',
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
$PayoutController = new PayoutController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'PayoutController')
    {
        
    }
    else 
    {
        echo $PayoutController->index()->render();
    }
}
