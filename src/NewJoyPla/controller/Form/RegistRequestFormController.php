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
use App\Model\DistributorAffiliationView;
use App\Model\TenantMaster;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class RegistRequestFormController extends Controller
{
    public function __construct()
    {
    }
    
    public function input(): View
    {
        global $SPIRAL;
        try {
            $current_distributor_Id = $SPIRAL->getParam('distributorId');
            
            $user_id = $SPIRAL->getParam('user_id');
            $user_auth_key = $SPIRAL->getParam('user_auth_key');
            
            $user_info = HospitalUser::where('authKey',$user_auth_key)->find((int)$user_id)->get();
            
            if($user_info->count == 0)
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            $user_info = $user_info->data->get(0);
            
            $distributor_data = Distributor::where('hospitalId',$user_info->hospitalId)->where('vendorFlag','1')->get();
            $distributor_data = $distributor_data->data->all();
            
            $content = $this->view('NewJoyPla/view/Form/QuoteRequest/Input', [
                    'current_distributor_Id' => $current_distributor_Id,
                    'csrf_token' => Csrf::generate(16),
                    'top_page_link' => $SPIRAL->getParam('topPageLink'),
                    'distributors' => $distributor_data,
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
                'title'     => 'JoyPla 見積依頼 - 入力',
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
            $current_distributor_Id = $SPIRAL->getParam('distributorId');
            
            $user_id = $SPIRAL->getParam('user_id');
            $user_auth_key = $SPIRAL->getParam('user_auth_key');
            
            $user_info = HospitalUser::where('authKey',$user_auth_key)->find((int)$user_id)->get();
            
            if($user_info->count == 0)
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $user_info = $user_info->data->get(0);
            
            $distributor_data = Distributor::where('hospitalId',$user_info->hospitalId)->where('distributorId',$current_distributor_Id)->get();
            $distributor_data = $distributor_data->data->get(0);
            
            $content = $this->view('NewJoyPla/view/Form/QuoteRequest/Confirm', [
                    'distributorName' => $distributor_data->distributorName,
                    'csrf_token' => Csrf::generate(16),
                    'top_page_link' => $SPIRAL->getParam('topPageLink'),
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
                'title'     => 'JoyPla 見積依頼 - 確認',
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
            
			$requestTitle = $SPIRAL->getContextByFieldTitle("requestTitle");
			$requestUName = $SPIRAL->getContextByFieldTitle("requestUName");
			$distributorId = $SPIRAL->getContextByFieldTitle("distributorId");
            
            $mail_body = $this->view('NewJoyPla/view/Mail/RegistRequestForm', [
                'name' => '%val:usr:name%',
                'request_title' => $requestTitle,
                'request_Name' => $requestUName,
                'url' => OROSHI_LOGIN_URL,
            ] , false)->render();
            
            $select_name = $this->makeId($distributorId);

            $test = DistributorAffiliationView::selectName($select_name)->rule([
                'name'=>'distributorId',
                'label'=>'name_'.$distributorId,
                'value1'=>$distributorId,
                'condition'=>'matches'
            ])
            ->rule([
                'name'=>'invitingAgree',
                'label'=>'invitingAgree',
                'value1'=>'t',
                'condition'=>'is_boolean'
            ])->filterCreate();

            $test = DistributorAffiliationView::selectRule($select_name)
                ->body($mail_body)
                ->subject("[JoyPla] ".$requestUName."さんが見積依頼「".$requestTitle."」を作成しました")
                ->from(FROM_ADDRESS,FROM_NAME)
                ->send();
             
             
			$tenantId = $SPIRAL->getContextByFieldTitle("tenantId");
            
            $mail_body = $this->view('NewJoyPla/view/Mail/RegistRequestForm', [
                'name' => '%val:usr:name%',
                'request_title' => $requestTitle,
                'request_Name' => $requestUName,
                'url' => TENANT_ADMIN_LOGIN_URL,
            ] , false)->render();
            
            $select_name = $this->makeId($tenantId);

            $test = TenantMaster::selectName($select_name)->rule([
                'name'=>'tenantId',
                'label'=>'name_'.$tenantId,
                'value1'=>$tenantId,
                'condition'=>'matches'
            ])->filterCreate();

            $test = TenantMaster::selectRule($select_name)
                ->body($mail_body)
                ->subject("[JoyPla] ".$requestUName."さんが見積依頼「".$requestTitle."」を作成しました")
                ->from(FROM_ADDRESS,FROM_NAME)
                ->send();
                    
                
            $breadcrumb = <<<EOM
		    <li><a target="_parent" href="%url/rel:mpg:top%">TOP</a></li>
		    <li><span>入力</span></li>
		    <li><span>確認</span></li>
		    <li><span>完了</span></li>
EOM;
                
            $form_content = <<<EOM
            <h1>見積依頼 - 完了</h1>
            <div class="smp_tmpl uk-text-left">
                <div class="sub_text">
                    見積依頼が完了しました。
                </div>
            </div>
EOM;
                
            $content = $this->view('NewJoyPla/view/template/FormDesign', [
                    'form_content' =>$form_content,
                    'csrf_token' => Csrf::generate(16),
                    'breadcrumb' => $breadcrumb,
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
                'title'     => 'JoyPla 見積依頼 - 完了',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
}