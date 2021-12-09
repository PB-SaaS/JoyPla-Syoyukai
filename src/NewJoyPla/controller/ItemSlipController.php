<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Tenant;
use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class ItemSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            $api_url = '%url/card:page_177122%';
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            $cardId = $SPIRAL->getCardId();
            if($cardId == null)
            {
                throw new Exception("ページが存在しません",404);
            }

            $tenantKind = '';
            $tenantId = $user_info->getTenantId();
            if ($tenantId)
            {
                $tenant = Tenant::where('tenantId',$tenantId)->get();
                $tenant = $tenant->data->all()[0];
                $tenantKind = $tenant->tenantKind;
            }

            $content = $this->view('NewJoyPla/view/ProductInformationDetail', [
                'api_url' => $api_url,
                'userInfo' => $user_info,
                'csrf_token' => Csrf::generate(16),
                'tenantKind' => $tenantKind
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
                'title'     => 'JoyPla 商品情報詳細',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function itemChangeForm(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            $api_url = '%url/card:page_177122%';
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpgt:Product%&Action=Item&table_cache=true">商品一覧</a></li>
            <li><a href="%url/card:page_177122%">商品情報詳細</a></li>
            <li><span>商品情報変更</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '商品情報変更',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                        'SMPFORM'=> '%smpform:330_itemChange%',
                        'itemId' => '%val:usr:itemId%',
                        'category' => '%val:usr:category:id%',
                        'itemName' => '%val:usr:itemName%',
                        'itemCode' => '%val:usr:itemCode%',
                        'itemStandard' => '%val:usr:itemStandard%',
                        'itemJANCode' => '%val:usr:itemJANCode%',
                        'officialFlag' => '%val:usr:officialFlag%',
                        'officialpriceOld' => '%val:usr:officialpriceOld%',
                        'officialprice' => '%val:usr:officialprice%',
                        'quantity' => '%val:usr:quantity%',
                        'quantityUnit' => '%val:usr:quantityUnit%',
                        'itemUnit' => '%val:usr:itemUnit%',
                        'tenantId' => '%val:usr:tenantId%',
                        'itemsAuthKey' => '%val:usr:itemsAuthKey%',
                        'makerName' => '%val:usr:makerName%',
                        'itemsAuthKey' => '%val:usr:itemsAuthKey%',
                        'catalogNo' => '%val:usr:catalogNo%',
                        'serialNo' => '%val:usr:serialNo%',
                        'minPrice' => '%val:usr:minPrice%',
                        'lotManagement' => '%val:usr:lotManagement%',
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
                'title'     => 'JoyPla 商品情報変更',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function priceRegist(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            $api_url = '%url/card:page_177122%';
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpgt:Product%&Action=Item&table_cache=true">商品一覧</a></li>
            <li><a href="%url/card:page_177122%">商品情報詳細</a></li>
            <li><span>金額情報登録</span></li>
EOM;
            
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '金額情報登録',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                        "itemName" => "%val:usr:itemName%",
                        "itemCode" => "%val:usr:itemCode%",
                        "itemStandard" => "%val:usr:itemStandard%",
                        "itemJANCode" => "%val:usr:itemJANCode%",
                        "makerName" => "%val:usr:makerName%",
                        "itemId" => "%val:usr:itemId%",
                        "quantity" => "%val:usr:quantity%",
                        "quantityUnit" => "%val:usr:quantityUnit%",
                        "itemUnit" => "%val:usr:itemUnit%",
                        "SMPFORM" => "%smpform:310_priceReg%",
                        "hospitalId" => "%val:@usr:hospitalId%", 
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
                'title'     => 'JoyPla 金額情報登録',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function inHospitalItemRegist(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            $api_url = '%url/card:page_177122%';
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpgt:Product%&Action=Item&table_cache=true">商品一覧</a></li>
            <li><a href="%url/card:page_177122%">商品情報詳細</a></li>
            <li><span>院内商品情報登録</span></li>
EOM;
            
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '院内商品情報登録',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                        "SMPFORM" => "%smpform:330_inHpItemsR%",
                        "itemId" => "%val:usr:itemId%",
                        "itemName" => "%val:usr:itemName%",
                        "itemCode" => "%val:usr:itemCode%",
                        "itemStandard" => "%val:usr:itemStandard%",
                        "itemJANCode" => "%val:usr:itemJANCode%",
                        "quantity" => "%val:usr:quantity%",
                        "quantityUnit" => "%val:usr:quantityUnit%",
                        "itemUnit" => "%val:usr:itemUnit%",
                        "makerName" => "%val:usr:makerName%",
                        "hospitalId" => "%val:@usr:hospitalId%",
                        "catalogNo" => "%val:usr:catalogNo%",
                        "serialNo" => "%val:usr:serialNo%",
                        "user_login_id" => "%val:usr:loginId%",
                        "user_auth_key" => "%val:usr:authKey%",
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
                'title'     => 'JoyPla 院内商品情報登録',
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
$ItemSlipController = new ItemSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'itemChangeForm')
    {
        echo $ItemSlipController->itemChangeForm()->render();
    }
    else if($action === 'priceRegist')
    {
        echo $ItemSlipController->priceRegist()->render();
    } 
    else if($action === 'inHospitalItemRegist')
    {
        echo $ItemSlipController->inHospitalItemRegist()->render();
    } 
    else
    {
        echo $ItemSlipController->index()->render();
    }
}