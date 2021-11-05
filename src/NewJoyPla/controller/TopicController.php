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

class TopicController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            $api_url = "%url/rel:mpgt:Topic%";
            
            if($user_info->isDistributorUser())
            {
                $api_url = "%url/rel:mpgt:TopicD%";
            }

            $content = $this->view('NewJoyPla/view/TopicList', [
                'api_url' => $api_url
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
                'title'     => 'JoyPla トピック一覧',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function registTopic(): View
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                $distributor = Distributor::Where('distributorId',$user_info->getDistributorId())->get();
                $distributor = $distributor->data->get(0);
                
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
						"SMPFORM" =>"%smpform:topicReg%",
						"hospitalId" =>"%val:usr:hospitalId%",
						"distributorId" =>"%val:usr:distributorId%",
						"topicName" =>"%val:usr:name%",
						"tenantId" =>"%val:usr:tenantId%" ,
						"distributorName" =>$distributor->distributorName,
                        ]
                    ] , false);
            }
            else if($user_info->isHospitalUser())
            {
                
                $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => 'トピック作成 - 卸業者選択',
                    'table' => '<p>トピックを作成したい卸業者を選択してください</p><div class="uk-margin-auto uk-width-4-5@m">%sf:usr:search90:table%</div>',
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
                
            }
            
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
                'title'     => 'JoyPla トピック一覧',
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
$TopicController = new TopicController();
$action = $SPIRAL->getParam('Action');

{
    if($action === 'registTopic')
    {
        echo $TopicController->registTopic()->render();
    } 
    else 
    {
        echo $TopicController->index()->render();
    }
}
