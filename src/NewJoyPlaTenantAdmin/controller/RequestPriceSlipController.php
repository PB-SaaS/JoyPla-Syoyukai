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
use App\Model\PriceInfoView;

use Validate\PriceTrDB;

use stdClass;
use Exception;

class RequestPriceSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $step = $SPIRAL->getParam('step');
            $session = $SPIRAL->getSession();
            
            $back_url = $session->get('RequestSlip');
            
            $back_text = "見積依頼詳細";
            
            $auth = new Auth();
            $api_url = "%url/card:page_179161%";
            
            $card_id = $SPIRAL->getCardId();
            $price_info_view = PriceInfoView::find($card_id)->get();
            $price_info_view = $price_info_view->data->get(0);
            $current_distributorId = $SPIRAL->getParam('distributorId');
            if($current_distributorId == "" && $step == "" )
            {
                $current_distributorId = $price_info_view->distributorId;
            }
            
            if($price_info_view->requestFlg === "1")
            {
                throw new Exception("すでに採用された見積です",0);
            }
            
            if($step == "" || $step == "1")
            {
                $step_check = 1;
            }
            if($step == "2")
            {
                $hospitalId = $price_info_view->hospitalId;
                $distributorId = $SPIRAL->getParam('distributorId');
                if($distributorId == "")
                {
                    $step_check = 1;
                    $error['distributorId'] = "入力必須です";
                }
                else
                {
                    $count = Distributor::where('hospitalId',$hospitalId)->where('distributorId',$distributorId)->count();
                    if($count != 1)
                    {
                        $step_check = 1;
                        $error['distributorId'] = "値を確認してください";
                    }
                    else 
                    {
                        $step_check = 2;
                    }
                }
            }
            if($step_check == 1){
                $distributor = Distributor::where('hospitalId',$price_info_view->hospitalId)->get();
                $content = $this->view('NewJoyPlaTenantAdmin/view/RequestPriceUpdate/Step1', [
                    'price_info_view' => $price_info_view,
                    'distributor' => $distributor->data->all(),
                    'current_distributorId' => $current_distributorId,
                    'error' => $error,
                    'api_url' => $api_url
                    ] , false)->render();
            }
            
            if($step_check == 2){
                $distributorId = $SPIRAL->getParam('distributorId');
                $distributor = Distributor::where('distributorId',$distributorId)->where('hospitalId',$hospitalId)->get();
                $distributor = $distributor->data->get(0);
                
                $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                    'title' => '見積金額情報登録',
                    'width' => '100%',
                    'height'=> '100%',
                    'url' => '/regist/is',
                    'hiddens' => [
                            'SMPFORM'=> '%smpform:T_ReqPriceUp%',
                            'hospitalId' => $price_info_view->hospitalId,
                            'hospitalName' => $price_info_view->hospitalName,
                            'distributorId' => $distributor->distributorId,
                            'distributorName' => $distributor->distributorName,
                            'makerName' => '%val:usr:makerName%',
                            'itemId' => '%val:usr:itemId%',
                            'itemName' => '%val:usr:itemName%',
                            'itemCode' => '%val:usr:itemCode%',
                            'itemStandard' => '%val:usr:itemStandard%',
                            'itemJANCode' => '%val:usr:itemJANCode%',
                            'quantity' => '%val:usr:quantity%',
                            'quantityUnit' => '%val:usr:quantityUnit%',
                            'itemUnit' => '%val:usr:itemUnit%',
                            'price' => '%val:usr:price%',
                            'notice' => '%val:usr:notice%',
                            'priceId' => '%val:usr:priceId%',
                            'itemsAuthKey' => '%val:usr:authKey%',
                            'requestId' => '%val:usr:requestId%',
                        ]
                    ] , false)->render();
            }
            
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
                'title'     => 'JoyPla-Tenant-Master 見積金額登録',
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
$RequestPriceSlipController = new RequestPriceSlipController();

$action = $SPIRAL->getParam('Action');

{
    {
        echo $RequestPriceSlipController->index()->render();
    }
}