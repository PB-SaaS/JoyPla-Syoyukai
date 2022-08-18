<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class ReturnController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:Return%";
            
            if ($user_info->isHospitalUser() && ( $user_info->isAdmin() || $user_info->isApprover() ))
            {
                $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '返品一覧',
                    'submenulink' => "%url/rel:mpg:top%&page=page1",
                    'submenu' => '消費・発注',
                    'table' => '%sf:usr:search82:mstfilter%',
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
            } 
            else 
            {
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'table' => '%sf:usr:search103:table%',
                    'title' => '返品一覧 - 部署選択',
                    'param' => 'returnListForDivision',
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
                'title'     => 'JoyPla 返品一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function returnListForDivision(): View
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
            
            $api_url = "%url/rel:mpgt:Receipt%";
        
            $content = $this->view('NewJoyPla/view/template/List', [
                'title' => '返品一覧',
                'submenulink' => "%url/rel:mpg:top%&page=page1",
                'submenu' => '消費・発注',
                'table' => '%sf:usr:search82:mstfilter%',
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
                'title'     => 'JoyPla 返品一覧',
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
$ReturnController = new ReturnController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'returnListForDivision')
    {
        echo $ReturnController->returnListForDivision()->render();
    } 
    else
    {
        echo $ReturnController->index()->render();
    }
}

