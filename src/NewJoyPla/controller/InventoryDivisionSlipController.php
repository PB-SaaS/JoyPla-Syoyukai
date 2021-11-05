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
use App\Model\Inventory;
use App\Model\InventoryEnd;
use App\Model\InventoryHistory;
use App\Model\StockView;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class InventoryDivisionSlipController extends Controller
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
            
            $division_history_slip = InventoryHistory::where('hospitalId',$user_info->getHospitalId())->find($record_id)->get();
            $division_history_slip = $division_history_slip->data->get(0);
            
            $end_slip = InventoryEnd::where('hospitalId',$user_info->getHospitalId())->where('inventoryEndId',$division_history_slip->inventoryEndId)->get();
            $end_slip = $end_slip->data->get(0);
            
            $inventory_items = Inventory::where('hospitalId',$user_info->getHospitalId())->where('inventoryHId',$division_history_slip->inventoryHId)->get();
            
            $inventory_remake_items = [];
            
            $stock = StockView::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$division_history_slip->divisionId);

            foreach($inventory_items->data->all() as $item )
            {
                $stock->orWhere('inHospitalItemId',$item->inHospitalItemId);
            }
            
            $stock = $stock->get();
            
            foreach($inventory_items->data->all() as $item )
            {
                if(array_key_exists($item->inHospitalItemId , $inventory_remake_items) == false)
                {
                    foreach($stock->data->all() as $stock_item){
                        if($stock_item->inHospitalItemId == $item->inHospitalItemId)
                        {
                            $inventory_remake_items[$item->inHospitalItemId] = $stock_item;
                            $inventory_remake_items[$item->inHospitalItemId]->inventryNum = $item->inventryNum;
                            $inventory_remake_items[$item->inHospitalItemId]->inventryAmount = $item->inventryAmount;
                            break;
                        }
                    }
                } 
                else 
                {
                    $inventory_remake_items[$item->inHospitalItemId]->inventryNum = (int)$inventory_remake_items[$item->inHospitalItemId]->inventryNum + (int)$item->inventryNum;
                    $inventory_remake_items[$item->inHospitalItemId]->inventryAmount = (float)$inventory_remake_items[$item->inHospitalItemId]->inventryAmount + (float)$item->inventryAmount;
                }
            }
            
            if($end_slip->inventoryStatus != 2)
            {
                $prices = [];
                $in_hospital_item_ids = [];
                foreach($inventory_items->data->all() as $inventory_item)
                {
                    $prices[] = (float)$inventory_item->inventryAmount;
                    if(!in_array( $inventory_item->inHospitalItemId ,$in_hospital_item_ids))
                    {
                        $in_hospital_item_ids[] = $inventory_item->inHospitalItemId;
                    }
                }
                
                InventoryHistory::where('hospitalId',$user_info->getHospitalId())->find($record_id)->update([
                    "totalAmount" => collect($prices)->sum(),
                    "itemsNumber" => collect($in_hospital_item_ids)->count(),
                ]);
                	
                $history_items = Inventory::where('hospitalId',$user_info->getHospitalId())->where('inventoryEndId', $division_history_slip->inventoryEndId)->get();
                
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
                
                InventoryEnd::where('hospitalId',$user_info->getHospitalId())->where('inventoryEndId',$division_history_slip->inventoryEndId)->update([
                    'totalAmount' => collect($prices)->sum(),
                    "itemsNumber" => collect($in_hospital_item_ids)->count(),
                ]);
            }
            
            $link = "%url/rel:mpgt:Inventory%&Action=inventoryEndList";
            
            if($end_slip->inventoryStatus == 2 || 
                ($user_info->isUser() && $end_slip->divisionId != $user_info->getDivisionId() ) ||
                ($user_info->isApprover() )
              )
            {
                $table = "%sf:usr:search25:mstfilter:table%";//修正不可能
            }
            else
            {
                $table = "%sf:usr:search24:mstfilter:table%";//修正可能
            }
            
            $delete_button_view_flg = false;
            if($end_slip->inventoryStatus == 1) {
                if( $user_info->isAdmin() || $user_info->isApprover() )
                {
                    $delete_button_view_flg = true;
                } 
                else if( $user_info->isUser() && $user_info->getDivisionId() == $division_history_slip->divisionId )  
                {
                    $delete_button_view_flg = true;
                }
            }
            
            $api_url = "%url/card:page_263632%";
            $content = $this->view('NewJoyPla/view/InventorySlip', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'link' => $link,
                'delete_button_view_flg' => $delete_button_view_flg,
                'end_flg' => ( $end_slip->inventoryStatus == 2 ),
                'useUnitPrice'=> $useUnitPrice,
                'inventory_total_items' => $inventory_remake_items,
                'table' => $table,
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
    
    public function deleteSlipApi(): View
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
            
            $inventory_history = InventoryHistory::where('hospitalId',$user_info->getHospitalId())->find($record_id)->get();
            $inventory_history = $inventory_history->data->get(0);
            
            $end_slip = InventoryEnd::where('hospitalId',$user_info->getHospitalId())->where('inventoryEndId',$inventory_history->inventoryEndId)->get();
            $end_slip = $end_slip->data->get(0);
            
            if($end_slip->inventoryStatus == 2)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $result = InventoryHistory::where('hospitalId',$user_info->getHospitalId())->find($record_id)->delete();
        
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
$InventoryDivisionSlipController = new InventoryDivisionSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'deleteSlipApi')
    {
        echo $InventoryDivisionSlipController->deleteSlipApi()->render();
    }
    else
    {
        echo $InventoryDivisionSlipController->index()->render();
    }
}


