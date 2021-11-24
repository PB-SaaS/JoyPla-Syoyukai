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

class DistributorSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {
            
            $session = $SPIRAL->getSession();
            
            $base_url = "%url/card:page_178749%";
            
            $back_key = $SPIRAL->getParam('BACK');
            $back_url = "%url/rel:mpgt:Distributor%&table_cache=true";
            $back_text = "卸業者管理";
            $sidemenu = [
                'n2' => 'uk-active uk-open',
                'n2_1' => 'uk-active',
            ];
            
            if($back_key == "FacilitySlip" && $session->containsKey($back_key))
            {
                $sidemenu = [
                    'n1' => 'uk-active uk-open',
                    'n1_1' => 'uk-active',
                ];
                $back_text = "病院情報詳細";
                $back_url = $session->get($back_key);
            }
            $base_url = $base_url ."&BACK=".$back_key;
            
            $switcher = $SPIRAL->getParam('Switcher');
            
            $switch_1 = ($switcher == "")? "uk-active" : "";
            $switch_2 = ($switcher == "Users")? "uk-active" : "";
                
            $content = $this->view('NewJoyPlaTenantAdmin/view/DistributorList/Slip', [
                'switch_1' => $switch_1,
                'switch_2' => $switch_2,
                'base_url' => $base_url,
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
                'back_text' => $back_text,
                'title'     => 'JoyPla-Tenant-Master 卸業者情報詳細',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    
    public function divisionUserInv(): View
    {
        global $SPIRAL;
        try {
            $base_url = "%url/card:page_178749%";
            $back_url = $_SERVER['HTTP_REFERER'];
            
            $back_text = "卸業者情報詳細";
            
            $sidemenu = [
                'n2' => 'uk-active uk-open',
                'n2_1' => 'uk-active'
            ];
            if (preg_match("/&Switcher=Distributor/", $back_url)) {
                $back_text = "卸業者情報詳細";
                $sidemenu = [
                    'n2' => 'uk-active uk-open',
                    'n2_1' => 'uk-active']; 
            }
            if (preg_match("/&BACK=FacilitySlip/", $back_url)) {
                $back_text = "病院情報詳細";
                $sidemenu = [
                    'n1' => 'uk-active uk-open',
                    'n1_1' => 'uk-active']; 
            }
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => '卸業者ユーザー招待',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
    					"SMPFORM" => "%smpform:T_distUserInv%",
    					"distAuthKey" => "%val:usr:authKey%",
    					"hospitalId" => "%val:usr:hospitalId%",
    					"distributorId" => "%val:usr:distributorId%",
    					"hospitalName" => "%val:usr:hospitalName%",
    					"distributorName" => "%val:usr:distributorName%",
                    ]
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
                'back_text' => $back_text,
                'title'     => 'JoyPla-Tenant-Master 卸業者情報詳細',
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
$DistributorSlipController = new DistributorSlipController();
$action = $SPIRAL->getParam('Action');

{
    if($action === "divisionUserInv")
    {
        echo $DistributorSlipController->divisionUserInv()->render();
    }
    else
    {
        echo $DistributorSlipController->index()->render();
    }
}
