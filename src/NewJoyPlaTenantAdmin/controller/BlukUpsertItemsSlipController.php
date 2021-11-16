<?php
namespace App\Controller;

use Controller;
use Csrf;
use ApiResponse;
use App\Lib\Auth;
use App\Model\Tenant;
use App\Model\ItemBulkUpsertTrDB;

use Validate\itemDB;

use stdClass;
use Exception;

class BlukUpsertItemsSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            
            $back_url = $_SERVER['HTTP_REFERER'];
            $back_text = "施設情報詳細";
            $sidemenu = [
                'n3' => 'uk-active uk-open',
                'n3_3' => 'uk-active'];
            if (preg_match("/&Switcher=logs/", $back_url)) {
                $back_text = "商品情報詳細";
                $sidemenu = [
                    'n3' => 'uk-active uk-open',
                    'n3_1' => 'uk-active']; 
            }
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/BlukUpsertItemsLogs/Slip', [
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16)
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu',$sidemenu, false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => $back_url,
                'back_text' => $back_text,
                'title'     => 'JoyPla-Tenant-Master 商品登録更新詳細',
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
$BlukUpsertItemsSlipController = new BlukUpsertItemsSlipController();

$action = $SPIRAL->getParam('Action');

{
    {
        echo $BlukUpsertItemsSlipController->index()->render();
    }
}