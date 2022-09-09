<?php

namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\HospitalUser;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class DivisionSlipController extends Controller
{
    public function __construct()
    {
    }

    public function index(): View
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
            <li><span>部署情報変更</span></li>
EOM;
            $hidden = [
                "divisionId" => "%val:usr:divisionId%",
                "deleteFlag" => "%val:usr:deleteFlag%",
                "divisionName" => "%val:usr:divisionName%",
                "SMPFORM" => "%smpform:divisionChange%",
                "authkey" => "%val:usr:authkey%",
                "user_id" => "%val:@sys:id%",
                "user_auth_key" => "%val:@usr:authKey%",
                "hospitalId" => "%val:usr:hospitalId%",
                "deliveryDestCode" => "%val:usr:deliveryDestCode%"
                ];

            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '部署情報変更',
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
                'title'     => 'JoyPla 部署情報変更',
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
$DivisionSlipController = new DivisionSlipController();
$action = $SPIRAL->getParam('Action');

{
    {
        echo $DivisionSlipController->index()->render();
    }
}
