<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\DistributorUser;
use App\Model\Distributor;
use App\Model\HospitalUser;
use App\Model\Price;
use App\Model\PriceView;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class RegistInHospitalItemController extends Controller
{
    public function __construct()
    {
    }
    
    
    public function input(): View
    {
        global $SPIRAL;
        try {
            
            $user_login_id = $SPIRAL->getParam('user_login_id');
            $user_auth_key = $SPIRAL->getParam('user_auth_key');
            $itemId = $SPIRAL->getParam('itemId');
            
            if($user_login_id == '' || $user_auth_key == '' || $itemId == '')
            {
                throw new Exception("ページが存在しません",404);
            }
            
            $user_info = HospitalUser::where('loginId',$user_login_id)->where('authKey',$user_auth_key)->get();
            $user_info = $user_info->data->get(0);
            
            $priceId = $SPIRAL->getParam('priceId');
            
            $price = PriceView::where('requestFlg','1')->where('notUsedFlag','1','!=')->where('itemId',$itemId)->where('hospitalId',$user_info->hospitalId)->get();
            $price = $price->data->all();
            
            $content = $this->view('NewJoyPla/view/Form/RegistInHospitalItem/Input', [
                    'csrf_token' => Csrf::generate(16),
                    'price_data' => $price,
                    'current_price' => $SPIRAL->getParam('priceId'),
                    ] , false);
        
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 院内商品情報追加 - 入力',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function confirm(): View
    {
        global $SPIRAL;
        try {
            
            $user_login_id = $SPIRAL->getParam('user_login_id');
            $user_auth_key = $SPIRAL->getParam('user_auth_key');
            $itemId = $SPIRAL->getParam('itemId');
            $priceId = $SPIRAL->getParam('priceId');
            
            if($user_login_id == '' || $user_auth_key == '' || $itemId == '' || $priceId == '')
            {
                throw new Exception("ページが存在しません",404);
            }
            
            $user_info = HospitalUser::where('loginId',$user_login_id)->where('authKey',$user_auth_key)->get();
            $user_info = $user_info->data->get(0);
            
            
            $price = PriceView::where('requestFlg','1')->where('priceId',$priceId)->where('itemId',$itemId)->where('hospitalId',$user_info->hospitalId)->get();
            $price = $price->data->get(0);
            
            $content = $this->view('NewJoyPla/view/Form/RegistInHospitalItem/Confirm', [
                    'csrf_token' => Csrf::generate(16),
                    'price' => $price,
                    ] , false);
        
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 院内商品情報追加 - 入力',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function thank()
    {
        
    }
}