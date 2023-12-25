<?php
namespace App\Controller;

use Controller;
use Csrf;
use App\Lib\Auth;
use App\Model\Division;
use App\Model\Tenant;
use App\Model\Hospital;
use App\Model\HospitalUser;
use stdClass;
use Exception; 

class FacilityController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('FacilityList');
            
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            $select_hospital = [['text'=> '----- 選択してください -----' ,'value'=> '' ]];
            foreach($hospital->data->all() as $h)
            {
                $select_hospital[] = ['text'=> $h->hospitalName ,'value'=> $h->hospitalName ];
            }
            $session = $SPIRAL->getSession(true , 3600);
            $session->remove('back_url');
            $error = $SPIRAL->getParam('errorMsg');
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Facility/Index', [
                'error' => $error,
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [
                'select_hospital'=>$select_hospital,
                ] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n1' => 'uk-active uk-open',
                'n1_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla 病院情報管理',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function Users()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('FacilityRegist');
            
            $hidden = [
 					"SMPFORM" => "%smpform:T_HpInfoReg%",
					"tenantId" => "%val:usr:tenantId%",
                ];
                
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => '施設登録',
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
                'n0' => 'uk-active uk-open',
                'n0_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla ユーザー管理',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    public function Regist()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('FacilityRegist');
            
            $hidden = [
 					"SMPFORM" => "%smpform:T_HpInfoReg%",
					"tenantId" => "%val:usr:tenantId%",
                ];
                
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => '施設登録',
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
                'n1_3' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla 病院情報登録',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }

    public function UserReg(){
        
        global $SPIRAL;
        try {
            
            $hidden = [
                "SMPFORM" => "%smpform:T_hpUserReg%",
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
                'n0' => 'uk-active uk-open',
                'n0_2' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
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
}

/***
 * 実行
 */
$FacilityController = new FacilityController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "Users")
    {
        echo $FacilityController->Users()->render();
    } else
    if($action === "Regist")
    {
        echo $FacilityController->Regist()->render();
    } else
    if($action === "UserReg")
    {
        echo $FacilityController->UserReg()->render();
    }
    else
    {
        echo $FacilityController->index()->render();
    }
}