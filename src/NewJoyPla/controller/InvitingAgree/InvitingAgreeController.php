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

class InvitingAgreeController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'title' => '招待同意',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                        'SMPFORM'=> '%smpform:NJ_InvitingAgree%',
                        'id' => '%val:sys:id%',
                        'authKey' => '%val:usr:authKey%',
                        'hospitalName' => '%val:usr:hospitalName%',
                        'distributorName' => '%val:usr:distributorName%',
                        'hospitalId' => '%val:usr:hospitalId%',
                        'distributorId' => '%val:usr:distributorId%',
                        'OUserPermission' => '%val:usr:OUserPermission:id%',
                        'affiliationId' => '%val:usr:affiliationId%',
                    ]
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/Header', [
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

$InvitingAgreeController = new InvitingAgreeController();

$action = $SPIRAL->getParam('Action');

{
    echo $InvitingAgreeController->index()->render();
}