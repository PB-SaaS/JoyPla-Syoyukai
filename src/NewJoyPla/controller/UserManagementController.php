<?php

namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\Hospital;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class UserManagementController extends Controller
{
    public function __construct()
    {
    }

    public function index(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            $content = $this->view('NewJoyPla/view/UserManagement', [
                'user_info' => $user_info
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

    public function userRegist(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            $hospital = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);


            if (!$user_info->isAdmin()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpg:top%&path=user">ユーザーメニュー</a></li>
            <li><a href="%url/rel:mpgt:userManagement%">ユーザー一覧</a></li>
            <li><span>ユーザー情報登録</span></li>
EOM;
            $hidden = [
                    "SMPFORM" => "%smpform:hpUserReg%",
                    "hospitalId" => "%val:usr:hospitalId%",
                    "hospitalAuthKey" => $hospital->authKey,
                    "user_id" => "%val:sys:id%",
                    "user_auth_key" => "%val:usr:authKey%",
                ];

            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => 'ユーザー情報登録',
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
                'title'     => 'JoyPla ユーザー情報登録',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }


    public function divisionList(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isUser()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $content = $this->view('NewJoyPla/view/DivisionList', [
                'user_info' => $user_info
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


    public function registDivision(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            if (!$user_info->isAdmin()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpg:top%&path=user">ユーザーメニュー</a></li>
            <li><a href="%url/rel:mpgt:userManagement%&Action=divisionList">部署一覧</a></li>
            <li><span>部署登録</span></li>
EOM;
            $hidden = [
                        "SMPFORM" => "%smpform:divisionReg%",
                        "hospitalId" => "%val:usr:hospitalId%",
                        "divisionType" => "2",
                ];

            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '部署登録',
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
                'title'     => 'JoyPla 部署登録',
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
$UserManagementController = new UserManagementController();
$action = $SPIRAL->getParam('Action');

{
    if ($action === 'userRegist') {
        echo $UserManagementController->userRegist()->render();
    } elseif ($action === 'divisionList') {
        echo $UserManagementController->divisionList()->render();
    } elseif ($action === 'registDivision') {
        echo $UserManagementController->registDivision()->render();
    } else {
        echo $UserManagementController->index()->render();
    }
}
