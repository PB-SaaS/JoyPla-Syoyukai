<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\InHospitalItemView;
use App\Model\ReceivingView;
use App\Model\PayoutView;
use App\Model\CardInfoView;
use App\Model\Hospital;
use App\Model\Division;

use ApiErrorCode\FactoryApiErrorCode;
use StdClass;
use Exception;

class LabelBarcodeSearchController extends Controller
{

    public function __construct()
    {
    }
    
    public function index(): View
    {
		global $SPIRAL;

        try {
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

			$user_info = new UserInfo($SPIRAL);
	
            //$token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            //Csrf::validate($token,true);

			$barcode = $SPIRAL->getParam('barcode');
			if($barcode == "" || $barcode == null)
			{
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
			}
			
			if(preg_match('/^20/', $barcode) && strlen($barcode) == 12){
				if($user_info->isDistributorUser())
				{
                	throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
				}
				//検収書から発行されたラベル
				$receiving_num = substr($barcode, 2 , 10);
				if($user_info->isAdmin())
				{
					$result = ReceivingView::where('receivingNumber', 'rec_'.$receiving_num)->where('hospitalId',$user_info->getHospitalId())->get();
					$record = $result->data->get(0);
				}
				else
				{
					$result = ReceivingView::where('receivingNumber', 'rec_'.$receiving_num)->where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
					$record = $result->data->get(0);
				}
				
				$hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
				$hospital = $hospital->data->get(0);
				
				$divisionId = $record->divisionId;
				
				if($hospital->receivingTarget == "1"){
					$division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionType','1')->get();
					$division = $division->data->get(0);
					$divisionId = $division->divisionId;
				}

				$in_hospital_item = InHospitalItemView::where('notUsedFlag','0')->where('inHospitalItemId',$record->inHospitalItemId)->where('hospitalId', $user_info->getHospitalId())->get();	
				$in_hospital_item = $in_hospital_item->data->get(0);
				
				if($result->count == '0'){
					throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
				}
				$data = [
					"divisionId" => $divisionId,
					"itemId" => $in_hospital_item->itemId,
					"maker" => $in_hospital_item->makerName,
					"shouhinName" => $in_hospital_item->itemName,
					"code" => $in_hospital_item->itemCode,
					"kikaku" => $in_hospital_item->itemStandard,
					"irisu" => $in_hospital_item->quantity,
					"kakaku" => $in_hospital_item->price,
					"jan" => $in_hospital_item->itemJANCode,
					"oroshi" => $in_hospital_item->distributorName,
					"recordId" => $in_hospital_item->inHospitalItemId,
					"unit" => $in_hospital_item->quantityUnit,
					"itemUnit" => $in_hospital_item->itemUnit,
					"distributorId" => $in_hospital_item->distributorId,
					"count" => $in_hospital_item->quantity,
					"labelId" => $in_hospital_item->labelId,
					"unitPrice" => $in_hospital_item->unitPrice,
					"lotNumber" => $record->lotNumber,
					"lotDate" => \App\Lib\changeDateFormat('Y年m月d日' , $record->lotDate , 'Y-m-d'),
					"lotFlag" => ($in_hospital_item->lotManagement == 1 )? "はい": "",
					"lotFlagBool" => $in_hospital_item->lotManagement,
				];

				$content = new ApiResponse($data , $result->count , $result->code, $result->message, ['LabelBarcodeSearchApi']);
				$content = $content->toJson();

			} else if(preg_match('/^30/', $barcode) && strlen($barcode) == 12){
				if($user_info->isDistributorUser())
				{
                	throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
				}
				//払出から発行されたラベル
				$payout_num = substr($barcode, 2 , 10);
				if($user_info->isAdmin())
				{
					$result = PayoutView::where('payoutId', 'payout_'.$payout_num)->where('hospitalId',$user_info->getHospitalId())->get();
					$record = $result->data->get(0);
				}
				else
				{
					$result = PayoutView::where('payoutId', 'payout_'.$payout_num)->where('hospitalId',$user_info->getHospitalId())->where('sourceDivisionId',$user_info->getDivisionId())->get();
					$record = $result->data->get(0);
				}
				$in_hospital_item = InHospitalItemView::where('notUsedFlag','0')->where('inHospitalItemId',$record->inHospitalItemId)->where('hospitalId', $user_info->getHospitalId())->get();	
				$in_hospital_item = $in_hospital_item->data->get(0);

				if($result->count == '0'){
					throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
				}
				$data = [
					"divisionId" => $record->targetDivisionId,
					"itemId" => $in_hospital_item->itemId,
					"maker" => $in_hospital_item->makerName,
					"shouhinName" => $in_hospital_item->itemName,
					"code" => $in_hospital_item->itemCode,
					"kikaku" => $in_hospital_item->itemStandard,
					"irisu" => $in_hospital_item->quantity,
					"kakaku" => $in_hospital_item->price,
					"jan" => $in_hospital_item->itemJANCode,
					"oroshi" => $in_hospital_item->distributorName,
					"recordId" => $in_hospital_item->inHospitalItemId,
					"unit" => $in_hospital_item->quantityUnit,
					"itemUnit" => $in_hospital_item->itemUnit,
					"distributorId" => $in_hospital_item->distributorId,
					"count" => $record->payoutQuantity,
					"labelId" => $in_hospital_item->labelId,
					"unitPrice" => $in_hospital_item->unitPrice,
					"lotNumber" => $record->lotNumber,
					"lotDate" => \App\Lib\changeDateFormat('Y年m月d日' , $record->lotDate , 'Y-m-d'),
					"lotFlag" => ($in_hospital_item->lotManagement == 1 )? "はい": "",
					"lotFlagBool" => $in_hospital_item->lotManagement,
				];

				$content = new ApiResponse($data , $result->count , $result->code, $result->message, ['LabelBarcodeSearchApi']);
				$content = $content->toJson();

			} else if(preg_match('/^90/', $barcode) && strlen($barcode) == 18){
				if($user_info->isDistributorUser())
				{
                	throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
				}
				if($user_info->isAdmin())
				{
					$result = CardInfoView::where('cardId', $barcode)->where('hospitalId',$user_info->getHospitalId())->get();
					$record = $result->data->get(0);
				}
				else
				{
					$result = CardInfoView::where('cardId', $barcode)->where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
					$record = $result->data->get(0);
				}

				if($result->count == '0'){
					throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
				}
				$in_hospital_item = InHospitalItemView::where('hospitalId',$user_info->getHospitalId())->where('inHospitalItemId',$record->inHospitalItemId)->get();
				$in_hospital_item = $in_hospital_item->data->get(0);
				$data = [
					"divisionId" => $record->divisionId,
					"itemId" => $in_hospital_item->itemId,
					"maker" => $in_hospital_item->makerName,
					"shouhinName" => $in_hospital_item->itemName,
					"code" => $in_hospital_item->itemCode,
					"kikaku" => $in_hospital_item->itemStandard,
					"irisu" => $in_hospital_item->quantity,
					"kakaku" => $in_hospital_item->price,
					"jan" => $in_hospital_item->itemJANCode,
					"oroshi" => $in_hospital_item->distributorName,
					"recordId" => $in_hospital_item->inHospitalItemId,
					"unit" => $in_hospital_item->quantityUnit,
					"itemUnit" => $in_hospital_item->itemUnit,
					"distributorId" => $in_hospital_item->distributorId,
					"count" => $record->quantity,
					"countNum" => $record->quantity,
					"labelId" => $in_hospital_item->labelId,
					"unitPrice" => $in_hospital_item->unitPrice,
					"lotNumber" => $record->lotNumber,
					"lotDate" => \App\Lib\changeDateFormat('Y年m月d日' , $record->lotDate , 'Y-m-d'),
					"lotFlag" => ($in_hospital_item->lotManagement == 1 )? "はい": "",
					"lotFlagBool" => $in_hospital_item->lotManagement,
				];

				$content = new ApiResponse($data , $result->count , $result->code, $result->message, ['LabelBarcodeSearchApi']);
				$content = $content->toJson();

			} else if(strlen($barcode) == 13) {
				//JANコード検索（複数件返却される）
				$InHospitalItemView = InHospitalItemView::where('notUsedFlag','0')->where('itemJANCode',$barcode)->where('hospitalId', $user_info->getHospitalId());
				if($user_info->isDistributorUser())
				{
					$InHospitalItemView->where('distributorId',$user_info->getDistributorId());
				}
				
				$result = $InHospitalItemView->get();
				if($result->count == '0'){
					throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
				}
				$data = [];
				if($result->count == '1'){
					$record = $result->data->get(0);
					$data = [
						"divisionId" => '',
						"itemId" => $record->itemId,
						"maker" => $record->makerName,
						"shouhinName" => $record->itemName,
						"code" => $record->itemCode,
						"kikaku" => $record->itemStandard,
						"irisu" => $record->quantity,
						"kakaku" => $record->price,
						"jan" => $record->itemJANCode,
						"oroshi" => $record->distributorName,
						"recordId" => $record->inHospitalItemId,
						"unit" => $record->quantityUnit,
						"itemUnit" => $record->itemUnit,
						"distributorId" => $record->distributorId,
						"count" => (int)0,
						"labelId" => $record->labelId,
						"unitPrice" => $record->unitPrice,
						"lotFlag" => ($record->lotManagement == 1 )? "はい": "",
						"lotFlagBool" => $record->lotManagement,
					];
				} else {
					foreach($result->data->all() as $record){
						$data[] = [
							"divisionId" => '',
							"itemId" => $record->itemId,
							"maker" => $record->makerName,
							"shouhinName" => $record->itemName,
							"code" => $record->itemCode,
							"kikaku" => $record->itemStandard,
							"irisu" => $record->quantity,
							"kakaku" => $record->price,
							"jan" => $record->itemJANCode,
							"oroshi" => $record->distributorName,
							"recordId" => $record->inHospitalItemId,
							"unit" => $record->quantityUnit,
							"itemUnit" => $record->itemUnit,
							"distributorId" => $record->distributorId,
							"count" => (int)0,
							"labelId" => $record->labelId,
							"unitPrice" => $record->unitPrice,
							"lotFlag" => ($record->lotManagement == 1 )? "はい": "",
							"lotFlagBool" => $record->lotManagement,
						];
					}
				}
				
				$content = new ApiResponse($data , $result->count , $result->code, $result->message, ['LabelBarcodeSearchApi']);
				$content = $content->toJson();
				
			} else if((preg_match('/^1/', $barcode) && strlen($barcode) == 14 ) || (preg_match('/^01/', $barcode) && strlen($barcode) == 14)){
				//在庫表等で発行されたラベル
				if(preg_match('/^1/', $barcode) && strlen($barcode) == 14){
					$label_id = substr($barcode, 1 , 5);
					$label_id = str_pad($label_id, 8, 0, STR_PAD_LEFT);
					$custom_quantity = substr($barcode, 10 , 4);
				}
				else if(preg_match('/^01/', $barcode) && strlen($barcode) == 14){
					$label_id = substr($barcode, 2 , 8);
					$custom_quantity = substr($barcode, 10 , 4);
				}
				
				$InHospitalItemView = InHospitalItemView::where('notUsedFlag','0')->where('labelId',$label_id)->where('hospitalId', $user_info->getHospitalId());
				
				if($user_info->isDistributorUser())
				{
					$InHospitalItemView->where('distributorId',$user_info->getDistributorId());
				}
				
				$result = $InHospitalItemView->get();
				if($result->count == '0'){
					throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
				}
				$record = $result->data->get(0);
				
				$data = [
					"divisionId" => '',
					"itemId" => $record->itemId,
					"maker" => $record->makerName,
					"shouhinName" => $record->itemName,
					"code" => $record->itemCode,
					"kikaku" => $record->itemStandard,
					"irisu" => $record->quantity,
					"kakaku" => $record->price,
					"jan" => $record->itemJANCode,
					"oroshi" => $record->distributorName,
					"recordId" => $record->inHospitalItemId,
					"unit" => $record->quantityUnit,
					"itemUnit" => $record->itemUnit,
					"distributorId" => $record->distributorId,
					"count" => (int)$custom_quantity,//ラベルの入数
					"countNum" => (int)$custom_quantity,//ラベルの入数
					"labelId" => $record->labelId,
					"unitPrice" => $record->unitPrice,
					"lotFlag" => ($record->lotManagement == 1 )? "はい": "",
					"lotFlagBool" => $record->lotManagement,
				];
		
				$content = new ApiResponse($data , $result->count , $result->code, $result->message, ['LabelBarcodeSearchApi']);
				$content = $content->toJson();
			} else {
				$content = new ApiResponse([] , 0 , 0, 'not data', ['LabelBarcodeSearchApi']);
				$content = $content->toJson();
			}
		} catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['LabelBarcodeSearchApi']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
	}
}

$LabelBarcodeSearchController = new LabelBarcodeSearchController();
echo $LabelBarcodeSearchController->index()->render();