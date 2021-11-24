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

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class AgreeFormForDistributorController extends Controller
{
    public function __construct()
    {
    }
    
    public function input(): View
    {
        global $SPIRAL;
        try {
            $agreementText = $this->view('NewJoyPla/src/AgreementText', [
                    ] , false)->render();
            
            $content = $this->view('NewJoyPla/view/Form/AgreeForm/Input', [
                    'csrf_token' => Csrf::generate(16),
                    'agreementText' => $agreementText
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
                'title'     => 'JoyPla 利用規約同意 - 入力',
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
            
            $form_content = <<<EOM
            <script>parent.location.href="%url/rel:mpgt:oroshiTopPage%"</script>
EOM;

            $content = $this->view('NewJoyPla/view/template/FormDesign', [
                    'form_content' =>$form_content,
                    'csrf_token' => Csrf::generate(16),
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
                'title'     => 'JoyPla 利用規約同意 - 完了',
                'content'   => $content->form_render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
}	