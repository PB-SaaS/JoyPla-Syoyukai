<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Tenant;
use App\Model\DistributorUser;
use App\Model\DistributorAffiliationView;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class DistributorUserSlipController extends Controller
{
    public function __construct()
    {
    }
    

    public function update(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            if(!$user_info->isAdmin())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $link = "%url/rel:mpgt:DistributorList%";
            $api_url = "%url/card:page_265119%";
            
            if($user_info->isDistributorUser())
            {
                $link = "%url/rel:mpgt:DistributorInfo%";
            }
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpg:top%&path=user">ユーザーメニュー</a></li>
            <li><a href="{$link}">卸業者一覧</a></li>
            <li><a href="javascript:history.back()">卸業者情報詳細</a></li>
            <li><span>卸業者ユーザー招待情報更新</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '卸業者ユーザー招待情報更新',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
            		"SMPFORM" => "%smpform:affiliationUp%",
            		"id" => "%val:sys:id%",
            		"authKey" => "%val:usr:authKey%" ,
            		"OUserPermission" => "%val:usr:OUserPermission:id%",
            		"affiliationId" => "%val:usr:affiliationId%",
            		"loginId" => "%val:usr:loginId%",
            		"name" => "%val:usr:name%",
            		"mailAddress" => "%val:usr:mailAddress%",
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
                'title'     => 'JoyPla 卸業者ユーザー招待情報更新',
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
            
            if(!$user_info->isAdmin())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $card_id = (int)$SPIRAL->getCardId();
            $distributor_user = DistributorAffiliationView::find($card_id)->get();
            $distributor_user = $distributor_user->data->get(0);
            
            if($user_info->isDistributorUser() && $user_info->getLoginId() === $distributor_user->loginId)
            {
                throw new Exception('自分自身を削除することはできません',-1);
            }
            
            $link = "%url/rel:mpgt:DistributorList%";
            $api_url = "%url/card:page_265119%";
            
            if($user_info->isDistributorUser())
            {
                $link = "%url/rel:mpgt:DistributorInfo%";
            }
            
            $breadcrumb = <<<EOM
            <li><a href="%url/rel:mpg:top%">TOP</a></li>
            <li><a href="%url/rel:mpg:top%&path=user">ユーザーメニュー</a></li>
            <li><a href="{$link}">卸業者一覧</a></li>
            <li><a href="javascript:history.back()">卸業者情報詳細</a></li>
            <li><span>卸業者ユーザー招待情報削除</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => '卸業者ユーザー招待情報削除',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
            		"SMPFORM" => "%smpform:affiliationDel%",
            		"id" => "%val:sys:id%",
            		"authKey" => "%val:usr:authKey%" ,
            		"OUserPermission" => "%val:usr:OUserPermission:id%",
            		"affiliationId" => "%val:usr:affiliationId%",
            		"loginId" => "%val:usr:loginId%",
            		"name" => "%val:usr:name%",
            		"mailAddress" => "%val:usr:mailAddress%",
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
                'title'     => 'JoyPla 卸業者ユーザー招待情報削除',
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
$DistributorUserSlipController = new DistributorUserSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'delete' )
    {
        echo $DistributorUserSlipController->delete()->render();
    } 
    else 
    {
        echo $DistributorUserSlipController->update()->render();
    }
}
