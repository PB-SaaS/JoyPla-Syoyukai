<?php
namespace App\Controller;

use Controller;
use View;
use Csrf;

use App\Lib\Auth;
use App\Model\Hospital;
use App\Model\Distributor;
use App\Model\PriceInfoView;
use App\Model\Item;

use stdClass;
use Exception;

class GoodsSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            
            $session = $SPIRAL->getSession();
            
            $session->put('ItemSlip',$_SERVER['REQUEST_URI']);
            
            $switcher = $SPIRAL->getParam('Switcher');
            
            $base_url = "%url/card:page_178572%";
            
            $switch_1 = ($switcher == "")? "uk-active" : "";
            $switch_2 = ($switcher == "logs")? "uk-active" : "";
            $switch_3 = ($switcher == "priceList")? "uk-active" : "";
            $switch_4 = ($switcher == "InHospitalItems")? "uk-active" : "";
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/GoodsList/Slip', [
                'switch_1' => $switch_1,
                'switch_2' => $switch_2,
                'switch_3' => $switch_3,
                'switch_4' => $switch_4,
                'form_url' => '%url/card:page_178572%',
                'base_url' => $base_url,
                'price_api_url' => '%url/rel:mpgt:PriceCont%',
                'csrf_token' => Csrf::generate(16)
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
                'n3' => 'uk-active uk-open',
                'n3_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => '%url/rel:mpgt:Goods%&table_cache=true',
                'back_text' => '商品情報一覧',
                'title'     => 'JoyPla 商品管理',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    
    public function itemUpdate(): View
    {
        global $SPIRAL;
        try {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => '商品情報変更',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                        'SMPFORM'=> '%smpform:T_ItemUpdate%',
                        'itemId' => '%val:usr:itemId%',
                        'category' => '%val:usr:category:id%',
                        'smallCategory' => '%val:usr:smallCategory%', 
                        'itemName' => '%val:usr:itemName%',
                        'itemCode' => '%val:usr:itemCode%',
                        'itemStandard' => '%val:usr:itemStandard%',
                        'itemJANCode' => '%val:usr:itemJANCode%',
                        'officialFlag' => '%val:usr:officialFlag:id%',
                        'officialpriceOld' => '%val:usr:officialpriceOld%',
                        'officialprice' => '%val:usr:officialprice%',
                        'quantity' => '%val:usr:quantity%',
                        'quantityUnit' => '%val:usr:quantityUnit%',
                        'itemUnit' => '%val:usr:itemUnit%',
                        'tenantId' => '%val:usr:tenantId%',
                        'makerName' => '%val:usr:makerName%',
                        'itemsAuthKey' => '%val:usr:itemsAuthKey%',
                        'catalogNo' => '%val:usr:catalogNo%',
                        'serialNo' => '%val:usr:serialNo%',
                        'minPrice' => '%val:usr:minPrice%',
                        'lotManagement' => '%val:usr:lotManagement:id%',
                        'o_makerName' => '%val:usr:makerName%',
                        'o_category' => '%val:usr:category:id%',
                        'o_itemName' => '%val:usr:itemName%',
                        'o_itemCode' => '%val:usr:itemCode%',
                        'o_itemStandard' => '%val:usr:itemStandard%',
                        'o_itemJANCode' => '%val:usr:itemJANCode%',  
                        'o_officialFlag' => '%val:usr:officialFlag:id%',
                        'o_officialpriceO' => '%val:usr:officialpriceOld%',
                        'o_officialprice' => '%val:usr:officialprice%',
                        'o_quantity' => '%val:usr:quantity%',
                        'o_quantityUnit' => '%val:usr:quantityUnit%',
                        'o_itemUnit' => '%val:usr:itemUnit%',
                        'o_lotManagement' => '%val:usr:lotManagement:id%',
                        'o_catalogNo' => '%val:usr:catalogNo%',
                        'o_serialNo' => '%val:usr:serialNo%',
                        'o_minPrice' => '%val:usr:minPrice%',
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
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n3' => 'uk-active uk-open',
                'n3_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => '%url/card:page_178572%',
                'back_text' => '商品情報詳細',
                'title'     => 'JoyPla 商品情報変更',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function priceReg(): View
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $step = $SPIRAL->getParam('step');
            $error = [];
            $step_check = 0;
            $api_url = "%url/card:page_178572%";
            
            if($step == "" || $step == "1")
            {
                $step_check = 1;
            }
            if($step == "2")
            {
                $hospitalId = $SPIRAL->getParam('hospitalId');
                if($hospitalId == "")
                {
                    $step_check = 1;
                    $error['hospitalId'] = "入力必須です";
                }
                else
                {
                    $count = Hospital::where('hospitalId',$hospitalId)->where('tenantId',$auth->tenantId)->count();
                    if($count != 1)
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
            if($step == "3")
            {
                $hospitalId = $SPIRAL->getParam('hospitalId');
                $distributorId = $SPIRAL->getParam('distributorId');
                if($distributorId == "")
                {
                    $step_check = 2;
                    $error['distributorId'] = "入力必須です";
                }
                else
                {
                    $count = Distributor::where('hospitalId',$hospitalId)->where('distributorId',$distributorId)->count();
                    if($count != 1)
                    {
                        $step_check = 2;
                        $error['distributorId'] = "値を確認してください";
                    }
                    else 
                    {
                        $step_check = 3;
                    }
                }
            }
            if($step_check == 1){
                $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
                $content = $this->view('NewJoyPlaTenantAdmin/view/PriceReg/Step1', [
                    'hospital' => $hospital->data->all(),
                    'current_hospitalId' => $SPIRAL->getParam('hospitalId'),
                    'error' => $error,
                    'api_url' => $api_url
                    ] , false)->render();
            }
            
            if($step_check == 2){
                $hospitalId = $SPIRAL->getParam('hospitalId');
                $hospital = Hospital::where('hospitalId',$hospitalId)->get();
                $distributor = Distributor::where('hospitalId',$hospitalId)->get();
                $content = $this->view('NewJoyPlaTenantAdmin/view/PriceReg/Step2', [
                    'hospital' => $hospital->data->get(0),
                    'distributor' => $distributor->data->all(),
                    'current_distributorId' => $SPIRAL->getParam('distributorId'),
                    'error' => $error,
                    'api_url' => $api_url
                    ] , false)->render();
            }
            
            if($step_check == 3){
                $hospitalId = $SPIRAL->getParam('hospitalId');
                $distributorId = $SPIRAL->getParam('distributorId');
                
                $hospital = Hospital::where('hospitalId',$hospitalId)->get();
                $distributor = Distributor::where('distributorId',$distributorId)->get();
                
                $hospital = $hospital->data->get(0);
                $distributor = $distributor->data->get(0);
                
                $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                    'title' => '金額情報登録',
                    'width' => '100%',
                    'height'=> '100%',
                    'url' => '/regist/is',
                    'hiddens' => [
                            'SMPFORM'=> '%smpform:T_PriceReg%',
                            'hospitalId' => $hospital->hospitalId,
                            'hospitalName' => $hospital->hospitalName,
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
                        ]
                    ] , false)->render();
            }
            
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false)->render();
        } finally {
            
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n3' => 'uk-active uk-open',
                'n3_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => '%url/card:page_178572%',
                'back_text' => '商品情報詳細',
                'title'     => 'JoyPla 金額情報登録',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    
    public function inHospitalItemReg(): View
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $step = $SPIRAL->getParam('step');
            $error = [];
            $step_check = 0;
            $api_url = "%url/card:page_178572%";
            
            $cardId = $SPIRAL->getCardId();
            
            $iteminfo = Item::find($cardId)->get();
            $iteminfo = $iteminfo->data->get(0);
            
            if($step == "" || $step == "1")
            {
                $step_check = 1;
            }
            if($step == "2")
            {
                $hospitalId = $SPIRAL->getParam('hospitalId');
                if($hospitalId == "")
                {
                    $step_check = 1;
                    $error['hospitalId'] = "入力必須です";
                }
                else
                {
                    $count = Hospital::where('hospitalId',$hospitalId)->where('tenantId',$auth->tenantId)->count();
                    if($count != 1)
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
            if($step == "3")
            {
                $priceId = $SPIRAL->getParam('priceId');
                $hospitalId = $SPIRAL->getParam('hospitalId');
                if($priceId == "")
                {
                    $step_check = 1;
                    $error['priceId'] = "選択してください";
                }
                else
                {
                    $count = PriceInfoView::where('priceId',$priceId)->where('tenantId',$auth->tenantId)->count();
                    if($count != 1)
                    {
                        $step_check = 2;
                        $error['priceId'] = "値を確認してください";
                    }
                    else 
                    {
                        $step_check = 3;
                    }
                }
            }
            if($step_check == 1){
                $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
                $content = $this->view('NewJoyPlaTenantAdmin/view/InHospitalItemReg/Step1', [
                    'hospital' => $hospital->data->all(),
                    'current_hospitalId' => $SPIRAL->getParam('hospitalId'),
                    'error' => $error,
                    'api_url' => $api_url
                    ] , false)->render();
            }
            
            if($step_check == 2){
                $hospitalId = $SPIRAL->getParam('hospitalId');
                $hospital = Hospital::where('hospitalId',$hospitalId)->get();
                $priceInfoView = PriceInfoView::where('tenantId',$auth->tenantId)->where('hospitalId',$hospitalId)->where('itemId',$iteminfo->itemId)->where('notUsedFlag','1','!=')->get();
                $content = $this->view('NewJoyPlaTenantAdmin/view/InHospitalItemReg/Step2', [
                    'hospital' => $hospital->data->get(0),
                    'priceinfo' => $priceInfoView->data->all(),
                    'currentPriceId' => $SPIRAL->getParam('priceId'),
                    'error' => $error,
                    'api_url' => $api_url
                    ] , false)->render();
            }
            
            if($step_check == 3){
                $priceId = $SPIRAL->getParam('priceId');
                $priceinfo = PriceInfoView::where('priceId',$priceId)->get();
                $priceinfo = $priceinfo->data->get(0);
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
                            'priceMText' => $priceinfo->distributorName."：￥".number_format_jp($priceinfo->price)."/".number_format_jp($priceinfo->quantity).$priceinfo->quantityUnit."(".$priceinfo->itemUnit.")",
                            'makerName' => '%val:usr:makerName%',
                            'itemId' => '%val:usr:itemId%',
                            'itemName' => '%val:usr:itemName%',
                            'itemCode' => '%val:usr:itemCode%',
                            'itemStandard' => '%val:usr:itemStandard%',
                            'itemJANCode' => '%val:usr:itemJANCode%',
                            'priceId' => $priceId,
                        ]
                    ] , false)->render();
            }
            
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false)->render();
        } finally {
            
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n3' => 'uk-active uk-open',
                'n3_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => '%url/card:page_178572%',
                'back_text' => '商品情報詳細',
                'title'     => 'JoyPla 院内商品登録',
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
$GoodsSlipController = new GoodsSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'itemUpdate')
    {
        echo $GoodsSlipController->itemUpdate()->render();
    }
    else if($action === 'priceReg')
    {
        echo $GoodsSlipController->priceReg()->render();
    }
    else if($action === "inHospitalItemReg")
    {
        echo $GoodsSlipController->inHospitalItemReg()->render();
    }
    else 
    {
        echo $GoodsSlipController->index()->render();
    }
}