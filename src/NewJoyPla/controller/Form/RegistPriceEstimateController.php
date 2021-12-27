<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\DistributorUser;
use App\Model\Distributor;
use App\Model\HospitalUser;
use App\Model\QuoteRequest;
use App\Model\Price;
use App\Model\QuoteItem;



use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class RegistPriceEstimateController extends Controller
{
    public function __construct()
    {
    }
    
    public function input(): View
    {
        global $SPIRAL;
        try {
            $content = $this->view('NewJoyPla/view/Form/RegistPriceEstimate/Input', [
                    'csrf_token' => Csrf::generate(16),
                    'top_page_link' => $SPIRAL->getParam('topPageLink'),
                    'list_link' => $SPIRAL->getParam('list_link'),
                    'slip_link' => $SPIRAL->getParam('slip_link'),
                    'SPIRAL' => $SPIRAL,
                    ] , false);
        
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 見積金額登録 - 入力',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function confirm(): View
    {
        global $SPIRAL;
        try {
            $content = $this->view('NewJoyPla/view/Form/RegistPriceEstimate/Confirm', [
                    /*'distributorName' => $distributor_data->distributorName,*/
                    'csrf_token' => Csrf::generate(16),
                    'top_page_link' => $SPIRAL->getParam('topPageLink'),
                    'list_link' => $SPIRAL->getParam('list_link'),
                    'slip_link' => $SPIRAL->getParam('slip_link'),
                    'SPIRAL' => $SPIRAL,
                    ] , false);
        
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 見積金額登録 - 確認',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function thank(): View
    {
        global $SPIRAL;
        try {
            
            $request_id = $SPIRAL->getContextByFieldTitle('requestId');
            
            $quote_request = QuoteRequest::where('requestId',$request_id)->get();
            $quote_request = $quote_request->data->get(0);
            
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
            
                
            $subject = "[JoyPla] 見積金額が登録されました";
            
            $mail_body = $this->view('NewJoyPla/view/Mail/RegistRequestPrice', [
                'name' => '%val:usr:name%',
                'request_title' => $quote_request->requestTitle,
                'request_Name' => $quote_request->requestUName,
                'url' => LOGIN_URL,
            ] , false)->render();
            
            $hospital_user = HospitalUser::getNewInstance();
            
            $select_name = $this->makeId($quote_request->hospitalId);
            $test = $hospital_user::selectName($select_name)
                ->rule(['name'=>'hospitalId','label'=>'name_'.$quote_request->hospitalId,'value1'=>$quote_request->hospitalId,'condition'=>'matches'])
                ->filterCreate();
                
            $test = $hospital_user::selectRule($select_name)
                ->body($mail_body)
                ->subject($subject)
                ->from(FROM_ADDRESS,FROM_NAME)
                ->send();
            
            $breadcrumb = <<<EOM
		    <li><a target="_parent" href="%url/rel:mpg:top%">TOP</a></li>
		    <li><a target="_parent" href="{$SPIRAL->getParam('list_link')}">見積依頼一覧</a></li>
		    <li><a target="_parent" href="{$SPIRAL->getParam('slip_link')}">見積依頼詳細</a></li>
		    <li><span>見積商品追加</span></li>
		    <li><span>入力</span></li>
		    <li><span>確認</span></li>
		    <li><span>完了</span></li>
EOM;
                
            $form_content = <<<EOM
            <h1>見積金額登録 - 完了</h1>
            <div class="smp_tmpl uk-text-left">
                <div class="sub_text">
                    見積金額登録が完了しました。	
                </div>
            </div>
EOM;
                
            $content = $this->view('NewJoyPla/view/template/FormDesign', [
                    'form_content' =>$form_content,
                    'csrf_token' => Csrf::generate(16),
                    'breadcrumb' => $breadcrumb,
                    ] , false);
        
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 見積金額登録 - 完了',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
}