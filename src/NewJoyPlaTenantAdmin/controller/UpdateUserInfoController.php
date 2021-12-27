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

class UpdateUserInfoController extends Controller
{
    public function __construct()
    {
    }
    
    
    public function input(): View
    {
        global $SPIRAL;
        try {
            
            $hospital_id = $SPIRAL->getParam('hospitalId');
            
            $division = Division::where('hospitalId',$hospital_id)->get();
            $division = $division->data->all();
            
            $divisionId = $SPIRAL->getParam('divisionId');
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/UpdateUserInfo/Input', [
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
                'title'     => 'JoyPla 病院ユーザー情報変更 - 入力',
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
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/UpdateUserInfo/Confirm', [
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
                'title'     => 'JoyPla 病院ユーザー情報変更 - 確認',
                'content'   => $content,
                'head' => $head,
                'style' => $style,
            ],false);
        }
    }
    
    public function thank()
    {
    }
}