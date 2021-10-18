<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\InHospitalItemView;
use App\Model\Hospital;

use ApiErrorCode\FactoryApiErrorCode;
use StdClass;
use Exception;

class InHospitalItemsListForReferenceController extends Controller
{
    public function __construct()
    {
    }

    public function index(): View
    {
		global $SPIRAL;
        try{
            $user_info = new UserInfo($SPIRAL);
            $keyword = "%sf:usr:innnaizyouhouv3%";
            $api_url = "%url/rel:mpgt:page_175973%";
            $content = $this->view('NewJoyPla/view/InHospitalItemsListForReference', [
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'page_title' => '院内商品情報一覧',
                'keyword' => $keyword,
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
                
            $header = $this->view('NewJoyPla/view/template/parts/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            $style   = $this->view('NewJoyPla/view/template/parts/LabelsCss', [] , false)->render();
                
            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [] , false)->render();
                
            $script   = $this->view('NewJoyPla/view/template/parts/Script', [] , false)->render();
                
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 院内商品情報一覧',
                'script' => $script,
                'style' => $style,
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
	}
	
	public function searchApi()
	{
		global $SPIRAL;

        try {
			$user_info = new UserInfo($SPIRAL);
	
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

			$in_hospital_item_id = $SPIRAL->getParam('inHospitalItemId');
			if($in_hospital_item_id == "" || $in_hospital_item_id == null)
			{
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
			}
			
			$in_hospital_item = InHospitalItemView::where('notUsedFlag','0')->where('inHospitalItemId',$in_hospital_item_id)->where('hospitalId', $user_info->getHospitalId())->get();	
			
			if($in_hospital_item->count == '0'){
				throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
			}
			
			$result = $in_hospital_item->data->get(0);
		
			
			$data = [
				"maker" => $result->makerName,
				"shouhinName" => $result->itemName,
				"code" => $result->itemCode,
				"kikaku" => $result->itemStandard,
				"irisu" => $result->quantity,
				"kakaku" => $result->price,
				"jan" => $result->itemJANCode,
				"oroshi" => $result->distributorName,
				"recordId" => $result->inHospitalItemId,
				"unit" => $result->quantityUnit,
				"itemUnit" => $result->itemUnit,
				"distributorId" => $result->distributorId,
				"catalogNo" => $result->catalogNo,
				"labelId" => $result->labelId,
				"unitPrice" => $result->unitPrice,
				"lotFlag" => ($result->lotManagement == 1 )? "はい": "",
				"lotFlagBool" => $result->lotManagement,
			];
	
			$content = new ApiResponse($data , $in_hospital_item->count , $in_hospital_item->code, $in_hospital_item->message, ['searchApi']);
			$content = $content->toJson();
			
		} catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['searchApi']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
	}
}

$InHospitalItemsListForReferenceController = new InHospitalItemsListForReferenceController();


$action = $SPIRAL->getParam('Action');

{
    if($action === 'searchApi')
    {
        echo $InHospitalItemsListForReferenceController->searchApi()->render();
    } 
    else 
    {
        echo $InHospitalItemsListForReferenceController->index()->render();
    }
}