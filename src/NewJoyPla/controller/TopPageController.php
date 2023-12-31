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

            $content = $this->view('NewJoyPla/view/HPTop', [
                'hospital' => $hospital,
                'tenant' => $tenant,
                'user_info' => $user_info,
                'MyPageID' => $this->sanitize($SPIRAL->getParam('MyPageID')),
                'page' => $this->sanitize($SPIRAL->getParam('page')),
                'url' => '%url/rel:mpgt:page_262241%'
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

    public function option(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            $hospital = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);

            $tenant = Tenant::where('tenantId', $hospital->tenantId)->get();
            $tenant = $tenant->data->get(0);

            $content = $this->view('NewJoyPla/view/Option', [
                'hospital' => $hospital,
                'tenant' => $tenant,
                'user_info' => $user_info,
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


    public function contractConfirm(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            if (!$user_info->isAdmin()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $hospital = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);

            $content = $this->view('NewJoyPla/view/ContractConfirm', [
                'hospital' => $hospital,
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
            <li><a href="%url/rel:mpg:top%&path=user">ユーザーメニュー</a></li>
            <li><span>ユーザー情報変更</span></li>
EOM;

            $hidden = [
                "oldLoginId" => "%val:usr:loginId%",
                "loginId" => "%val:usr:loginId%",
                "name" => "%val:usr:name%",
                "nameKana" => "%val:usr:nameKana%",
                "mailAddress" => "%val:usr:mailAddress%",
                "remarks" => "%val:usr:remarks%",
                "SMPFORM" => "%smpform:hpUserSelfChange%"
                ];

            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => 'ユーザー情報変更',
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
                'title'     => 'JoyPla ユーザー情報変更',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    public function quoteRequest(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpg:top%&path=estimate">見積メニュー</a></li>
            <li><span>見積依頼</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '見積依頼',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                        'tenantId' => '%val:usr:tenantId%',
                        'SMPFORM'=> '%smpform:310_quoteReques%',
                        'hospitalId' => '%val:usr:hospitalId%',
                        'user_id' => '%val:sys:id%',
                        'user_auth_key' => '%val:usr:authKey%',
                        'requestUName' => '%val:usr:name%',
                        'mail' => '%val:usr:mailAddress%',
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
                'title'     => 'JoyPla 見積依頼',
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

    public function itemReg(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            if (!$user_info->isAdmin()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpg:top%&path=product">商品メニュー</a></li>
            <li><span>商品登録</span></li>
EOM;
            $hidden = [
                "SMPFORM" => "%smpform:330_itemReg%",
                "tenantId" => "%val:usr:tenantId%",
                "hospitalId" => "%val:usr:hospitalId%",
                ];

            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '商品登録',
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
                'title'     => 'JoyPla 商品登録',
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
$TopPageController = new TopPageController();
$action = $SPIRAL->getParam('Action');

{
    if ($action === 'quoteRequest') {
        echo $TopPageController->quoteRequest()->render();
    } elseif ($action === 'userInfoChange') {
        echo $TopPageController->userInfoChange()->render();
    } elseif ($action === 'option') {
        echo $TopPageController->option()->render();
    } elseif ($action === 'contractConfirm') {
        echo $TopPageController->contractConfirm()->render();
    } elseif ($action === 'contactUs') {
        echo $TopPageController->contactUs()->render();
    } elseif ($action === 'itemReg') {
        echo $TopPageController->itemReg()->render();
    } else {
        echo $TopPageController->index()->render();
    }
}
