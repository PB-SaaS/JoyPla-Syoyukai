<?php

namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class PriceSlipController extends Controller
{
    public function __construct()
    {
    }

    public function index(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser()) {
                $breadcrumb = <<<EOM
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="%url/rel:mpg:top%&page=page1">商品・見積</a></li>
                <li><a href="%url/rel:mpgt:ProductQuotation%&Action=PriceList&table_cache=true">金額情報一覧</a></li>
                <li><span>金額情報変更</span></li>
EOM;
                $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                    'title' => '金額情報変更',
                    'breadcrumb' => $breadcrumb,
                    'width' => '100%',
                    'height'=> '100%',
                    'url' => '/regist/is',
                    'hiddens' => [
                            "SMPFORM" => "%smpform:dis_priceUpdate%",
                            "notUsedFlag" => "%val:usr:notUsedFlag:v%",
                            "distributorMCode" => "%val:usr:distributorMCode%",
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
                            "quantityUnit" => "%val:usr:quantityUnit%",
                            "itemUnit" => "%val:usr:itemUnit%",
                            "notice" => "%val:usr:notice%",
                        ]
                    ], false);
            } else {
                throw new Exception("権限がありません", 404);
            }
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
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
            ], false);
        }
    }
}

$PriceSlipController = new PriceSlipController();

$action = $SPIRAL->getParam('Action');

{
    echo $PriceSlipController->index()->render();
}
