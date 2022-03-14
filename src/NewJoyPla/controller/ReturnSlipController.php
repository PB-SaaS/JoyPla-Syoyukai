<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\ReturnHistory;
use App\Model\ReturnItemView;
use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class ReturnSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $link_title = "返品一覧";
        	$link = '%url/rel:mpgt:Return%&Action=returnList';
            if($user_info->isUser()){
                if (preg_match("/Action=returnListForDivision/", $_SERVER['HTTP_REFERER'])) {
                    $box = parse_url($_SERVER['HTTP_REFERER']);
            	    $link = $box['path']."?".$box['query'];
                }
            }
            
            $record_id = (int)$SPIRAL->getCardId();
            
            $return_history = ReturnHistory::where('hospitalId',$user_info->getHospitalId())->find($record_id)->get();
            $return_history = $return_history->data->get(0);
            
            $return_items = ReturnItemView::where('hospitalId',$user_info->getHospitalId())->where('returnHistoryID',$return_history->returnHistoryID)->get();
            $return_items = $return_items->data->all();
            
            $content = $this->view('NewJoyPla/view/ReturnSlip', [
                'title' => '返品伝票',
                'link' => $link,
                'return_items' => $return_items,
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
                'title'     => 'JoyPla 返品伝票',
                'script' => '',
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
$ReturnSlipController = new ReturnSlipController();

$action = $SPIRAL->getParam('Action');

{
    echo $ReturnSlipController->index()->render();
}

