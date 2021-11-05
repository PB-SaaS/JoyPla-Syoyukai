<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\Division;
use App\Model\Hospital;
use App\Model\InHospitalItem;
use App\Model\InHospitalItemView;
use App\Model\Inventory;
use App\Model\InventoryEnd;
use App\Model\InventoryHistory;
use App\Model\Stock;
use App\Model\StockTakingTransaction;
use App\Model\StockView;
use App\Model\Lot;
use App\Model\InventoryAdjustmentTransaction;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class InventoryEndSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = array();
        try {

            $user_info = new UserInfo($SPIRAL);
            
            $record_id = (int)$SPIRAL->getCardId();
            
            $end_slip = InventoryEnd::where('hospitalId',$user_info->getHospitalId())->find($record_id)->get();
            $end_slip = $end_slip->data->get(0);
            
            if($end_slip->inventoryStatus != 2)
            {
                $history_items = Inventory::where('hospitalId',$user_info->getHospitalId())->where('inventoryEndId', $end_slip->inventoryEndId)->get();
                
                $prices = [];
                $in_hospital_item_ids = [];
                foreach($history_items->data->all() as $history_item)
                {
                    $prices[] = (float)$history_item->inventryAmount;
                    if(!in_array( $history_item->inHospitalItemId ,$in_hospital_item_ids))
                    {
                        $in_hospital_item_ids[] = $history_item->inHospitalItemId;
                    }
                }
                
                InventoryEnd::where('hospitalId',$user_info->getHospitalId())->find($record_id)->update([
                    'totalAmount' => collect($prices)->sum(),
                    "itemsNumber" => collect($in_hospital_item_ids)->count(),
                ]);
            }
            
            $link = "%url/rel:mpgt:Inventory%&Action=inventoryEndList";
            
            $api_url = "%url/card:page_263632%";
            $content = $this->view('NewJoyPla/view/InventoryInformationByDivision', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'link' => $link,
                'end_flg' => ( $end_slip->inventoryStatus == 2 ),
                'useUnitPrice'=> $useUnitPrice,
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
                'title'     => 'JoyPla 棚卸結果報告',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function slipDeleteApi(): View
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $record_id = (int)$SPIRAL->getCardId();
            
            $end_slip = InventoryEnd::where('hospitalId',$user_info->getHospitalId())->find($record_id)->get();
            $end_slip = $end_slip->data->get(0);
            
            if($end_slip->inventoryStatus == 2)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $result = InventoryEnd::where('hospitalId',$user_info->getHospitalId())->find($record_id)->delete();
        
            $content = new ApiResponse($result->ids , $result->count , $result->code, $result->message, ['slipDeleteApi']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['slipDeleteApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    public function slipFixApi(): View
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $record_id = (int)$SPIRAL->getCardId();
            
            $end_slip = InventoryEnd::where('hospitalId',$user_info->getHospitalId())->find($record_id)->get();
            $end_slip = $end_slip->data->get(0);
            
            if($end_slip->inventoryStatus == 2)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $inventory_items = Inventory::where('hospitalId',$user_info->getHospitalId())->where('inventoryEndId',$end_slip->inventoryEndId)->get();
            
            $in_hospital_items_ids = [];
            $division_ids = [];
            
            foreach($inventory_items->data->all() as $item)
            {
                if(!in_array($item->inHospitalItemId ,$in_hospital_items_ids))
                {
                    $in_hospital_items_ids[] = $item->inHospitalItemId;
                }
                if(!in_array($item->divisionId ,$division_ids))
                {
                    $division_ids[] = $item->divisionId;
                }
            }
            
            $inventory_adjustment_trdata = [];
            foreach($inventory_items->data->all() as $record)
    		{
    		    if($record->lotNumber && $record->lotDate)
    		    {
                    $lot_date = \App\Lib\changeDateFormat('Y年m月d日',$record->lotDate,'Y-m-d');
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record->divisionId,
                        'inHospitalItemId' => $record->inHospitalItemId,
                        'count' => $record->inventryNum,
                        'pattern' => 7,
                        'hospitalId' => $user_info->getHospitalId(),
        		        'lotUniqueKey' => $user_info->getHospitalId().$record->divisionId.$record->inHospitalItemId.$record->lotNumber.$lot_date,
        		        'stockQuantity' => $record->inventryNum,
                        'lotNumber' =>  $record->lotNumber,
                        'lotDate' =>    $lot_date,
        		    ];
    		    }
    		    else
    		    {
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record->divisionId,
                        'pattern' => 7,
                        'inHospitalItemId' => $record->inHospitalItemId,
                        'count' => $record->inventryNum,
                        'hospitalId' => $user_info->getHospitalId(),
        		    ];
    		    }
    		}
    		
            $stock = Stock::where('hospitalId', $user_info->getHospitalId());
            $lot = Lot::where('hospitalId', $user_info->getHospitalId());
            foreach($division_ids as $division_id)
            {
                $stock->orWhere('divisionId',$division_id);
                $lot->orWhere('divisionId',$division_id);
            }
            $stock->update(['stockQuantity'=>0 , 'invFinishTime' => 'now']); //一旦リセット
            $lot->delete(); //一旦リセット

            $result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);
            
            
            $stock_items = Stock::where('hospitalId',$user_info->getHospitalId())->get();
            $update_stock_data = [];
            foreach($stock_items->data->all() as $stock_item)
            {
                if(array_key_exists($stock_item->inHospitalItemId, $update_stock_data) === false)
                {
                    $update_stock_data[$stock_item->inHospitalItemId] = 0;
                }
                $update_stock_data[$stock_item->inHospitalItemId] = $update_stock_data[$stock_item->inHospitalItemId] + (int)$stock_item->stockQuantity;
            }
            
            $in_hospital_update = [];
            
            foreach($update_stock_data as $in_hospital_item_id => $count)
            {
                $in_hospital_update[] = [
                    'inHospitalItemId' => $in_hospital_item_id,
                    'HPstock' => $count,
                    ];
            }
            
            InHospitalItem::where('hospitalId',$user_info->getHospitalId())->bulkUpdate('inHospitalItemId',$in_hospital_update);
            $result = InventoryEnd::where('hospitalId',$user_info->getHospitalId())->find($record_id)->update([
                'inventoryTime' => 'now',
                'inventoryStatus' => 2
                ]);
            $content = new ApiResponse($result->ids , $result->count , $result->code, $result->message, ['slipDeleteApi']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['slipDeleteApi']);
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
$InventoryEndSlipController = new InventoryEndSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'slipDeleteApi')
    {
        echo $InventoryEndSlipController->slipDeleteApi()->render();
    }
    else if($action === 'slipFixApi')
    {
        echo $InventoryEndSlipController->slipFixApi()->render();
    }
    else
    {
        echo $InventoryEndSlipController->index()->render();
    }
}


