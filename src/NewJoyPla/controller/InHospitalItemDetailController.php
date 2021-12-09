<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\InHospitalItemView;
use App\Model\Hospital;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class InHPItemDetailController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);

            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            $cardId = $SPIRAL->getCardId();
            if($cardId == null)
            {
                throw new Exception("ページが存在しません",404);
            }
            $api_url = '%url/card:page_176013%';

            $content = $this->view('NewJoyPla/view/InHospitalProductsDetail', [
                'userInfo' => $user_info,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
            ] , false);

        } catch ( Exception $ex ) {
            $title = 'JoyPla エラー';
            $header = $this->view('NewJoyPla/src/Header', [], false);
            
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false);

        } finally {
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 院内商品情報詳細',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function InHospitalItemUpdate(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            $api_url = '%url/card:page_176013%';
            
            if($user_info->isUser())
            {    
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpgt:Product%&Action=InHospitalItem&table_cache=true">院内商品一覧</a></li>
            <li><a href="%url/card:page_176013%">院内商品情報</a></li>
            <li><span>院内商品情報変更</span></li>
EOM;
        
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '院内商品情報変更',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                    "SMPFORM" => "%smpform:330_inHpItemsC%",
                    "hospitalId" => "%val:usr:hospitalId%",
                    "makerName" => "%val:usr:makerName%",
                    "itemId" => "%val:usr:itemId%",
                    "itemName" => "%val:usr:itemName%",
                    "itemCode" => "%val:usr:itemCode%",
                    "itemStandard" => "%val:usr:itemStandard%",
                    "itemJANCode" => "%val:usr:itemJANCode%",
                    "price" => "%val:usr:price%",
                    "inHospitalItemId" => "%val:usr:inHospitalItemId%",
                    "authKey" => "%val:usr:authKey%",
                    "notUsedFlag" => "%val:usr:notUsedFlag%",
                    "distributorId" => "%val:usr:distributorId%",
                    "catalogNo" => "%val:usr:catalogNo%",
                    "serialNo" => "%val:usr:serialNo%",
                    "quantity" => "%val:usr:quantity%",
                    "quantityUnit" => "%val:usr:quantityUnit%",
                    "itemUnit" => "%val:usr:itemUnit%",
                    "price" => "%val:usr:price%",
                    "priceId" => "%val:usr:priceId%",
                    "medicineCategory" => "%val:usr:medicineCategory%",
                    "homeCategory" => "%val:usr:homeCategory%",
                    "HPstock" => "%val:usr:HPstock%",
                    "notice" => "%val:usr:notice%",
                    "unitPrice" => "%val:usr:unitPrice%",
                    "measuringInst" => "%val:usr:measuringInst%",
                    "oldPrice" => "%val:usr:price%",
                    "oldUnitPrice" => "%val:usr:unitPrice%",
                    "user_login_id" => "%val:@usr:loginId%",
                    "user_auth_key" => "%val:@usr:authKey%",
                    ]
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 院内商品情報変更',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function createLabel()
    {
        global $SPIRAL;

        $title = 'JoyPla ラベル発行';

        try {
            
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $card_Id = (int)$SPIRAL->getCardId();
            if($card_Id == null)
            { 
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $card = InHospitalItemView::where('hospitalId',$user_info->getHospitalId())->find($card_Id)->get();
            $card = $card->data->all();
            
            $api_url = '%url/card:page_176013%';

        	$link = '%url/rel:mpgt:ProductQuotation%';
        	
        	$quantity = $SPIRAL->getParam('quantity');
        	$printCount = $SPIRAL->getParam('printCount');
        	
        	$hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
            
        	$design = $hospital->labelDesign1;
        	
            if($design == ""){
        	    $design = $this->design();
            }
            
            $content = $this->view('NewJoyPla/view/InHospitalItemCreateLabel', [
                'user_info' => $user_info,
                'api_url' => $api_url,
                'cardItems' => $card,
                'quantity' => $quantity,
                'original_design' => $design,
                'printCount' => $printCount,
                'csrf_token' => Csrf::generate(16),
                'link' => $link,
            ] , false);

        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false);

        } finally {

            $head   = $this->view('NewJoyPla/view/template/parts/Head', [
                'new' => true
                ] , false);
                
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            $style   = $this->view('NewJoyPla/view/template/parts/LabelsCss', [] , false)->render();
                
            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [] , false)->render();
                
            $script   = $this->view('NewJoyPla/view/template/parts/Script', [] , false)->render();
                
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => $title,
                'content'   => $content->render(),
                'script' => $script,
                'style' => $style,
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    private function design()
    {
        return <<<EOM
	<div class="printarea uk-margin-remove">
		<span>%JoyPla:distributorName%</span><br>
		<span>メーカー名：%JoyPla:itemMaker%</span><br>
		<span>商品名：%JoyPla:itemName%</span><br>
		<span>規格：%JoyPla:itemStandard%</span><br>
		<span>商品コード：%JoyPla:itemCode%</span>
		<span>入数：%JoyPla:quantity%%JoyPla:quantityUnit%</span><br>
		<span>%JoyPla:nowTime%</span><br>
		<div class="uk-text-center" id="barcode_%JoyPla:num%">%JoyPla:barcodeId%</div>
	</div>
EOM;
    }
}

/***
 * 実行
 */
$InHPItemDetailController = new InHPItemDetailController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'InHospitalItemUpdate')
    {
        echo $InHPItemDetailController->InHospitalItemUpdate()->render();
    } 
    else if($action === "createLabel")
    {
        echo $InHPItemDetailController->createLabel()->render();
    }
    else 
    {
        echo $InHPItemDetailController->index()->render();
    }
    
}