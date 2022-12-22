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

class ItemRequestHistoryListController extends Controller
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
                    'table' => '%sf:usr:search68:table%',
                    'title' => '請求実績 - 部署選択',
                    'param' => 'ItemRequestHistoryListForDivision',
                    ] , false);
            }
            else
            {
                $content = $this->view('NewJoyPla/view/template/List', [
                        'title' => '請求実績',
                        'print' => false,
                        'export' => false,
                        'submenulink' => "%url/rel:mpg:top%&path=trackrecord",
                        'submenu' => '実績メニュー',
                        'table' => '%sf:usr:search67:mstfilter%',
                        'csrf_token' => Csrf::generate(16),
                        'script' => ''
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
                'title' => 'JoyPla 請求実績',
                'script' => '',
                'content' => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function ItemRequestHistoryListForDivision(): View
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
            
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '請求実績',
                    'print' => false,
                    'export' => false,
                    'submenulink' => "%url/rel:mpg:top%&path=trackrecord",
                    'submenu' => '実績メニュー',
                    'table' => '%sf:usr:search67:mstfilter%',
                    'csrf_token' => Csrf::generate(16),
                    'script' => ''
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
                'title' => 'JoyPla 請求実績',
                'script' => '',
                'content' => $content->render(),
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
$ItemRequestHistoryListController = new ItemRequestHistoryListController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'ItemRequestHistoryListForDivision')
    {
        echo $ItemRequestHistoryListController->ItemRequestHistoryListForDivision()->render();
    }
    else
    {
        echo $ItemRequestHistoryListController->index()->render();
    }
}
