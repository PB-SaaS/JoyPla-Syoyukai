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

class InHPItemDetailController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);

            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            $cardId = $SPIRAL->getCardId();
            if($cardId == null)
            {
                throw new Exception("ページが存在しません",404);
            }

            $content = $this->view('NewJoyPla/view/InHospitalProductsDetail', [
                'userInfo' => $user_info,
                'csrf_token' => Csrf::generate(16),
            ] , false);

        } catch ( Exception $ex ) {
            $title = 'JoyPla エラー';
            $header = $this->view('NewJoyPla/src/Header', [], false);
            
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false);

        } finally {
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 院内商品情報詳細',
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
$InHPItemDetailController = new InHPItemDetailController();

$action = $SPIRAL->getParam('Action');

{
    echo $InHPItemDetailController->index()->render();
}