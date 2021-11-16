<?php
namespace App\Controller;

use Controller;
use Csrf;
use App\Lib\Auth;
use App\Model\Tenant;

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
                'title'     => 'JoyPla-Tenant-Master 施設管理',
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
            $tenant = Tenant::where('tenantId',$auth->tenantId)->get();
            $tenant = $tenant->data->get(0);
            $hidden = [
 					"SMPFORM" => "%smpform:T_HpInfoReg%",
					"tenantId" => "%val:usr:tenantId%",
					"distributorName" => $tenant->tenantName,
					"postalCode" => $tenant->postalCode,
					"prefectures" => $tenant->prefectures,
					"address" => $tenant->address,
					"phoneNumber" => $tenant->phoneNumber,
					"faxNumber" => $tenant->faxNumber,
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
                'title'     => 'JoyPla-Tenant-Master 施設登録',
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
    if($action === "Regist")
    {
        echo $FacilityController->Regist()->render();
    }
    else
    {
        echo $FacilityController->index()->render();
    }
}