<?php
namespace App\Controller;

use Controller;
use Csrf;

use App\Lib\Auth;
use App\Model\Hospital;
use App\Model\DistributorAndHospitalDB;

use stdClass;
use Exception;

class HistoryController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('ConsumedHistory');
            
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            $select_hospital = [['text'=> '----- 選択してください -----' ,'value'=> '' ]];
            foreach($hospital->data->all() as $h)
            {
                $select_hospital[] = ['text'=> $h->hospitalName ,'value'=> $h->hospitalName ];
            }
            $content = $this->view('NewJoyPlaTenantAdmin/view/History/ConsumedHistoryList', [
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [
                'select_hospital'=>$select_hospital
                ] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n4' => 'uk-active uk-open',
                'n4_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 消費履歴詳細一覧',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function OrderedHistoryList()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('OrderedHistory');
            
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
            $content = $this->view('NewJoyPlaTenantAdmin/view/History/OrderedHistoryList', [
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
                'n4' => 'uk-active uk-open',
                'n4_2' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 注文履歴詳細一覧',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function ReceivedHistoryList()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('ReceivedHistory');
            
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
            $content = $this->view('NewJoyPlaTenantAdmin/view/History/ReceivedHistoryList', [
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
                'n4' => 'uk-active uk-open',
                'n4_3' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 入荷履歴詳細一覧',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function PayoutHistoryList()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('PayoutHistory');
            
            $auth = new Auth();
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            $select_hospital = [['text'=> '----- 選択してください -----' ,'value'=> '' ]];
            foreach($hospital->data->all() as $h)
            {
                $select_hospital[] = ['text'=> $h->hospitalName ,'value'=> $h->hospitalName ];
            }
            $content = $this->view('NewJoyPlaTenantAdmin/view/History/PayoutHistoryList', [
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [
                'select_hospital'=>$select_hospital,
                //'select_distributor'=>$select_distributor,
                ] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n4' => 'uk-active uk-open',
                'n4_5' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 入荷履歴詳細一覧',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function ReceivingHistoryMCH()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('ReceiveHistoryMCH');
            
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
            $content = $this->view('NewJoyPlaTenantAdmin/view/History/ReceivingHistoryMCH', [
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
                'n4' => 'uk-active uk-open',
                'n4_10' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 入庫履歴詳細一覧',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    
    public function ReturnHistoryMCH()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('ReturnHistoryMCH');
            
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
            $content = $this->view('NewJoyPlaTenantAdmin/view/History/ReturnHistoryMCH', [
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
                'n4' => 'uk-active uk-open',
                'n4_11' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 返品履歴詳細一覧',
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
$HistoryController = new HistoryController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "ReturnHistoryMCH")
    {
        echo $HistoryController->ReturnHistoryMCH()->render();
    }
    else if($action === "ReceivingHistoryMCH")
    {
        echo $HistoryController->ReceivingHistoryMCH()->render();
    }
    else if($action === "OrderedHistoryList")
    {
        echo $HistoryController->OrderedHistoryList()->render();
    }
    else if($action === "ReceivedHistoryList")
    {
        echo $HistoryController->ReceivedHistoryList()->render();
    }
    else if($action === "PayoutHistoryList")
    {
        echo $HistoryController->PayoutHistoryList()->render();
    }
    else
    {
        echo $HistoryController->index()->render();
    }
}