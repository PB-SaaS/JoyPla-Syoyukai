<?php
namespace App\Controller;

use Controller;
use Csrf;

use App\Lib\Auth;
use App\Model\Hospital;
use App\Model\Distributor;
use App\Model\PriceInfoView;
use App\Model\InHospitalItem;

use stdClass;
use Exception;

class InHospitalItemSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $session = $SPIRAL->getSession();
            $switcher = $SPIRAL->getParam('Switcher');
            
            $back_key = $SPIRAL->getParam('BACK');
            $back_url = "%url/rel:mpgt:InHospitalItem%&table_cache=true";
            $back_text = "院内商品管理";
            $sidemenu = [
                'n3' => 'uk-active uk-open',
                'n3_6' => 'uk-active',
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
            else if($back_key == "PriceSlip" && $session->containsKey($back_key))
            {
                $sidemenu = [
                    'n3' => 'uk-active uk-open',
                    'n3_4' => 'uk-active',
                ];
                $back_text = "金額情報詳細";
                $back_url = $session->get($back_key);
            }
            $back_url = $back_url."&BACK=".$back_key;
            
            $form_url = "%url/card:page_178914%"."&BACK=".$back_key;
            
            $error = $SPIRAL->getParam('errorMsg');
            $content = $this->view('NewJoyPlaTenantAdmin/view/InHospitalItemList/Slip', [
                'error' => $error,
                'form_url' => $form_url,
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
                'title'     => 'JoyPla 院内商品詳細',
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
            
            $session = $SPIRAL->getSession();
            
            $back_key = $SPIRAL->getParam('BACK');
            $back_url = "%url/card:page_178914%";
            $back_text = "院内商品詳細";
            
            $sidemenu = [
                'n3' => 'uk-active uk-open',
                'n3_6' => 'uk-active',
            ];
            
            if($back_key == "ItemSlip" && $session->containsKey($back_key))
            {
                $sidemenu = [
                    'n3' => 'uk-active uk-open',
                    'n3_1' => 'uk-active',
                ];
            }
            else if($back_key == "PriceSlip" && $session->containsKey($back_key))
            {
                $sidemenu = [
                    'n3' => 'uk-active uk-open',
                    'n3_4' => 'uk-active',
                ];
            }
            
            $back_url = $back_url."&BACK=".$back_key;
            
            $step = $SPIRAL->getParam('step');
            $cardId = $SPIRAL->getCardId();
            
            $iteminfo = InHospitalItem::find($cardId)->get();
            $iteminfo = $iteminfo->data->get(0);
            
            $currentPriceId = $SPIRAL->getParam('priceId');
            $api_url = "%url/card:page_178914%";
            
            $error = [];
            $step_check = 0;
            if($step == "" || $step == "1")
            {
                $step_check = 1;
            }
            
            if($step == "2")
            {
                $priceId = $SPIRAL->getParam('priceId');
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
                        $step_check = 1;
                        $error['priceId'] = "値を確認してください";
                    }
                    else 
                    {
                        $step_check = 2;
                    }
                }
            }
            if($step_check == 1){
                $priceinfo = PriceInfoView::where('tenantId',$auth->tenantId)->where('hospitalId',$iteminfo->hospitalId)->where('itemId',$iteminfo->itemId)->where('notUsedFlag','1','!=')->get();
                $content = $this->view('NewJoyPlaTenantAdmin/view/InHospitalItemUpdate/Step1', [
                    'priceinfo' => $priceinfo->data->all(),
                    'currentPriceId' => $currentPriceId,
                    'error' => $error,
                    'api_url' => $api_url
                    ] , false)->render();
            }
            
            if($step_check == 2){
                
                $priceId = $SPIRAL->getParam('priceId');
                $priceinfo = PriceInfoView::where('priceId',$priceId)->get();
                $priceinfo = $priceinfo->data->get(0);
                
                $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                    'title' => '院内商品情報更新',
                    'width' => '100%',
                    'height'=> '100%',
                    'url' => '/regist/is',
                    'hiddens' => [
                            'SMPFORM'=> '%smpform:T_InhpItemUpdate%',
                            'notUsedFlag' => '%val:usr:notUsedFlag%',
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
                            'notice' => '%val:usr:notice%',
                            'measuringInst' => '%val:usr:measuringInst%',
                            'unitPrice' => '%val:usr:unitPrice%',
                            'minPrice' => '%val:usr:minPrice%',
                            'priceId' => $priceId,
                            'medicineCategory' =>  '%val:usr:medicineCategory%',
                            'homeCategory' => '%val:usr:homeCategory%',
                            'authKey' => '%val:usr:authKey%',
                            'inHospitalItemId' => '%val:usr:inHospitalItemId%',
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
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', $sidemenu , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => $back_url,
                'back_text' => $back_text,
                'title'     => 'JoyPla 院内商品情報変更',
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
$InHospitalItemSlipController = new InHospitalItemSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "update")
    {
        echo $InHospitalItemSlipController->update()->render();
    }
    else 
    {
        echo $InHospitalItemSlipController->index()->render();
    }
}