<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class SystemNotificationSlipController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        global $SPIRAL;
        try {
            $content = $this->view('NewJoyPlaTenantAdmin/view/SystemNotificationList/Slip', [] , false)->render();
                   
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false)->render();
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n8' => 'uk-active uk-open',
                'n8_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => "%url/rel:mpgt:TopPage%&Action=systemNotification&table_cache=true",
                'back_text' => 'システム通知',
                'title'     => 'JoyPla-Tenant-Master システム通知詳細',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
}

/***
 * 実行
 */
$SystemNotificationSlipController = new SystemNotificationSlipController();
$action = $SPIRAL->getParam('Action');

{
    echo $SystemNotificationSlipController->index()->render();
}
