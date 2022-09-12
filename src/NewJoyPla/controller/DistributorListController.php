<?php

namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Tenant;
use App\Model\Hospital;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class DistributorListController extends Controller
{
    public function __construct()
    {
    }


    public function index(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);
            $isOldTopPage = false;
            if ($user_info->isDistributorUser()) {
                $isOldTopPage = true;
                $hospital = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
                $hospital = $hospital->data->get(0);
                $tenant = Tenant::where('tenantId', $hospital->tenantId)->get();
                $tenant = $tenant->data->get(0);
                $api_url = "%url/rel:mpgt:DistributorInfo%";
            } else {
                $tenant = Tenant::where('tenantId', $user_info->getTenantId())->get();
                $tenant = $tenant->data->get(0);
                $api_url = "%url/rel:mpgt:DistributorList%";
            }

            $content = $this->view('NewJoyPla/view/DistributorList', [
                        'user_info' => $user_info,
                        'tenant' => $tenant,
                        'isOldTopPage' => $isOldTopPage,
                        'api_url' => $api_url,
                    ], false);
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
                'title'     => 'JoyPla 卸業者一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }


    public function regDistributor(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isUser()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }
            if ($user_info->isDistributorUser()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpg:top%&path=user">ユーザーメニュー</a></li>
            <li><span>卸業者登録</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '卸業者登録',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                        "SMPFORM" => "%smpform:distributorReg%",
                        "hospitalId" => "%val:usr:hospitalId%"
                    ]
                ], false);
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
                'title'     => 'JoyPla 卸業者登録',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }
}

/***
 * 実行
 */
$DistributorListController = new DistributorListController();

$action = $SPIRAL->getParam('Action');

{
    if ($action === "regDistributor") {
        echo $DistributorListController->regDistributor()->render();
    } else {
        echo $DistributorListController->index()->render();
    }
}
