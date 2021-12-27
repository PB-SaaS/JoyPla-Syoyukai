<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;

use App\Model\HospitalUser;
use App\Model\Division;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class UpdateAdminUserInfoController extends Controller
{
    public function __construct()
    {
    }
    
    
    public function input(): View
    {
        global $SPIRAL;
        try {
            
            $user_id = $SPIRAL->getParam('user_id');
            $user_auth_key = $SPIRAL->getParam('user_auth_key');
            
            if($user_id == '' || $user_auth_key == '')
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $user_info = HospitalUser::find((int)$user_id)->where('authKey',$user_auth_key)->get();
            $user_info = $user_info->data->get(0);
            
            $division = Division::where('hospitalId', $user_info->hospitalId)->whereDeleted()->get();
            $division = $division->data->all();
            
            $divisionId = $SPIRAL->getParam('divisionId');
            
            $content = $this->view('NewJoyPla/view/Form/ChangeAdminUserInfo/Input', [
                    'csrf_token' => Csrf::generate(16),
                    'division_data' => $division,
                    'current_division' => $divisionId,
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
                'title'     => 'JoyPla 病院ユーザー情報変更 - 入力',
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
            
            $user_id = $SPIRAL->getParam('user_id');
            $user_auth_key = $SPIRAL->getParam('user_auth_key');
            
            if($user_id == '' || $user_auth_key == '')
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $user_info = HospitalUser::find((int)$user_id)->where('authKey',$user_auth_key)->get();
            $user_info = $user_info->data->get(0);
            
            $divisionId = $SPIRAL->getParam('divisionId');
            
            $division = Division::where('divisionId' , $divisionId)->whereDeleted()->where('hospitalId', $user_info->hospitalId)->get();
            $division = $division->data->get(0);
            
            $content = $this->view('NewJoyPla/view/Form/ChangeAdminUserInfo/Confirm', [
                    'divisionName' => $division->divisionName,
                    ] , true);
        
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 病院ユーザー情報変更 - 確認',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function thank(): View
    {
        global $SPIRAL;
        try {
            
            $content = $this->view('NewJoyPla/view/Form/UpdateInHospitalItem/Thank', [
                    'csrf_token' => Csrf::generate(16),
                    //'price' => $price,
                    //'ticket' => $ticket,
                    'oldPrice' => $SPIRAL->getParam('oldPrice') ,
                    'oldUnitPrice' => $SPIRAL->getParam('oldUnitPrice'),
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
                'title'     => 'JoyPla 院内商品情報変更 - 完了',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
        

    }
    
    private function random($length = 8) {
        $str_list = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $str_list_length = count($str_list);
        $str = "";
        for($i = 0; $i < $length; ++$i) {
            $str .= $str_list[random_int(0, $str_list_length - 1)];
        }
        return $str;
    }
}