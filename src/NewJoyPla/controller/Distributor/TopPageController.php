<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\Hospital;
use App\Model\Tenant;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class TopPageController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
            
            $tenant = Tenant::where('tenantId',$hospital->tenantId)->get();
            $tenant = $tenant->data->get(0);
            
            $content = $this->view('NewJoyPla/view/Distributor/Top', [
                'hospital' => $hospital,
                'tenant' => $tenant,
                'user_info' => $user_info,
                'url' => '%url/rel:mpgt:oroshiTopPage%'
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
                'title'     => 'JoyPla TOP',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function userInfoChange(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><span>卸業者ユーザー変更</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '卸業者ユーザー変更',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
            			"userPermission" => "%val:usr:userPermission:id%",
            			"loginId" => "%val:usr:loginId%",
            			"name" => "%val:usr:name%",
            			"nameKana" => "%val:usr:nameKana%",
            			"mailAddress" => "%val:usr:mailAddress%",
            			"remarks" => "%val:usr:remarks%",
            			"SMPFORM" => "%smpform:oroshiUserChange%",
            			"id" => "%val:sys:id%",
            			"authKey" => "%val:usr:authKey%"
                    ]
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
                'title'     => 'JoyPla 卸業者ユーザー変更',
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
$TopPageController = new TopPageController();
$action = $SPIRAL->getParam('Action');

{
    if($action === 'userInfoChange')
    {
        echo $TopPageController->userInfoChange()->render();
    } 
    else 
    {
        echo $TopPageController->index()->render();
    }
}
