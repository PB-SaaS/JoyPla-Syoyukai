<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Distributor;
use App\Model\Division;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class ReceivingHistListController extends Controller
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
                    'table' => '%sf:usr:search2:table%',
                    'title' => '入荷履歴詳細一覧 - 部署選択',
                    'param' => 'ReceivingHistListForDivision',
                    ] , false);
            }
            else
            {
                    
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
                
                $division_script = $this->view('NewJoyPla/view/Script/SearchTableDivisionSelect', [
                    'division' => $division->data->all()
                ],false);
                
                $distributor_data = Distributor::where('hospitalId',$user_info->getHospitalId())->get();
                $distributor_data = $distributor_data->data->all();
                $distributor = [];
                foreach ($distributor_data as $val)
                {
                    $distributor[] = [
                        $val->distributorName,
                        $val->distributorId
                    ];
                }
                $content = $this->view('NewJoyPla/view/template/List', [
                        'title' => '入荷履歴詳細一覧',
                        'print' => true,
                        'export' => true,
                        'table' => '%sf:usr:search41:mstfilter%',
                        'csrf_token' => Csrf::generate(16),
                        'script' => $division_script->render().($this->view('NewJoyPla/view/Script/OrderHistoryDetailList', ['distributor' => $distributor] , false)->render())
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
                'title'     => 'JoyPla 入荷履歴詳細一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function ReceivingHistListForDivision(): View
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
            
            $distributor_data = Distributor::where('hospitalId',$user_info->getHospitalId())->get();
            $distributor_data = $distributor_data->data->all();
            $distributor = [];
            foreach ($distributor_data as $val)
            {
                $distributor[] = [
                    $val->distributorName,
                    $val->distributorId
                ];
            }
            
            
            $script = $this->view('NewJoyPla/view/Script/SearchTableDivisionSelect', ['division' => $division->data->all()],false)->render();
            $script .= $this->view('NewJoyPla/view/Script/OrderHistoryDetailList', ['distributor' => $distributor,] , false)->render();
            
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '入荷履歴詳細一覧',
                    'print' => true,
                    'export' => true,
                    'table' => '%sf:usr:search41:mstfilter%',
                    'csrf_token' => Csrf::generate(16),
                    'script' => $script
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
                'title'     => 'JoyPla 入荷履歴詳細一覧',
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
$ReceivingHistListController = new ReceivingHistListController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'ReceivingHistListForDivision')
    {
        echo $ReceivingHistListController->ReceivingHistListForDivision()->render();
    }
    else 
    {
        echo $ReceivingHistListController->index()->render();
    }
}
