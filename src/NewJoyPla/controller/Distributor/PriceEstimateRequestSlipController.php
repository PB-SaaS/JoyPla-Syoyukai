<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Price;

use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;


/**
 * 見積金額
 */
class PriceEstimateRequestSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);
            $record_id = (int)$SPIRAL->getCardId();
            $price_data = Price::find($record_id)->get();
            $price_data = $price_data->data->get(0);
            
            $breadcrumb = <<<EOM
    		    <li><a target="_parent" href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="%url/rel:mpg:top%&page=page7">商品・見積</a></li>
    		    <li><a target="_parent" href="%url/rel:mpgt:ProductQuotation%&Action=Quotation&table_cache=true">見積依頼一覧</a></li>
    		    <li><a target="_parent" href="javascript:history.back()">見積依頼詳細</a></li>
    		    <li><span>見積金額登録</span></li>
EOM;
            if($price_data->requestFlg == 1)
            {

                $form_content = <<<EOM
                <h1>見積金額登録 - 入力</h1>
                <div class="smp_tmpl uk-text-left">
                    <div class="sub_text">
                        すでに採用済みの情報です。<br>		
                    </div>
                </div>
EOM;
                $content = $this->view('NewJoyPla/view/template/FormDesign', [
                    'title' => '見積金額登録',
                    'breadcrumb' => $breadcrumb,
                    'form_content' => $form_content
                    ] , false);
            } 
            else 
            {
                $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                    'breadcrumb' => $breadcrumb,
                    'title' => '見積金額登録',
                    'width' => '100%',
                    'height'=> '100%',
                    'url' => '/regist/is',
                    'hiddens' => [
                            'SMPFORM'=> '%smpform:310_ORQItemC%',
                    		"itemName" => "%val:usr:itemName%",
                    		"itemCode" => "%val:usr:itemCode%",
                    		"itemStandard" => "%val:usr:itemStandard%",
                    		"itemJANCode" => "%val:usr:itemJANCode%",
                    		"makerName" => "%val:usr:makerName%",
                    		"category" => "%val:usr:category%",
                    		"catalogNo" => "%val:usr:catalogNo%",
                    		"serialNo" => "%val:usr:serialNo%",
                    		"priceId" => "%val:usr:priceId%",
                    		"authKey" => "%val:usr:authKey%",
                    		"itemId" => "%val:usr:itemId%",
                    		"distributorId" => "%val:usr:distributorId%",
                    		"quantity" => "%val:usr:quantity%",
                    		"price" => "%val:usr:price%",
                    		"hospitalId" => "%val:usr:hospitalId%",
                    		"requestFlg" => "4",
                    		"quantityUnit" => "%val:usr:quantityUnit%",
                    		"itemUnit" => "%val:usr:itemUnit%",
                    		"notice" => "%val:usr:notice%",
                    		"notUsedFlag" => "%val:usr:notUsedFlag%",
                    		"lotManagement" => "%val:usr:lotManagement:v%"
                        ]
                    ] , false);
            }
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
                'title'     => 'JoyPla 見積金額登録',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
}

/***
 * 実行
 */
$PriceEstimateRequestSlipController = new PriceEstimateRequestSlipController();

$action = $SPIRAL->getParam('Action');

{
    echo $PriceEstimateRequestSlipController->index()->render();
}