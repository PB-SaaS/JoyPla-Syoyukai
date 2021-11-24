<?php
namespace App\Controller;

use Controller;
use Csrf;
use ApiResponse;
use App\Lib\Auth;
use App\Model\PriceInfoView;
use App\Model\Hospital;
use App\Model\Distributor;

use Validate\PriceTrDB;

use stdClass;
use Exception;

class PriceSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $session = $SPIRAL->getSession();
            $session->put('PriceSlip',$_SERVER['REQUEST_URI']);
            
            $switcher = $SPIRAL->getParam('Switcher');
            $base_url = "%url/card:page_178889%";
            
            $switch_1 = ($switcher == "")? "uk-active" : "";
            $switch_2 = ($switcher == "InHospitalItem")? "uk-active" : "";
            
            $back_key = $SPIRAL->getParam('BACK');
            $back_url = "%url/rel:mpgt:PriceCont%&table_cache=true";
            $back_text = "金額管理";
            $sidemenu = [
                'n3' => 'uk-active uk-open',
                'n3_4' => 'uk-active',
            ];
            
            if($back_key == "ItemSlip" && $session->containsKey($back_key))
            {
                $sidemenu = [
                    'n3' => 'uk-active uk-open',
                    'n3_1' => 'uk-active',
                ];
                $back_text = "商品情報詳細";
                $back_url = $session->get($back_key);
            }
            else if($back_key === "")
            {
                $back_key = "PriceSlip";
            }
            
            
            
            $base_url = $base_url ."&BACK=".$back_key;
            $content = $this->view('NewJoyPlaTenantAdmin/view/PriceList/Slip', [
                'error' => $error,
                'back_key' => $back_key,
                'switch_1' => $switch_1,
                'switch_2' => $switch_2,	
                'back_key' => $back_key, 
                'form_url' => '%url/card:page_178889%&BACK='.$back_key,
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
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', $sidemenu , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => $back_url,
                'back_text' => $back_text,
                'title'     => 'JoyPla-Tenant-Master 金額管理',
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
            $step = $SPIRAL->getParam('step');
            $error = [];
            $step_check = 0;
            $api_url = "%url/card:page_178572%";
            
            $back_url = "";
            $back_text = "";
            $sidemenu = [];
            
            $session = $SPIRAL->getSession();
            $back_key = $SPIRAL->getParam('BACK');
            $back_url = "%url/card:page_178572%";
            $back_text = "金額管理詳細";
            $sidemenu = [
                'n3' => 'uk-active uk-open',
                'n3_4' => 'uk-active',
            ];
            
            if($back_key == "ItemSlip" && $session->containsKey($back_key))
            {
                $sidemenu = [
                    'n3' => 'uk-active uk-open',
                    'n3_1' => 'uk-active',
                ];
            }
            
            $base_url = $base_url ."&BACK=".$back_key;
            
            $card_id = $SPIRAL->getCardId();
            $price_info_view = PriceInfoView::find($card_id)->get();
            $price_info_view = $price_info_view->data->get(0);
            
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
                $content = $this->view('NewJoyPlaTenantAdmin/view/PriceUpdate/Step1', [
                    'price_info_view' => $price_info_view,
                    'distributor' => $distributor->data->all(),
                    'current_distributorId' => $SPIRAL->getParam('distributorId'),
                    'error' => $error,
                    'api_url' => $api_url
                    ] , false)->render();
            }
            
            if($step_check == 2){
                $distributorId = $SPIRAL->getParam('distributorId');
                $distributor = Distributor::where('distributorId',$distributorId)->where('hospitalId',$hospitalId)->get();
                $distributor = $distributor->data->get(0);
                
                $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                    'title' => '金額情報変更',
                    'width' => '100%',
                    'height'=> '100%',
                    'url' => '/regist/is',
                    'hiddens' => [
                            'SMPFORM'=> '%smpform:T_PriceUpdate%',
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
                        ]
                    ] , false)->render();
            }
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
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
                'title'     => 'JoyPla-Tenant-Master 金額情報変更',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function registInHospitalItem()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $id = $SPIRAL->getCardId();
            
            $priceinfo = PriceInfoView::find($id)->get();
            $priceinfo = $priceinfo->data->get(0);
            $priceId = $priceinfo->priceId;
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => '院内商品登録',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                        'SMPFORM'=> '%smpform:T_InhpItemIns%',
                        'hospitalId' => $priceinfo->hospitalId,
                        'hospitalName' => $priceinfo->hospitalName,
                        'distributorId' => $priceinfo->distributorId,
                        'distributorName' => $priceinfo->distributorName,
                        'priceMText' => $priceinfo->distributorName."：￥".number_format($priceinfo->price,2)."/".$priceinfo->quantity.$priceinfo->quantityUnit."(".$priceinfo->itemUnit.")",
                        'makerName' => '%val:usr:makerName%',
                        'itemId' => '%val:usr:itemId%',
                        'itemName' => '%val:usr:itemName%',
                        'itemCode' => '%val:usr:itemCode%',
                        'itemStandard' => '%val:usr:itemStandard%',
                        'itemJANCode' => '%val:usr:itemJANCode%',
                        'priceId' => $priceId,
                    ]
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false)->render();
        } finally {
            
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n3' => 'uk-active uk-open',
                'n3_4' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => '%url/card:page_178572%',
                'back_text' => '金額情報詳細',
                'title'     => 'JoyPla-Tenant-Master 院内商品登録',
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
$PriceSlipController = new PriceSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "update")
    {
        echo $PriceSlipController->update()->render();
    }
    else if($action === "registInHospitalItem")
    {
        echo $PriceSlipController->registInHospitalItem()->render();
    }
    else
    {
        echo $PriceSlipController->index()->render();
    }
}