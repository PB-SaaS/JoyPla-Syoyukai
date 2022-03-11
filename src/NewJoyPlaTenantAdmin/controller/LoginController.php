<?php
namespace App\Controller;

use Controller;
use Csrf;

use stdClass;
use Exception;

class LoginController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $error = $SPIRAL->getParam('errorMsg');
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Login/Index', [
                'error' => $error,
                ] , false)->render();
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
        } finally {
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla ログイン',
                'content'   => $content,
                'head' => $head,
                'style' => $style,
            ],false);
        }
    }
}

/***
 * 実行
 */
$LoginController = new LoginController();

$action = $SPIRAL->getParam('Action');

{
    {
        echo $LoginController->index()->render();
    }
}