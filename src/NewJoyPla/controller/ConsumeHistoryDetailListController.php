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

class ConsumeHistListController extends Controller
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
            
            if($user_info->isUser())
            {
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'table' => '%sf:usr:search30:table%',
                    'title' => '消費履歴詳細一覧 - 部署選択',
                    'param' => 'consumeHistListForDivision',
                    ] , false);
            }
            else
            {
                
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
                
                $division_script = $this->view('NewJoyPla/view/Script/SearchTableDivisionSelect', [
                    'division' => $division->data->all()
                ],false);
                
                $content = $this->view('NewJoyPla/view/template/List', [
                        'title' => '消費履歴詳細一覧',
                        'print' => false,
                        'export' => false,
                        'submenulink' => "%url/rel:mpg:top%&path=trackrecord",
                        'submenu' => '実績メニュー',
                        'table' => '%sf:usr:361_consume:mstfilter%',
                        'csrf_token' => Csrf::generate(16),
                        'script' => $division_script->render(),
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
                'title'     => 'JoyPla 消費履歴詳細一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function consumeHistListForDivision(): View
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
            
            
            $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            
            $division_script = $this->view('NewJoyPla/view/Script/SearchTableDivisionSelect', [
                'division' => $division->data->all()
            ],false);
    
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '消費履歴詳細一覧',
                    'print' => false,
                    'export' => false,
                    'submenulink' => "%url/rel:mpg:top%&path=trackrecord",
                    'submenu' => '実績メニュー',
                    'table' => '%sf:usr:361_consume:mstfilter%',
                    'csrf_token' => Csrf::generate(16),
                    'script' => $division_script->render(),
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
            
            $script = $this->view('NewJoyPla/view/template/parts/TableScript', [], false);

            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 消費履歴詳細一覧',
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
$ConsumeHistListController = new ConsumeHistListController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'consumeHistListForDivision')
    {
        echo $ConsumeHistListController->consumeHistListForDivision()->render();
    }
    else 
    {
        echo $ConsumeHistListController->index()->render();
    }
}
