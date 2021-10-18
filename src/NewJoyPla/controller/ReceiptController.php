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

class ReceiptController extends Controller
{
    private $in_hospital_items = null ;
    public function __construct()
    {
    }
    
    /**
     * 発注書一覧
     */
    public function OrederList(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = array();

        $user_info = new UserInfo($SPIRAL);

        if ($user_info->isDistributorUser())
        {
            throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
        }

        $myPageID = $SPIRAL->getParam('MyPageID');
        if ($user_info->getUserPermission() != '1' && $myPageID != '')
        {
            App\Lib\viewNotPossible();
            exit;
        }

        $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
        $header = $this->view('NewJoyPla/src/HeaderForMypage', [
            'SPIRAL' => $SPIRAL
        ], false);

        $content = $this->view('NewJoyPla/view/PurchaseOrderList', [
            'userInfo' => $user_info,
            'csrf_token' => Csrf::generate(16)
            ] , false);
        
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        return $this->view('NewJoyPla/view/template/Template', [
            'title'     => 'JoyPla 発注書一覧',
            'content'   => $content->render(),
            'head' => $head->render(),
            'header' => $header->render(),
            'baseUrl' => '',
        ],false);
    }

    /**
     * 
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
$ReceiptController = new ReceiptController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'OrederList')
    {
        echo $ReceiptController->OrederList()->render();
    } 
    else if($action === 'InHospitalItem')
    {
        //echo $ReceiptController->InHospitalItem()->render();
    }
}
