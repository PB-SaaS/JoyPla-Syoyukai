<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\Auth;
use App\Model\Hospital;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class MonthlyReportController extends Controller
{
    
    public function __construct()
    {
    }
    
    public function index($pattern = "GoodsBilling")
    {
        
        global $SPIRAL;
        try {
            $auth = new Auth();
            
            if($pattern === "GoodsBilling")
            {
                $auth->browseAuthority('ConsumMR');
                
                $sidemenu = [
                'n4' => 'uk-active uk-open',
                'n4_6' => 'uk-active',
                ];
                $title = "消費";
            }
            else if($pattern === "Order")
            {
                $auth->browseAuthority('OrderMR');
                
                $sidemenu = [
                'n4' => 'uk-active uk-open',
                'n4_7' => 'uk-active',
                ];
                $title = "注文";
            }
            else if($pattern === "Payout")
            {
                $auth->browseAuthority('PayoutMR');
                
                $sidemenu = [
                'n4' => 'uk-active uk-open',
                'n4_9' => 'uk-active',
                ];
                $title = "払出";
            }
            else if($pattern === "Receiving")
            {
                $auth->browseAuthority('ReceivingMR');
                
                $sidemenu = [
                'n4' => 'uk-active uk-open',
                'n4_8' => 'uk-active',
                ];
                $title = "入庫";
            }
            
            $step = $SPIRAL->getParam('step');
            $step_check = 1;
            $error = [];
                
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            $hospital = $hospital->data->all();
            
            if($step == "" || $step == "1")
            {
                $step_check = 1;
            }
            
            if($step == "2")
            {
                if($SPIRAL->getParam('hospitalId') == "")
                {
                    $step_check = 1;
                    $error['hospitalId'] = "入力必須です";
                }
                else
                {
                    $hospitalId = $SPIRAL->getParam('hospitalId');
                    $check = false;
                    foreach($hospital as $h)
                    {
                        if($hospitalId === $h->hospitalId)
                        {
                            $check = true;
                        }
                    }
                    if(!$check)
                    {
                        $step_check = 1;
                        $error['hospitalId'] = "値を確認してください";
                    }
                    else 
                    {
                        $step_check = 2;
                    }
                }
            }
            
            $session = $SPIRAL->getSession(true , 3600);
            $content = $this->view('NewJoyPlaTenantAdmin/view/History/HospitalIdSelect', [
                'hospital' => $hospital,
                'current_hospitalId' => $SPIRAL->getParam('hospitalId'),
                'error' => $error,
                'api_url' => "%url/rel:mpgt:MonthlyReport%",
                'Action' => $pattern,
                'title' => $title,
                ] , false)->render();
                
            if($step_check == 2)
            {
                
                if($pattern === "GoodsBilling")
                {
                    $GoodsBillingMRController = new GoodsBillingMRController();
                    $content .= $GoodsBillingMRController->index();
                }
                else if($pattern === "Order")
                {
                    $OrderMRController = new OrderMRController();
                    $content .= $OrderMRController->index();
                }
                else if($pattern === "Payout")
                {
                    $PayoutMRController = new PayoutMRController();
                    $content .= $PayoutMRController->index();
                }
                else if($pattern === "Receiving")
                {
                    $ReceivingMRController = new ReceivingMRController();
                    $content .= $ReceivingMRController->index();
                }
            }
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', $sidemenu , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla 月次レポート',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
            ],false);
        }
    }
}

require_once "NewJoyPlaTenantAdmin/controller/GoodsBillingMRController.php";
require_once "NewJoyPlaTenantAdmin/controller/OrderMRController.php";
require_once "NewJoyPlaTenantAdmin/controller/ReceivingMRController.php";
require_once "NewJoyPlaTenantAdmin/controller/PayoutMRController.php";

/***
 * 実行
 */
$MonthlyReportController = new MonthlyReportController();

$action = $SPIRAL->getParam('Action');

{
    echo $MonthlyReportController->index($action)->render();
}
