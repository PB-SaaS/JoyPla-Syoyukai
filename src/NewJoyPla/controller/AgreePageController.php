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

class AgreePageController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'title' => '利用規約同意',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                        'loginId' => '%val:usr:loginId%',
                        'SMPFORM'=> '%smpform:termsAgreement%',
                        'authKey' => '%val:usr:authKey%',
                        'autoloadCheckSkip' => true,
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
                'title'     => 'JoyPla 利用規約同意',
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
$AgreePageController = new AgreePageController();

$action = $SPIRAL->getParam('Action');

{
    echo $AgreePageController->index()->render();
}
