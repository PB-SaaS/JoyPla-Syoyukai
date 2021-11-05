<?PHP 
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Division;
use App\Model\Hospital;
use App\Model\StockView;
use App\Model\Stock;
use App\Model\Card;
use App\Model\InventoryAdjustmentTransaction;
use App\Model\Distributor;
use App\Model\Order;
use App\Model\OrderHistory;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class AchievementController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {

            $api_url = "%url/rel:mpgt:Achievement%";
            
            $title = "発注履歴詳細一覧";
            
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => $title,
                    'table' => '%sf:usr:search17%',
                    'print' => true,
                    'export' => true,
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
                'title'     => 'JoyPla '.$title,
                'script' => '',
                'before_script' => '<script>$("#search_distributorId").remove();</script>',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }   
}

$AchievementController = new AchievementController();

$action = $SPIRAL->getParam('Action');

{
        echo $AchievementController->index()->render();
}