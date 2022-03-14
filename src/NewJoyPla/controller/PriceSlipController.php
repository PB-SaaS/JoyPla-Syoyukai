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

class PriceSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index($pattern = 0): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            $tenant = Tenant::where('tenantId', $user_info->getTenantId())->get();
            $tenant = $tenant->data->get(0);
            
            if(($user_info->isAdmin() || $user_info->isApprover()) && $tenant->tenantKind == '1'){
                if($pattern == 0)
                {
                    $breadcrumb = <<<EOM
                    <li><a href="%url/rel:mpg:top%">TOP</a></li>
                    <li><a href="%url/rel:mpgt:Price%&Action=Price&table_cache=true">金額情報一覧</a></li>
                    <li><span>金額情報変更</span></li>
EOM;
                } 
                else 
                {
                    $breadcrumb = <<<EOM
                    <li><a href="%url/rel:mpg:top%">TOP</a></li>
                    <li><a href="%url/rel:mpgt:Product%&Action=Item&table_cache=true">商品マスタ</a></li>
                    <li><a href="javascript:history.back()">商品情報詳細</a></li>
                    <li><span>金額情報変更</span></li>
EOM;
                    
                }
                $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                    'title' => '金額情報変更',
                    'breadcrumb' => $breadcrumb,
                    'width' => '100%',
                    'height'=> '100%',
                    'url' => '/regist/is',
                    'hiddens' => [
                    		"SMPFORM" => "%smpform:310_priceUpdate%",
                    		"itemName" => "%val:usr:itemName%",
                    		"itemCode" => "%val:usr:itemCode%",
                    		"itemStandard" => "%val:usr:itemStandard%",
                    		"itemJANCode" => "%val:usr:itemJANCode%",
                    		"makerName" => "%val:usr:makerName%",
                    		"priceId" => "%val:usr:priceId%",
                    		"authKey" => "%val:usr:authKey%",
                    		"itemId" => "%val:usr:itemId%",
                    		"distributorId" => "%val:usr:distributorId%",
                    		"quantity" => "%val:usr:quantity%",
                    		"price" => "%val:usr:price%",
                    		"hospitalId" => "%val:usr:hospitalId%",
                    		"requestFlg" => "%val:usr:requestFlg:id%",
                    		"quantityUnit" => "%val:usr:quantityUnit%",
                    		"itemUnit" => "%val:usr:itemUnit%",
                    		"notice" => "%val:usr:notice%",
                    		"notUsedFlag" => "%val:usr:notUsedFlag%",
                            "user_login_id" => "%val:@usr:loginId%",
                            "user_auth_key" => "%val:@usr:authKey%",
                        ]
                    ] , false);
            } 
            else
            {
                throw new Exception("権限がありません",404);
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
                'title'     => 'JoyPla 金額情報変更',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
}

$PriceSlipController = new PriceSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action == 'itemSlip')
    {
        echo $PriceSlipController->index(1)->render();
    } 
    else 
    {
        echo $PriceSlipController->index()->render();
    }
}
