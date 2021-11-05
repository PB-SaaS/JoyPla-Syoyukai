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

class SystemNotificationListController extends Controller
{
    public function __construct()
    {
    }

    public function index(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);
            
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => 'システム通知一覧',
                    'table' => '<div class="uk-width-1-2@m uk-margin-auto">%sf:usr:search91:table%</div>',
                    'csrf_token' => Csrf::generate(16)
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
                'title'     => 'JoyPla システム通知一覧',
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
$SystemNotificationListController = new SystemNotificationListController();
$action = $SPIRAL->getParam('Action');

{
    echo $SystemNotificationListController->index()->render();
}
