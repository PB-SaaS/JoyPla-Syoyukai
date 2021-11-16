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
            
            $hospital_id = $SPIRAL->getParam('hospitalId');
            
            $hospital = Hospital::where('hospitalId',$hospital_id)->get(0);
            $hospital = $hospital->data->get(0);
            
            $user_count = HospitalUser::where('hospitalId',$hospital_id)->count();
            
            if($hospital->registerableNum <= $user_count)
            {
                throw new Exception("登録可能人数の上限に達しています",FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $division = Division::where('hospitalId',$hospital_id)->get();
            $division = $division->data->all();
            
            $divisionId = $SPIRAL->getParam('divisionId');
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/RegistUserInfo/Input', [
                    'csrf_token' => Csrf::generate(16),
                    'division' => $division,
                    'current_division' => $divisionId,
                    ] , false)->form_render();
        
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false)->render();
        } finally {
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 病院ユーザー情報登録 - 入力',
                'content'   => $content,
                'head' => $head,
                'style' => $style,
            ],false);
        }
    }
    
    public function confirm(): View
    {
        global $SPIRAL;
        try {
            
            $hospital_id = $SPIRAL->getParam('hospitalId');
            
            $divisionId = $SPIRAL->getParam('divisionId');
            
            $division = Division::where('divisionId' , $divisionId)->where('hospitalId',$hospital_id)->get();
            $division = $division->data->get(0);
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/RegistUserInfo/Confirm', [
                    'divisionName' => $division->divisionName,
                    ] , true)->form_render();
        
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false)->render();
        } finally {
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/Template/FormBase', [
                'title'     => 'JoyPla 病院ユーザー情報登録 - 確認',
                'content'   => $content,
                'head' => $head,
                'style' => $style,
            ],false);
        }
    }
    
    public function thank(): View
    {
    }
    
}