<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\HospitalUser;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class DistributorUserSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {
            
            $box = parse_url($_SERVER['HTTP_REFERER']);
            $back_url = $box['path']."?".$box['query'];
            
            $back_key = $SPIRAL->getParam('BACK');
            
            $sidemenu = [
                'n2' => 'uk-active uk-open',
                'n2_2' => 'uk-active',
                ];
            
            if($back_key === "FacilitySlip")
            {
                $sidemenu = [
                    'n1' => 'uk-active uk-open',
                    'n1_1' => 'uk-active',
                    ];
            }
            $hidden = [
            		"SMPFORM" => "%smpform:T_affiliationUp%",
            		"id" => "%val:sys:id%",
            		"authKey" => "%val:usr:authKey%" ,
            		"OUserPermission" => "%val:usr:OUserPermission:id%",
            		"affiliationId" => "%val:usr:affiliationId%",
            		"loginId" => "%val:usr:loginId%",
            		"name" => "%val:usr:name%",
            		"mailAddress" => "%val:usr:mailAddress%",
                ];
                
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => 'ユーザー招待情報変更',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => $hidden
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false)->render();
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu',$sidemenu, false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => $this->sanitize($back_url),
                'back_text' => '卸業者情報詳細',
                'title'     => 'JoyPla ユーザー招待情報変更',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    
    public function delete(): View
    {
        global $SPIRAL;
        try {
            $box = parse_url($_SERVER['HTTP_REFERER']);
            $back_url = $box['path']."?".$box['query'];
            
            $back_key = $SPIRAL->getParam('BACK');
            
            $sidemenu = [
                'n2' => 'uk-active uk-open',
                'n2_2' => 'uk-active',
                ];
            
            if($back_key === "FacilitySlip")
            {
                $sidemenu = [
                    'n1' => 'uk-active uk-open',
                    'n1_1' => 'uk-active',
                    ];
            }
            
            $hidden = [
            		"SMPFORM" => "%smpform:T_affiliationDel%",
            		"id" => "%val:sys:id%",
            		"authKey" => "%val:usr:authKey%" ,
            		"OUserPermission" => "%val:usr:OUserPermission:id%",
            		"affiliationId" => "%val:usr:affiliationId%",
            		"loginId" => "%val:usr:loginId%",
            		"name" => "%val:usr:name%",
            		"mailAddress" => "%val:usr:mailAddress%",
                ];
                
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => 'ユーザー招待情報削除',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => $hidden
                ] , false)->render();
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false)->render();
        } finally {
            
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', $sidemenu , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => $back_url,
                'back_text' => '卸業者情報詳細',
                'title'     => 'JoyPla ユーザー招待情報削除',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
}

/***
 * 実行
 */
$DistributorUserSlipController = new DistributorUserSlipController();
$action = $SPIRAL->getParam('Action');

{
    if($action === 'delete')
    {
        echo $DistributorUserSlipController->delete()->render();
    }
    else
    {
        echo $DistributorUserSlipController->index()->render();
    }
}
