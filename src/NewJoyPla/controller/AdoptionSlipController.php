<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Tenant;
use App\Model\QuoteRequest;
use App\Model\Price;
use App\Model\QuoteItem;
use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class AdoptionSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            
            $record_id = (int)$SPIRAL->getCardId();
            
            $quote_items = QuoteItem::find($record_id)->get(0);
            $quote_items = $quote_items->data->get(0);
            
            if($quote_items->requestFlg != 3)
            {
               throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpgt:Price%&Action=QuoteList&table_cache=true">見積依頼一覧</a></li>
            <li><a href="{$_SERVER['HTTP_REFERER']}">見積依頼詳細</a></li>
            <li><span>見積商品採用</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '見積商品採用',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
            		"SMPFORM" => "%smpform:330_quoteAdopt%",
            		"requestFlg" => "1",
            		"lotManagement" => "%val:usr:lotManagement:id%",
            		"category" => "%val:usr:category:id%",
            		"itemName" => "%val:usr:itemName%",
            		"itemCode" => "%val:usr:itemCode%",
            		"itemStandard" => "%val:usr:itemStandard%",
            		"itemJANCode" => "%val:usr:itemJANCode%",
            		"makerName" => "%val:usr:makerName%",
            		"catalogNo" => "%val:usr:catalogNo%",
            		"serialNo" => "%val:usr:serialNo%",
            		"officialFlag" => "%val:usr:officialFlag:id%",
            		"officialprice" => "%val:usr:officialprice%",
            		"officialpriceOld" => "%val:usr:officialpriceOld%",
            		"quantity" => "%val:usr:quantity%",
            		"quantityUnit" => "%val:usr:quantityUnit%",
            		"itemUnit" => "%val:usr:itemUnit%",
            		"minPrice" => "%val:usr:minPrice%",
            		"requestItemId" => "%val:usr:requestItemId%",
            		"authKey" => "%val:usr:authKey%",
            		"tenantId" => "%val:@usr:tenantId%",
            		"distributorId" => "%val:usr:distributorId%",
            		"requestId" => "%val:usr:requestId%",
            		"janTenantId" => "%val:usr:itemJANCode%%val:usr:tenantId%",
                    ]
                ] , false);

        } catch ( Exception $ex ) {
            $title = 'JoyPla エラー';
            $header = $this->view('NewJoyPla/src/Header', [], false);
            
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
                'title'     => 'JoyPla 見積商品採用',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function notAdopted()
    {
        global $SPIRAL;
        try {
            
            $record_id = (int)$SPIRAL->getCardId();
            
            $quote_items = QuoteItem::find($record_id)->get(0);
            $quote_items = $quote_items->data->get(0);
            
            if($quote_items->requestFlg != 3)
            {
               throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
        
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpgt:Price%&Action=QuoteList&table_cache=true">見積依頼一覧</a></li>
            <li><a href="{$_SERVER['HTTP_REFERER']}">見積依頼詳細</a></li>
            <li><span>見積商品不採用</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '見積商品不採用',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
            		"SMPFORM" => "%smpform:330_quoteReject%",
            		"requestFlg" => "2",
            		"lotManagement" => "%val:usr:lotManagement:id%",
            		"category" => "%val:usr:category:id%",
            		"itemName" => "%val:usr:itemName%",
            		"itemCode" => "%val:usr:itemCode%",
            		"itemStandard" => "%val:usr:itemStandard%",
            		"itemJANCode" => "%val:usr:itemJANCode%",
            		"makerName" => "%val:usr:makerName%",
            		"catalogNo" => "%val:usr:catalogNo%",
            		"serialNo" => "%val:usr:serialNo%",
            		"officialFlag" => "%val:usr:officialFlag:id%",
            		"officialprice" => "%val:usr:officialprice%",
            		"officialpriceOld" => "%val:usr:officialpriceOld%",
            		"quantity" => "%val:usr:quantity%",
            		"quantityUnit" => "%val:usr:quantityUnit%",
            		"itemUnit" => "%val:usr:itemUnit%",
            		"minPrice" => "%val:usr:minPrice%",
            		"requestItemId" => "%val:usr:requestItemId%",
            		"authKey" => "%val:usr:authKey%",
            		"tenantId" => "%val:@usr:tenantId%",
            		"distributorId" => "%val:usr:distributorId%",
            		"reqitemsAuthKey" => "%val:usr:reqitemsAuthKey%",
            		"requestId" => "%val:usr:requestId%",
            		"janTenantId" => "%val:usr:itemJANCode%%val:usr:tenantId%",
                    ]
                ] , false);

        } catch ( Exception $ex ) {
            $title = 'JoyPla エラー';
            $header = $this->view('NewJoyPla/src/Header', [], false);
            
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
                'title'     => 'JoyPla 見積商品不採用',
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
$AdoptionSlipController = new AdoptionSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'notAdopted')
    {
        echo $AdoptionSlipController->notAdopted()->render();
    } 
    else 
    {
        echo $AdoptionSlipController->index()->render();
    }
}