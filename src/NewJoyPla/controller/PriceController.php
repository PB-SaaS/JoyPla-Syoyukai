<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Distributor;
use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class PriceController extends Controller
{
    public function __construct()
    {
    }
    
    /**
     * 金額一覧
     */
    public function Price(): View
    {
        global $SPIRAL;
        try {
        
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $distributor_data = Distributor::where('hospitalId',$user_info->getHospitalId())->get();
            $distributor_data = $distributor_data->data->all();
            $distributor = [];
            foreach ($distributor_data as $val)
            {
                $distributor[] = [
                    $val->distributorName,
                    $val->distributorId
                ];
            }

            $content = $this->view('NewJoyPla/view/PriceList', [
                'userInfo' => $user_info,
                'csrf_token' => Csrf::generate(16),
                'distributor' => $distributor
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
                'title'     => 'JoyPla 金額情報一覧',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }

    /**
     * 見積依頼一覧
     */
    public function QuoteList(): View
    {
        global $SPIRAL;
        try {
        
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $user_info = new UserInfo($SPIRAL);

            $content = $this->view('NewJoyPla/view/QuoteOrderList', [
                'userInfo' => $user_info,
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
                'title'     => 'JoyPla 見積依頼一覧',
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
$PriceController = new PriceController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'Price')
    {
        echo $PriceController->Price()->render();
    } 
    else if($action === 'QuoteList')
    {
        echo $PriceController->QuoteList()->render();
    }
}