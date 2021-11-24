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
use App\Model\InHospitalItem;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class UpdateItemFormController extends Controller 
{

    public function __construct()
    {
    }
    
    public function input(): View
    {
        global $SPIRAL;
        try {
            $content = $this->view('NewJoyPla/view/Form/ItemChange/Input', [
                    'csrf_token' => Csrf::generate(16),
                    'top_page_link' => $SPIRAL->getParam('topPageLink'),
                    'list_link' => $SPIRAL->getParam('list_link'),
                    'slip_link' => $SPIRAL->getParam('slip_link'),
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
                'title'     => 'JoyPla 商品情報変更 - 入力',
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
            $content = $this->view('NewJoyPla/view/Form/ItemChange/Confirm', [
                    'csrf_token' => Csrf::generate(16),
                    'top_page_link' => $SPIRAL->getParam('topPageLink'),
                    'list_link' => $SPIRAL->getParam('list_link'),
                    'slip_link' => $SPIRAL->getParam('slip_link'),
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
                'title'     => 'JoyPla 商品情報変更 - 確認',
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
            $itemId = $SPIRAL->getContextByFieldTitle("itemId");
            
            InHospitalItem::where("itemId",$itemId)->update(['itemId'=>$itemId]);
                
            $form_content = <<<EOM
            <h1>商品情報変更 - 完了</h1>
            <div class="smp_tmpl uk-text-left">
                <div class="sub_text">
                    商品情報変更が完了しました。	
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
                'title'     => 'JoyPla 商品情報変更 - 完了',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
}