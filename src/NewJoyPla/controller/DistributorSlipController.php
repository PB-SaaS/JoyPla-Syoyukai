<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Tenant;
use App\Model\Hospital;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class DistributorSlipController extends Controller
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
                $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
                $hospital = $hospital->data->get(0);
                $tenant = Tenant::where('tenantId', $hospital->tenantId)->get();
                $tenant = $tenant->data->get(0);
                $link = "%url/rel:mpgt:DistributorInfo%";
            } 
            else
            {
                $tenant = Tenant::where('tenantId', $user_info->getTenantId())->get();
                $tenant = $tenant->data->get(0);
                $link = "%url/rel:mpgt:DistributorList%";
            }
            
            $api_url = "%url/card:page_265119%";
            
            $content = $this->view('NewJoyPla/view/DistributorInformationSlip', [
                        'user_info' => $user_info,
                        'link' => $link,
                        'tenant' => $tenant,
                        'api_url' => $api_url,
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
                'title'     => 'JoyPla 卸業者一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function updateDistributor(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            if(!$user_info->isAdmin())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $link = "%url/rel:mpgt:DistributorList%";
            $api_url = "%url/card:page_265119%";
            
            if($user_info->isDistributorUser())
            {
                $link = "%url/rel:mpgt:DistributorInfo%";
            }
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="{$link}">卸業者一覧</a></li>
            <li><a href="%url/card:page_265119%">卸業者情報詳細</a></li>
            <li><span>卸業者情報変更</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '卸業者情報変更',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
					"SMPFORM" => "%smpform:distributorChang%",
					"authKey" => "%val:usr:authKey%",
					"distributorId" => "%val:usr:distributorId%",
					"distributorName" => "%val:usr:distributorName%",
					"postalCode" => "%val:usr:postalCode%",
					"prefectures" => "%val:usr:prefectures%",
					"address" => "%val:usr:address%",
					"phoneNumber" => "%val:usr:phoneNumber%",
					"faxNumber" => "%val:usr:faxNumber%",
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
                'title'     => 'JoyPla 卸業者情報変更',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function distributorUserReg(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            if(!$user_info->isAdmin())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $link = "%url/rel:mpgt:DistributorList%";
            $api_url = "%url/card:page_265119%";
            
            if($user_info->isDistributorUser())
            {
                $link = "%url/rel:mpgt:DistributorInfo%";
            }
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="{$link}">卸業者一覧</a></li>
            <li><a href="%url/card:page_265119%">卸業者情報詳細</a></li>
            <li><span>卸業者ユーザー登録</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '卸業者ユーザー登録',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
    					"distributorId" => "%val:usr:distributorId%",
    					"SMPFORM" => "%smpform:oroshiUserReg%",
    					"distAuthKey" => "%val:usr:authKey%",
    					"hospitalId" => "%val:usr:hospitalId%",
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
                'title'     => 'JoyPla 卸業者ユーザー登録',
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
$DistributorSlipController = new DistributorSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "updateDistributor")
    {
        echo $DistributorSlipController->updateDistributor()->render();
    }
    else if($action === "distributorUserReg")
    {
        echo $DistributorSlipController->distributorUserReg()->render();
    }
    else 
    {
        echo $DistributorSlipController->index()->render();
    }
}
