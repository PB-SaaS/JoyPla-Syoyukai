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
use App\Model\QuoteRequest;
use App\Model\Price;
use App\Model\QuoteItem;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class RegistItemPriceController extends Controller
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
            
            $user_info = HospitalUser::where('loginId',$user_login_id)->where('authKey',$user_auth_key)->get();
            $user_info = $user_info->data->get(0);
            
            $distributor = Distributor::where('hospitalId',$user_info->hospitalId)->get();
            $distributor = $distributor->data->all();
            
            $makerName = $SPIRAL->getParam('makerName');
            $itemName = $SPIRAL->getParam('itemName');
            $itemCode = $SPIRAL->getParam('itemCode');
            $itemStandard = $SPIRAL->getParam('itemStandard');
            $itemJANCode = $SPIRAL->getParam('itemJANCode');
            
            $currentDistributorId = $SPIRAL->getParam('distributorId');
            
            $content = $this->view('NewJoyPla/view/Form/RegistItemPrice/Input', [
                    'csrf_token' => Csrf::generate(16),
                    'SPIRAL' => $SPIRAL,
                    'currentDistributorId' => $currentDistributorId,
                    'distributor' => $distributor,
                    'makerName' => $makerName,
                    'itemName' => $itemName,
                    'itemCode' => $itemCode,
                    'itemStandard' => $itemStandard,
                    'itemJANCode' => $itemJANCode,
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
                'title'     => 'JoyPla 金額情報登録 - 入力',
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
            
            $distributorId = $SPIRAL->getParam('distributorId');
            
            
            $makerName = $SPIRAL->getParam('makerName');
            $itemName = $SPIRAL->getParam('itemName');
            $itemCode = $SPIRAL->getParam('itemCode');
            $itemStandard = $SPIRAL->getParam('itemStandard');
            $itemJANCode = $SPIRAL->getParam('itemJANCode');
            
            
            $user_info = HospitalUser::where('loginId',$user_login_id)->where('authKey',$user_auth_key)->get();
            $user_info = $user_info->data->get(0);
            
            $distributor = Distributor::where('hospitalId',$user_info->hospitalId)->where('distributorId',$distributorId)->get();
            $distributor = $distributor->data->get(0);
            
            $content = $this->view('NewJoyPla/view/Form/RegistItemPrice/Confirm', [
                    'distributorName' => $distributor->distributorName,
                    'csrf_token' => Csrf::generate(16),
                    'makerName' => $makerName,
                    'itemName' => $itemName,
                    'itemCode' => $itemCode,
                    'itemStandard' => $itemStandard,
                    'itemJANCode' => $itemJANCode,
                    'SPIRAL' => $SPIRAL,
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
                'title'     => 'JoyPla 金額情報登録 - 確認',
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