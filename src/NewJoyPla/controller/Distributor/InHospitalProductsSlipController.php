<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\ReceivingView;
use App\Model\ReceivingHistory;
use App\Model\InHospitalItemView;
use App\Model\Hospital;

use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;


/**
 * 発注書
 */
class InHospitalProductsSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;

        $title = 'JoyPla 院内商品情報';

        try {
            
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isHospitalUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $card_Id = (int)$SPIRAL->getCardId();
            if($card_Id == null)
            {   
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $api_url = '%url/card:page_178244%';

        	$link = '%url/rel:mpgt:ProductQuotation%';
            
            $content = $this->view('NewJoyPla/view/Distributor/InHospitalProductsSlip', [
                'user_info' => $user_info,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'link' => $link,
            ] , false);

        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false);

        } finally {

            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => $title,
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

            if ($user_info->isHospitalUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $card_Id = (int)$SPIRAL->getCardId();
            if($card_Id == null)
            {   
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $card = InHospitalItemView::where('distributorId',$user_info->getDistributorId())->find($card_Id)->get();
            $card = $card->data->all();
            
            $api_url = '%url/card:page_178244%';

        	$link = '%url/rel:mpgt:ProductQuotation%';
        	
        	$quantity = $SPIRAL->getParam('quantity');
        	$printCount = $SPIRAL->getParam('printCount');
        	
        	$hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
            
        	$design = $hospital->labelDesign1;
        	
            if($design == ""){
        	    $design = $this->design();
            }
            
            $content = $this->view('NewJoyPla/view/Distributor/CreateLabel', [
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
$InHospitalProductsSlipController = new InHospitalProductsSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "createLabel")
    {
        echo $InHospitalProductsSlipController->createLabel()->render();
    }
    else
    {
        echo $InHospitalProductsSlipController->index()->render();
    }
}