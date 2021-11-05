<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Hospital;
use App\Model\Tenant;
use App\Model\Distributor;
use App\Model\HospitalUser;
use App\Model\Division;
use App\Model\QuoteRequest;
use App\Model\QuoteItem;
use App\Model\PriceQuoteItem;
use App\Model\Price;

use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class QuoteSlipSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;

        $title = 'JoyPla 見積依頼詳細';

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

            $card = QuoteRequest::where('distributorId',$user_info->getDistributorId())->find($card_Id)->get();
            $card = $card->data->get(0);
            
            if($card->requestStatus == 1)
            {
                QuoteRequest::where('distributorId',$user_info->getDistributorId())->find($card_Id)->update(
                    ['requestStatus' => 2]
                    );
            } 
            else 
            {
                $request_id = $card->requestId;
                $quote_request = $card;
                
                $price_data = Price::where('requestId',$request_id)->get();
                $price_data = $price_data->data->all();
                
                $quote_item = QuoteItem::where('requestId',$request_id)->get();
                $quote_item = $quote_item->data->all();
                
                $status = [];
                
                $saiyou = 0;
                $husaiyou = 0;
                $sonota = 0;
                
                foreach($price_data as $item)
                {
                    $status[] = $item->requestFlg;
                }
                foreach($quote_item as $item)
                {
                    $status[] = $item->requestFlg;
                }
                
                foreach($status as $s)
                {
                    if($s == 1)
                    {
                        $saiyou++;
                    } 
                    else if($s == 2)
                    {
                        $husaiyou++;
                    }
                    else
                    {
                        $sonota++;
                    }
                }
                
                $status_id = $quote_request->requestStatus;
                
                if(count($status) > 0 )
                {
            /**
             *  1	未開封
             *  2	開封
             *  3	商品記載有
             *  4	一部却下
             *  5	一部採用
             *  6	却下
             *  7	採用
                 */
                    if(count($status) == $sonota)
                    {
                        $status_id = 3;
                    }
                    else if(count($status) == $saiyou)
                    {
                        $status_id = 7;
                    }
                    else if(count($status) == $husaiyou)
                    {
                        $status_id = 6;
                    }
                    else if($husaiyou > 0 && $saiyou == 0 )
                    {
                        $status_id = 4;
                    }
                    else if($saiyou > 0 )
                    {
                        $status_id = 5;
                    }
                }
                
                QuoteRequest::where('requestId',$request_id)->update(
                    ['requestStatus' => $status_id]
                    );
            }
            
            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
            
            $tenant = Tenant::where('tenantId',$hospital->tenantId)->get();
            $tenant = $tenant->data->get(0);
            
            $api_url = '%url/card:page_266429%';

            $link_title = "見積依頼一覧";
        	$link = '%url/rel:mpgt:ProductQuotation%&Action=Quotation';
            
            $content = $this->view('NewJoyPla/view/Distributor/QuoteSlip', [
                'user_info' => $user_info,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'isMulti' => ($tenant->tenantKind == 2),
                'link_title' => $link_title,
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
    
    
    public function regQuoteItem(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            $api_url = '%url/card:page_266429%';
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpgt:ProductQuotation%&Action=Quotation&table_cache=true">見積依頼一覧 </a></li>
            <li><a href="%url/card:page_266429%">見積依頼詳細</a></li>
            <li><span>見積商品追加</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '見積商品追加',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                        'SMPFORM'=> '%smpform:330_reqItemsReg%',
                        'requestId' => '%val:usr:requestId%',
                        'tenantId' => '%val:usr:tenantId%',
                        'distributorId' => '%val:usr:distributorId%',
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
                'title'     => 'JoyPla 見積商品追加',
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
$QuoteSlipSlipController = new QuoteSlipSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "regQuoteItem")
    {
        echo $QuoteSlipSlipController->regQuoteItem()->render();
    }
    else 
    {
        echo $QuoteSlipSlipController->index()->render();
    }
}