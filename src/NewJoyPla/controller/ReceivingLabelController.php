<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\ReceivingView;
use App\Model\Hospital;

use ApiErrorCode\FactoryApiErrorCode;
use StdClass;
use Exception;

class ReceivingLabelController extends Controller
{
    public function __construct()
    {
    }

    public function index(): View
    {
		global $SPIRAL;
        try{
            $user_info = new UserInfo($SPIRAL);
            
            $receiving_id = $SPIRAL->getParam('receivingId');
            if($receiving_id == "" || $receiving_id == null)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if($user_info->isAdmin())
            {
                $receiving_items = ReceivingView::where('receivingHId', $receiving_id)->where('hospitalId',$user_info->getHospitalId())->get();
                $receiving_items = $receiving_items->data->all();
            }
            else
            {
                $receiving_items = ReceivingView::where('receivingHId', $receiving_id)->where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
                $receiving_items = $receiving_items->data->all();
            }
            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
        
            $content = $this->view('NewJoyPla/view/ReceivingLabel', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'receiving_items' => $receiving_items,
                'hospital_data' => $hospital_data
                ] , false);
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            
            $head   = $this->view('NewJoyPla/view/template/parts/Head', [
                'new' => true
                ] , false);
                
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            $style   = $this->view('NewJoyPla/view/template/parts/LabelsCss', [] , false)->render();
                
            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [] , false)->render();
                
            $script   = $this->view('NewJoyPla/view/template/parts/Script', [] , false)->render();
                
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla カード一覧',
                'script' => $script,
                'style' => $style,
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
	}
}

$ReceivingLabelController = new ReceivingLabelController();
echo $ReceivingLabelController->index()->render();