<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Division;
use App\Model\ReceivingHistory;
use App\Model\ReceivingHistoryView;
use App\Model\ReceivingView;
use App\Model\Hospital;
use App\Model\Receiving;
use App\Model\ReturnItem;
use App\Model\ReturnHistory;
use App\Model\InventoryAdjustmentTransaction;


use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class ReceiptSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function acceptanceDocument(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);
            $record_id = (int)$SPIRAL->getCardId();
            $receiving_history = ReceivingHistoryView::where('hospitalId', $user_info->getHospitalId())->find($record_id)->get();
            $receiving_history = $receiving_history->data->get(0);
            
            if($user_info->isUser() && $receiving_history->divisionId != $user_info->getDivisionId())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $receiving_item = ReceivingView::where('hospitalId', $user_info->getHospitalId())->where('receivingHId',$receiving_history->receivingHId)->get();
            $receiving_item = $receiving_item->data->all();
            
        	$link = '%url/rel:mpgt:Receipt%&Action=acceptanceList';
            if($user_info->isUser()){
                if (preg_match("/Action=acceptanceListForDivision/", $_SERVER['HTTP_REFERER'])) {
            	    $link = $_SERVER['HTTP_REFERER'];
                }
            }
            
            $api_url = "%url/card:page_266892%";
    
            $content = $this->view('NewJoyPla/view/AcceptanceDocument', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'link_title' => '検収書一覧',
                'receiving_history' => $receiving_history,
                'receiving_item' => $receiving_item,
                'link'=> $link,
                'csrf_token' => Csrf::generate(16)
                ] , false);
    
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 検収書',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function returnSlipEntry(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);
            $record_id = (int)$SPIRAL->getCardId();
            $receiving_history = ReceivingHistoryView::where('hospitalId', $user_info->getHospitalId())->find($record_id)->get();
            $receiving_history = $receiving_history->data->get(0);
            
            if($user_info->isUser() && $receiving_history->divisionId != $user_info->getDivisionId())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $receiving_item = ReceivingView::where('hospitalId', $user_info->getHospitalId())->where('receivingHId',$receiving_history->receivingHId)->get();
            $receiving_item = $receiving_item->data->all();
            
                        
            foreach($receiving_item as $record){
            	$ItemsToJs[$record->receivingNumber] = array(
            		"inHospitalItemId" => $record->inHospitalItemId,
            		"receivingCount" => $record->receivingCount,
            		"quantity" => $record->quantity,
            		"orderCNumber" => $record->orderCNumber,
            		"makerName" => $record->makerName,
            		"itemName" => $record->itemName,
            		"itemCode" => $record->itemCode,
            		"itemStandard" => $record->itemStandard,
            		"quantityUnit" => $record->quantityUnit,
            		"itemUnit" => $record->itemUnit,
            		"itemJANCode" => $record->itemJANCode,
            		"orderQuantity" => $record->orderQuantity,
            		"receivingNumber" => $record->receivingNumber,
            		"totalReturnCount" => $record->totalReturnCount,
            		"price" => $record->price,
            		"returnCount" => 0,
            		"lotNumber" => $record->lotNumber,
            		"lotDate" => $record->lotDate
            		);
            }
            
        	$link = '%url/rel:mpgt:Receipt%&Action=acceptanceList';
            if($user_info->isUser()){
                if (preg_match("/Action=acceptanceListForDivision/", $_SERVER['HTTP_REFERER'])) {
            	    $link = $_SERVER['HTTP_REFERER'];
                }
            }
            
            $api_url = "%url/card:page_266892%";
    
            $content = $this->view('NewJoyPla/view/ReturnSlipEntry', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'link_title' => '検収書一覧',
                'receiving_item' => $receiving_item,
                'receiving_history' => $receiving_history,
                'link'=> $link,
                'ItemsToJs' => $ItemsToJs,
                'csrf_token' => Csrf::generate(16),
                ] , false);
    
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 返品伝票入力',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function regReturnApi()
    {
        
        global $SPIRAL;
        $content = '';
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);

            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            if ($user_info->isApprover())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            $return_items = $SPIRAL->getParam('returnData');
            $return_items = $this->requestUrldecode($return_items);
            
            $record_id = (int)$SPIRAL->getCardId();
            $receiving_history = ReceivingHistory::where('hospitalId', $user_info->getHospitalId())->find($record_id)->get();
            $receiving_history = $receiving_history->data->get(0);
            
    		$return_history_id = $this->makeId('06');
    		
        	
        	$receiving = ReceivingView::where('hospitalId',$user_info->getHospitalId());
        	foreach($return_items as $data)
        	{
        	    $receiving->orWhere('receivingNumber',$data['receivingNumber']);
        	}
        	$receiving = $receiving->get();
        	
            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
            
            $divisionId = '';
            if($hospital->receivingTarget == '1'){ //大倉庫
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionType','1')->get();
                $division = $division->data->get(0);
                $divisionId = $division->divisionId;
            }
            if($hospital->receivingTarget == '2'){ //発注部署
                $divisionId = $card->divisionId;
            }
        	
        	$return_insert = [];
        	$receiving_update = [];
        	$inventory_adjustment_trdata = [];
        	$in_hospital_item_ids = [];
        	$price = [];
        	foreach($receiving->data->all() as $item)
        	{
        	    $return_count = 0;
            	foreach($return_items as $data){
            	    if($data['receivingNumber'] == $item->receivingNumber && $data['returnCount'] > 0){
            	        $return_insert[] = [
                			'receivingNumber'=>$item->receivingNumber,
                			'price'=>$item->price,
                			'returnCount'=>$data['returnCount'],
                			'returnPrice'=>(float)$item->price * (int)$data['returnCount'],
                			'hospitalId'=>$user_info->getHospitalId(),
                			'returnHistoryID'=>$return_history_id,
                			'orderCNumber'=>$item->orderCNumber,
                			'receivingHId'=>$receiving_history->receivingHId,
                			'inHospitalItemId'=>$item->inHospitalItemId,
                			'lotNumber'=>$item->lotNumber,
                			'lotDate'=>$item->lotDate
            			];
            			if(!in_array($item->inHospitalItemId , $in_hospital_item_ids))
            			{
            			    $in_hospital_item_ids[] = $item->inHospitalItemId;
            			}
                        if ($item->lotNumber && $item->lotDate) {
                            $lot_date = \App\Lib\changeDateFormat('Y年m月d日',$item->lotDate,'Y-m-d');
                            $inventory_adjustment_trdata[] = [
                                'divisionId' => $divisionId,
                                'inHospitalItemId' => $item->inHospitalItemId,
                                'count' => -((int)$item->quantity * (int)$data['returnCount']),
                                'hospitalId' => $user_info->getHospitalId(),
                                'lotUniqueKey' => $user_info->getHospitalId().$divisionId.$item->inHospitalItemId.$item->lotNumber.$lot_date,
                                'lotNumber' => $item->lotNumber,
                                'lotDate' => $lot_date,
                                'pattern' => 6,
                                'stockQuantity' => -((int)$item->quantity * (int)$data['returnCount']),
                            ];
                        }
                        else 
                        {
                            $inventory_adjustment_trdata[] = [
                                'divisionId' => $divisionId,
                                'pattern' => 6,
                                'inHospitalItemId' => $item->inHospitalItemId,
                                'count' => -((int)$item->quantity * (int)$data['returnCount']),
                                'hospitalId' => $user_info->getHospitalId()
                            ];
                        }
                        $price[] = (float)$item->price * (int)$data['returnCount'];
                        $return_count = $return_count + (int)$data['returnCount'];
            	    }
            	    
            	}
    			$receiving_update[] = [
    				'receivingNumber'=> $item->receivingNumber,
    				'totalReturnCount'=>(int)$item->totalReturnCount + (int)$return_count
				    ];
				
        	}
        	
        	$history_insert[] = [
					"receivingHId" => $receiving_history->receivingHId,
					"distributorId" => $receiving_history->distributorId,
					"returnHistoryID" => $return_history_id,
					"hospitalId" => $user_info->getHospitalId(),
					"itemsNumber" => collect($in_hospital_item_ids)->count(),
                    "returnTotalPrice" => collect($price)->sum(),
                    "divisionId" => $receiving_history->divisionId,
			        ];
			        
    		Receiving::bulkUpdate('receivingNumber',$receiving_update);
    		ReturnHistory::insert($history_insert);
    		ReturnItem::insert($return_insert);
    		
            $result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);
    		
            
            $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['return api']);
            $content = $content->toJson();

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['return api']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
}

/***
 * 実行
 */
$ReceiptSlipController = new ReceiptSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "returnSlipEntry")
    {
        echo $ReceiptSlipController->returnSlipEntry()->render();
    } 
    else if($action === "regReturnApi")
    {
        echo $ReceiptSlipController->regReturnApi()->render();
    }
    else 
    {
        echo $ReceiptSlipController->acceptanceDocument()->render();
    }
}

