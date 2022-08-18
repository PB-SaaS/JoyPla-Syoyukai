<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\Stock;
use App\Model\Lot;
use App\Model\OrderHistory;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class NotificationController extends Controller
{
    
    public $links = [
        'unorderedList' => '%url/rel:mpgt:Root%&path=/order/unapproved/show',
        'stock' => '%url/rel:mpgt:Stock%&Action=stockManagementList',
        'lot' => '%url/rel:mpgt:Lots%&Action=lotManagementList'
        ];
    
    public $icons = [
        'unorderedList' => "https://i02.smp.ne.jp/u/joypla/images/menu_icon/1.png",
        'stock' => "https://i02.smp.ne.jp/u/joypla/images/menu_icon/6.png",
        'lot' => "https://i02.smp.ne.jp/u/joypla/images/menu_icon/6.png",
    ];
    
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);
            $notification = [];
            if($user_info->isHospitalUser())
            {
                $instance =  Stock::where('hospitalId',$user_info->getHospitalId())->where('stockQuantity', 0 , '<');
                if($user_info->isUser()){$instance = $instance->where('divisionId',$user_info->getDivisionId());}
                $count = $instance->count();
                if($count > 0)
                {
                    $notification[] = [
                        'message' => '在庫管理表にマイナスとなっている在庫が<br><span class="uk-text-danger uk-text-bold">'.$count.'件</span>存在します',
                        'icon' => $this->icons['stock'],
                        'link' => $this->links['stock'],
                    ];
                }
                
                $instance =  Lot::where('hospitalId',$user_info->getHospitalId())->where('stockQuantity', 0 , '<');
                if($user_info->isUser()){$instance = $instance->where('divisionId',$user_info->getDivisionId());}
                $count = $instance->count();
                if($count > 0)
                {
                    $notification[] = [
                        'message' => 'ロット管理表にマイナスとなっている在庫が<br><span class="uk-text-danger uk-text-bold">'.$count.'件</span>存在します',
                        'icon' => $this->icons['lot'],
                        'link' => $this->links['lot'],
                    ];
                }
                
                $month_3 = date("Y/m/d",strtotime("+3 month"));
                $instance =  Lot::where('hospitalId',$user_info->getHospitalId())->where('lotDate', $month_3 , '<');
                if($user_info->isUser()){$instance = $instance->where('divisionId',$user_info->getDivisionId());}
                $count = $instance->count();
                if($count > 0)
                {
                    $notification[] = [
                        'message' => 'ロット管理表に期限が3カ月以内に切れる商品が<br><span class="uk-text-danger uk-text-bold">'.$count.'件</span>存在します',
                        'icon' => $this->icons['lot'],
                        'link' => $this->links['lot'],
                    ];
                }
                
                $instance =  OrderHistory::where('hospitalId',$user_info->getHospitalId())->where('orderStatus', '1');
                if($user_info->isUser()){$instance = $instance->where('divisionId',$user_info->getDivisionId());}
                $count = $instance->count();
                if($count > 0)
                {
                    $notification[] = [
                        'message' => '未発注伝票が<br><span class="uk-text-danger uk-text-bold">'.$count.'件</span>存在します',
                        'icon' => $this->icons['unorderedList'],
                        'link' => $this->links['unorderedList'],
                    ];
                }
            }
            
            $content = new ApiResponse($notification , count($notification) , 0 , '', ['']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
}

/***
 * 実行
 */
$NotificationController = new NotificationController();

$action = $SPIRAL->getParam('Action');

{
    echo $NotificationController->index()->render();
}
