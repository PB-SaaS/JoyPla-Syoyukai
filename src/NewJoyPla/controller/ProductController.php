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

class ProductController extends Controller
{
    private $in_hospital_items = null ;
    public function __construct()
    {
    }
    
    /**
     * 商品一覧
     */
    public function Item(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = array();

        $user_info = new UserInfo($SPIRAL);

        $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
        $header = $this->view('NewJoyPla/src/HeaderForMypage', [
            'SPIRAL' => $SPIRAL
        ], false);
        $content = $this->view('NewJoyPla/view/ProductMaster', [
            'userInfo' => $user_info,
            'csrf_token' => Csrf::generate(16)
            ] , false);
        
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        return $this->view('NewJoyPla/view/template/Template', [
            'title'     => 'JoyPla 商品マスタ',
            'content'   => $content->render(),
            'head' => $head->render(),
            'header' => $header->render(),
            'baseUrl' => '',
        ],false);
    }

    /**
     * 院内商品一覧
     */
    public function InHospitalItem(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = array();

        $user_info = new UserInfo($SPIRAL);

        $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
        $header = $this->view('NewJoyPla/src/HeaderForMypage', [
            'SPIRAL' => $SPIRAL
        ], false);

        $content = $this->view('NewJoyPla/view/InHospitalProductsMaster', [
            'userInfo' => $user_info,
            'csrf_token' => Csrf::generate(16)
        ] , false);
        
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        return $this->view('NewJoyPla/view/template/Template', [
            'title'     => 'JoyPla 院内商品マスタ',
            'content'   => $content->render(),
            'head' => $head->render(),
            'header' => $header->render(),
            'baseUrl' => '',
        ],false);
    }
}

/***
 * 実行
 */
$ProductController = new ProductController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'Item')
    {
        echo $ProductController->Item()->render();
    } 
    else if($action === 'InHospitalItem')
    {
        echo $ProductController->InHospitalItem()->render();
    }
}
