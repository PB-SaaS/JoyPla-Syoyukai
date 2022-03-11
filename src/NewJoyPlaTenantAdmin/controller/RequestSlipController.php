<?php
namespace App\Controller;

use Controller;
use Csrf;
use ApiResponse;
use App\Lib\Auth;
use App\Model\Tenant;
use App\Model\Hospital;
use App\Model\Distributor;
use App\Model\DistributorAndHospitalDB;
use App\Model\Item;
use App\Model\Price;
use App\Model\PriceUpsertTrDB;

use Validate\PriceTrDB;

use stdClass;
use Exception;

class RequestSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $session = $SPIRAL->getSession();
            
            $back_url = $session->get('RequestList');
            $session->put('RequestSlip',$_SERVER['REQUEST_URI']);
            $back_text = "見積依頼管理";
            $switcher = $SPIRAL->getParam('Switcher');
            $base_url = "%url/card:page_179154%";
            
            $switch_1 = ($switcher == "")? "uk-active" : "";
            $switch_2 = ($switcher == "PriceReg")? "uk-active" : "";
            
            $auth = new Auth();
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/RequestList/Slip', [
                //'error' => $error,
                'switch_1' => $switch_1,
                'switch_2' => $switch_2,	
                'form_url' => '%url/card:page_179154%',
                'base_url' => $base_url
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [
                'linkText'=> '情報入力'
                ] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n6' => 'uk-active uk-open',
                'n6_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => $back_url,
                'back_text' => $back_text,
                'title'     => 'JoyPla 見積依頼詳細',
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
$RequestSlipController = new RequestSlipController();

$action = $SPIRAL->getParam('Action');

{
    {
        echo $RequestSlipController->index()->render();
    }
}