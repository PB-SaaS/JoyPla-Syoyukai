<?php
namespace App\Controller;

use Controller;
use Csrf;

use App\Lib\Auth;
use App\Model\Hospital;
use App\Model\HospitalUser;
use App\Model\Division;

use stdClass;
use Exception;

class FacilitySlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $session = $SPIRAL->getSession();
            
            $session->put('FacilitySlip',$_SERVER['REQUEST_URI']);
            
            $switcher = $SPIRAL->getParam('Switcher');
            
            $switch_1 = ($switcher == "")? "uk-active" : "";
            $switch_2 = ($switcher == "Division")? "uk-active" : "";
            $switch_3 = ($switcher == "Users")? "uk-active" : "";
            $switch_4 = ($switcher == "Distributor")? "uk-active" : "";
            
            $base_url = "%url/card:page_178577%";
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/FacilityList/Slip', [
                'switch_1' => $switch_1,
                'switch_2' => $switch_2,
                'switch_3' => $switch_3,
                'switch_4' => $switch_4,
                'base_url' => $base_url
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n1' => 'uk-active uk-open',
                'n1_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => '%url/rel:mpgt:Facility%&table_cache=true',
                'back_text' => '病院情報管理',
                'title'     => 'JoyPla 病院情報詳細',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    
    public function divisionReg()
    {
        global $SPIRAL;
        try {
            $hidden = [
					"SMPFORM" => "%smpform:T_divisionReg%",
					"hospitalId" => "%val:usr:hospitalId%",
					"divisionType" => "2",
                ];
                
            $box = parse_url($_SERVER['HTTP_REFERER']);
            $back_url = $box['path']."?".$box['query'];
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => '部署情報登録',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => $hidden
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n1' => 'uk-active uk-open',
                'n1_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => $this->sanitize($back_url),
                'back_text' => '病院情報詳細',
                'title'     => 'JoyPla 部署情報登録',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }

    public function hospitalUserRegDivisionList()
    {
        global $SPIRAL;
        try {
            
            $base_url = "%url/card:page_178577%";
            
            $box = parse_url($_SERVER['HTTP_REFERER']);
            $back_url = $box['path']."?".$box['query'];
            
            $card_id = $SPIRAL->getCardId();
            $hospital = Hospital::find($card_id)->get();
            $hospital = $hospital->data->get(0);
            
            $user_count = HospitalUser::where('hospitalId',$hospital->hospitalId)->count();
            
            if($hospital->registerableNum <= $user_count)
            {
                throw new Exception("登録可能人数の上限に達しています",0);
            }
            
            $division = Division::where('hospitalId',$hospital->hospitalId)->get();
            
            $hidden = [
                    "division" => json_encode($division->data->all()),
 					"SMPFORM" => "%smpform:T_hpUserReg%",
					"hospitalId" => "%val:usr:hospitalId%",
					"hospitalAuthKey" => "%val:usr:authKey%",
                ];
                
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => '病院ユーザー登録',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => $hidden
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n1' => 'uk-active uk-open',
                'n1_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => $this->sanitize($back_url),
                'back_text' => '病院情報詳細',
                'title'     => 'JoyPla 病院ユーザー登録',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function distributorReg()
    {
        global $SPIRAL;
        try {
            
            $base_url = "%url/card:page_178577%";
            
            $box = parse_url($_SERVER['HTTP_REFERER']);
            $back_url = $box['path']."?".$box['query'];
            
            $hidden = [
 					"SMPFORM" => "%smpform:T_distributorReg%",
					"hospitalId" => "%val:usr:hospitalId%",
                ];
                
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => '卸業者情報登録',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => $hidden
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n1' => 'uk-active uk-open',
                'n1_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => $this->sanitize($back_url),
                'back_text' => '病院情報詳細',
                'title'     => 'JoyPla 卸業者情報登録',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function update()
    {
        global $SPIRAL;
        
        try {
            
            $auth = new Auth();
            $auth->browseAuthority('FacilityUpdate');
                
            $base_url = "%url/card:page_178577%";
            
            $box = parse_url($_SERVER['HTTP_REFERER']);
            $back_url = $box['path']."?".$box['query'];
            
            $hidden = [
 					"SMPFORM" => "%smpform:T_FacilityUpd%",
					"hospitalId" => "%val:usr:hospitalId%",
					"hospitalName" => "%val:usr:hospitalName%",
					"postalCode" => "%val:usr:postalCode%",
					"prefectures" => "%val:usr:prefectures%",
					"address" => "%val:usr:address%",
					"phoneNumber" => "%val:usr:phoneNumber%",
					"faxNumber" => "%val:usr:faxNumber%",
					"name" => "%val:usr:name%",
					"nameKana" => "%val:usr:nameKana%",
					"mailAddress" => "%val:usr:mailAddress%",
					"contactAddress" => "%val:usr:contactAddress%",
					"receivingTarget" => "%val:usr:receivingTarget:id%",
					"authKey" => "%val:usr:authKey%",
					"registerableNum" => "%val:usr:registerableNum%",
					"labelDesign1" => "%val:usr:labelDesign1%",
					"labelDesign2" => "%val:usr:labelDesign2%",
					"labelDesign3" => "%val:usr:labelDesign3%",
					"billingUnitPrice" => "%val:usr:billingUnitPrice%",
					"payoutUnitPrice" => "%val:usr:payoutUnitPrice%",
					"invUnitPrice" => "%val:usr:invUnitPrice%",
                ];
                
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => '病院情報更新',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => $hidden
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n1' => 'uk-active uk-open',
                'n1_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => $this->sanitize($back_url),
                'back_text' => '病院情報詳細',
                'title'     => 'JoyPla 病院情報更新',
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
$FacilitySlipController = new FacilitySlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "update")
    {
        echo $FacilitySlipController->update()->render();
    }
    else if($action === "divisionReg")
    {
        echo $FacilitySlipController->divisionReg()->render();
    }
    else if($action === "distributorReg")
    {
        echo $FacilitySlipController->distributorReg()->render();
    }
    else if($action === "hospitalUserRegDivisionList")
    {
        echo $FacilitySlipController->hospitalUserRegDivisionList()->render();
    }
    else 
    {
        echo $FacilitySlipController->index()->render();
    }
}