<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;

use App\Model\HospitalUser;
use App\Model\Hospital;
use App\Model\Division;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class RegistUserInfoController extends Controller
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
            
            $hospital = Hospital::where('hospitalId',$user_info->hospitalId)->get(0);
            $hospital = $hospital->data->get(0);
            
            $user_count = HospitalUser::where('hospitalId',$user_info->hospitalId)->count();
            
            if($hospital->registerableNum <= $user_count)
            {
                throw new Exception("登録可能人数の上限に達しています",FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $division = Division::where('hospitalId',$user_info->hospitalId)->get();
            $division = $division->data->all();
            
            $divisionId = $SPIRAL->getParam('divisionId');
            
            $content = $this->view('NewJoyPla/view/Form/RegistUserInfo/Input', [
                    'csrf_token' => Csrf::generate(16),
                    'division' => $division,
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
                'title'     => 'JoyPla 病院ユーザー情報登録 - 入力',
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
            
            $division = Division::where('divisionId' , $divisionId)->where('hospitalId', $user_info->hospitalId)->get();
            $division = $division->data->get(0);
            
            $content = $this->view('NewJoyPla/view/Form/RegistUserInfo/Confirm', [
                    'divisionName' => \App\Lib\html($division->divisionName),
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
                'title'     => 'JoyPla 病院ユーザー情報登録 - 確認',
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