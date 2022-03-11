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

use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;


/**
 * 発注書
 */
class ProductQuotationController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isHospitalUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:ProductQuotation%";
    
            $content = $this->view('NewJoyPla/view/Distributor/InHospitalItemsList', [
                'csrf_token' => Csrf::generate(16)
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
                'title'     => 'JoyPla 院内商品一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    
    public function quotation()
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isHospitalUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:ProductQuotation%";
    
            $content = $this->view('NewJoyPla/view/template/List', [
                'title' => '見積依頼一覧',
                'table' => '<div class="uk-margin-auto uk-width-4-5@m">%sf:usr:search92%</div>',
                'csrf_token' => Csrf::generate(16)
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
                'title'     => 'JoyPla 見積依頼一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function priceList(): View
    {
        global $SPIRAL;
        try {
        
            $user_info = new UserInfo($SPIRAL);

            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '金額情報一覧',
                    'print' => true,
                    'export' => true,
                    'table' => '%sf:usr:search58%',
                    'userInfo' => $user_info,
                    'csrf_token' => Csrf::generate(16),
                    'distributor' => $distributor,
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
                'title'     => 'JoyPla 金額情報一覧',
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
$ProductQuotationController = new ProductQuotationController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'Quotation')
    {
        echo $ProductQuotationController->quotation()->render();
    }
    else if($action === 'PriceList')
    {
        echo $ProductQuotationController->priceList()->render();
    }
    else 
    {
        echo $ProductQuotationController->index()->render();
    }
}