<?php
namespace App\Controller;

use Controller;
use Csrf;
use ApiResponse;
use App\Lib\Auth;
use App\Model\Tenant;
use App\Model\Hospital;
use App\Model\DistributorAndHospitalDB;

use Validate\DistributorDB;

use stdClass;
use Exception;

class StocksController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            $distributor = DistributorAndHospitalDB::where('tenantId',$auth->tenantId)->get();
            $select_hospital = [['text'=> '----- 選択してください -----' ,'value'=> '' ]];
            foreach($hospital->data->all() as $h)
            {
                $select_hospital[] = ['text'=> $h->hospitalName ,'value'=> $h->hospitalName ];
            }
            $select_distributor = [['text'=> '----- 選択してください -----' ,'value'=> '' ]];
            foreach($distributor->data->all() as $d)
            {
                $select_distributor[] = ['text'=> $d->distributorName ,'value'=> $d->distributorName ];
            }
            $content = $this->view('NewJoyPlaTenantAdmin/view/Stocks/Index', [
                //'error' => $error,
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [
                'select_hospital'=>$select_hospital,
                'select_distributor'=>$select_distributor,
                ] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n5' => 'uk-active uk-open',
                'n5_1' => 'uk-active'
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 卸業者管理',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function Lots()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            $distributor = DistributorAndHospitalDB::where('tenantId',$auth->tenantId)->get();
            $select_hospital = [['text'=> '----- 選択してください -----' ,'value'=> '' ]];
            foreach($hospital->data->all() as $h)
            {
                $select_hospital[] = ['text'=> $h->hospitalName ,'value'=> $h->hospitalName ];
            }
            $content = $this->view('NewJoyPlaTenantAdmin/view/Stocks/Lots', [
                //'error' => $error,
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
                'n5' => 'uk-active uk-open',
                'n5_2' => 'uk-active'
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 卸業者管理',
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
$StocksController = new StocksController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "Lots")
    {
        echo $StocksController->Lots()->render();
    }
    else
    {
        echo $StocksController->index()->render();
    }
}