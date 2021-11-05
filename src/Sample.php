//コントローラーの書き方


namespace App\Controller; //コントローラーのnamespace を記載

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

//各種必要なライブラリを設定

use App\Model\XXXX
//必要なモデルを設定

class XXXXXXController extends Controller
{
    
    public function __construct()
    {}
    
    public function index(): View
    {}
    
    public function input(): View
    {}
    
    public function confirm(): View
    {}
    
    public function thank(): View
    {}
    
    public function iframe(): View
    {
        global $SPIRAL;
        //iframe でＳＰＩＲＡＬフォームを呼び出す
        try{
            $user_info = new UserInfo($SPIRAL);
            
            $content = $this->view('NewJoyPla/view/template/parts/IframeContent', [
                'title' => 'XXXXXX',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                //hiddenに値を設定する
                        'SMPFORM'=> '%smpform:xxxxxx%',
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
                'title'     => 'JoyPla xxxx',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
}

