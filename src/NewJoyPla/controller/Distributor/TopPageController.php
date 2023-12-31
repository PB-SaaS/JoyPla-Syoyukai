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
use App\Model\DistributorAffiliationView;
use App\Model\DistributorUser;


use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class TopPageController extends Controller
{
    public function __construct()
    {
    }

    public function index(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            $hospital = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);

            $tenant = Tenant::where('tenantId', $hospital->tenantId)->get();
            $tenant = $tenant->data->get(0);

            $content = $this->view('NewJoyPla/view/Distributor/Top', [
                'hospital' => $hospital,
                'tenant' => $tenant,
                'user_info' => $user_info,
                'url' => '%url/rel:mpgt:oroshiTopPage%'
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
                'title'     => 'JoyPla TOP',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }


    public function userInfoChange(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpg:top%&page=page8">ユーザー管理</a></li>
            <li><span>卸業者ユーザー変更</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '卸業者ユーザー変更',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                    "oldLoginId" => "%val:usr:loginId%",
                    "loginId" => "%val:usr:loginId%",
                    "name" => "%val:usr:name%",
                    "nameKana" => "%val:usr:nameKana%",
                    "mailAddress" => "%val:usr:mailAddress%",
                    "remarks" => "%val:usr:remarks%",
                    "SMPFORM" => "%smpform:distrUserSelfCh%"
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
                'title'     => 'JoyPla 卸業者ユーザー変更',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    public function contactUs(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><span>お問合せ</span></li>
EOM;
            $hidden = [
                "SMPFORM" => "%smpform:contactUs%"
                ];

            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => 'お問合せ',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => $hidden
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
                'title'     => 'JoyPla お問合せ',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    public function accountSelect(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            $distributorAffiliation = DistributorAffiliationView::where('loginId', $user_info->getLoginId())->where('invitingAgree', '1')->get();
            $distributorAffiliation = $distributorAffiliation->data->all();
            $api_url = "%url/rel:mpg:top%";
            $content = $this->view('NewJoyPla/view/Distributor/AccountSelect', [
                'api_url' => $api_url,
                'affiliation' => $distributorAffiliation,
                    'csrf_token' => Csrf::generate(16),
                'current_affiliation' => $SPIRAL->getContextByFieldTitle('affiliationId')
                ], false);
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            $header = $this->view('NewJoyPla/src/Header', [
                'SPIRAL' => $SPIRAL
            ], false);

            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla アカウント切り替え',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    public function userAffiliationIdUpdate(): View
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            $affiliationId = $SPIRAL->getParam('affiliationId');

            $result = DistributorUser::where('loginId', $user_info->getLoginId())->update(['affiliationId'=> $affiliationId ]);

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['update']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['update']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }
}

/***
 * 実行
 */
$TopPageController = new TopPageController();
$action = $SPIRAL->getParam('Action');

if ($autoload_action != '') {
    $action = $autoload_action; //autoload
}

{
    if ($action === 'userInfoChange') {
        echo $TopPageController->userInfoChange()->render();
    } elseif ($action === 'contactUs') {
        echo $TopPageController->contactUs()->render();
    } elseif ($action === 'accountSelect') {
        echo $TopPageController->accountSelect()->render();
    } elseif ($action === 'userAffiliationIdUpdate') {
        echo $TopPageController->userAffiliationIdUpdate()->render();
    } else {
        echo $TopPageController->index()->render();
    }
}
