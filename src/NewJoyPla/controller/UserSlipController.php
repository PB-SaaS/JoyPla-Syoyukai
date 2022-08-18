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

class UserSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpg:top%&path=user">ユーザーメニュー</a></li>
            <li><a href="%url/rel:mpgt:userManagement%">ユーザー一覧</a></li>
            <li><span>ユーザー情報変更</span></li>
EOM;
            $hidden = [
        		"divisionId" => "%val:usr:divisionId%",
        		"userPermission" => "%val:usr:userPermission:id%",
        		"loginId" => "%val:usr:loginId%",
        		"name" => "%val:usr:name%",
        		"nameKana" => "%val:usr:nameKana%",
        		"mailAddress" => "%val:usr:mailAddress%",
        		"remarks" => "%val:usr:remarks%",
        		"SMPFORM" => "%smpform:hpUserChange%",
        		"id" => "%val:sys:id%",
        		"authKey" => "%val:usr:authKey%",
                "user_id" => "%val:@sys:id%",
                "user_auth_key" => "%val:@usr:authKey%",
                ];
                
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => 'ユーザー情報変更',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => $hidden
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
                'title'     => 'JoyPla ユーザー情報変更',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function delete(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            $card_id = (int)$SPIRAL->getCardId();
            $hospital_user = HospitalUser::find($card_id)->get();
            $hospital_user = $hospital_user->data->get(0);
            
            
            if($user_info->getLoginId() === $hospital_user->loginId)
            {
                throw new Exception('自分自身を削除することはできません',-1);
            }
		
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpg:top%&path=user">ユーザーメニュー</a></li>
            <li><a href="%url/rel:mpgt:userManagement%">ユーザー一覧</a></li>
            <li><span>ユーザー情報削除</span></li>
EOM;
            $hidden = [
        		"divisionId" => "%val:usr:divisionId%",
        		"userPermission" => "%val:usr:userPermission:id%",
        		"loginId" => "%val:usr:loginId%",
        		"name" => "%val:usr:name%",
        		"nameKana" => "%val:usr:nameKana%",
        		"mailAddress" => "%val:usr:mailAddress%",
        		"remarks" => "%val:usr:remarks%",
        		"SMPFORM" => "%smpform:HpUserDelete%",
        		"authKey" => "%val:usr:authKey%",
                "user_id" => "%val:@sys:id%",
                "user_auth_key" => "%val:@usr:authKey%",
                ];
                
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => 'ユーザー情報削除',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => $hidden
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
                'title'     => 'JoyPla ユーザー情報削除',
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
$UserSlipController = new UserSlipController();
$action = $SPIRAL->getParam('Action');

{
    if($action === 'delete')
    {
        echo $UserSlipController->delete()->render();
    }
    else
    {
        echo $UserSlipController->index()->render();
    }
}
