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
use App\Model\Distributor;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class TopicCreateController extends Controller
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
            <li><span>トピック作成</span></li>
EOM;
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'breadcrumb' => $breadcrumb,
                'title' => 'トピック作成',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                		"SMPFORM" => "%smpform:topicReg%",
                		"hospitalId" => "%val:@usr:hospitalId%",
                		"distributorId" => "%val:usr:distributorId%",
                		"topicName" => "%val:@usr:name%",
                		"distributorName" => "%val:usr:distributorName%",
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
                'title'     => 'JoyPla トピック作成',
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
$TopicCreateController = new TopicCreateController();
$action = $SPIRAL->getParam('Action');

{
    echo $TopicCreateController->index()->render();
}
