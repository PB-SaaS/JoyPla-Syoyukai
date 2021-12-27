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
use App\Model\InHospitalItemView;
use App\Model\Inventory;
use App\Model\InventoryEnd;
use App\Model\InventoryHistory;
use App\Model\StockTakingTransaction;
use App\Model\StockView;
use App\Model\Lot;


use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class InventoryController extends Controller
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
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if($user_info->isApprover())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            if ($user_info->isAdmin()) 
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            } 
            else
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }

            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->all();
            $useUnitPrice = $hospital_data[0]->invUnitPrice;
    
            $api_url = "%url/rel:mpgt:Inventory%";
    
            $content = $this->view('NewJoyPla/view/InventoryContentEntry', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'division'=> $division,
                'useUnitPrice'=> $useUnitPrice,
                'csrf_token' => Csrf::generate(16)
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                'csrf_token' => Csrf::generate(16)
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 棚卸内容入力',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function inventoryRegistAPI()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);

            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $inventory = $SPIRAL->getParam('inventory');
            $inventory = $this->requestUrldecode($inventory);
            $divisionId = $SPIRAL->getParam('divisionId');
            
            if($divisionId == '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            
            $in_hospital_item = InHospitalItemView::where('hospitalId', $user_info->getHospitalId());
            foreach($inventory as $key => $record)
            {
                $in_hospital_item->orWhere('inHospitalItemId',$record['recordId']);
            }
            $in_hospital_item = $in_hospital_item->get();
            
            foreach ($inventory as $record)
            {
                foreach($in_hospital_item->data->all() as $in_hp_item)
                {
                    $lot_flag = 0;
                    if($record['recordId'] == $in_hp_item->inHospitalItemId)
                    {
                        $lot_flag = (int)$in_hp_item->lotManagement;
                        break;
                    }
                }
                if($record['countNum'] > 0)
                {
                    if($lot_flag && ($record['lotNumber'] == '' || $record['lotDate'] == '' ))
                    {
                        throw new Exception('invalid lot',100);
                    }
                    
                    if (($record['lotNumber'] != '' && $record['lotDate'] == '' ) || ($record['lotNumber'] == '' && $record['lotDate'] != ''))
                    {
                        throw new Exception('invalid lotNumber input',101);
                    }
                    if (($record['lotNumber'] != '') && ($record['lotDate'] != '')) 
                    {
                        //if ((!ctype_alnum($item['lotNumber'])) || (strlen($item['lotNumber']) > 20))
                        if ((!preg_match('/^[a-zA-Z0-9!-\/:-@¥[-`{-~]+$/', $record['lotNumber'])) || (strlen($record['lotNumber']) > 20))
                        {
                            throw new Exception('invalid lotNumber format',102);
                        }
                    }
                }
            }

            $inventoryEnd = InventoryEnd::where('hospitalId',$user_info->getHospitalId())->where('inventoryStatus','1')->get();
            $invEndId = $this->makeId('09');
            if ($inventoryEnd->count == 0)
            {
                $result = InventoryEnd::create(['hospitalId' => $user_info->getHospitalId(), 'inventoryEndId' => $invEndId]);
            } else {
                $invEndId = $inventoryEnd->data->get(0)->inventoryEndId;
            }

            $inventoryHistory = InventoryHistory::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$divisionId)->where('inventoryEndId',$invEndId)->get();
            
            $invHistId = $this->makeId('08');
            
            if ($inventoryHistory->count == 0)
            {
                $create_data = [];
                $create_data = [
                    'inventoryHId' => $invHistId,
                    'inventoryEndId' => $invEndId,
                    'hospitalId' => $user_info->getHospitalId(),
                    'divisionId' => $divisionId,
                    'itemsNumber' => 0,
                    'totalAmount' => 0
                ];
                $result = InventoryHistory::create($create_data);
            } else {
                $invHistId = $inventoryHistory->data->get(0)->inventoryHId;
            }

            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $useUnitPrice = (int)$hospital_data->invUnitPrice;
            $stock_taking_trdata = [];
            
            //在庫として存在するものを０で登録する
            /*
            $stock = StockView::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$divisionId);
            
            foreach ($inventory as $data)
            {
                //棚卸対象以外
                $stock->where('inHospitalItemId',$data['recordId'],"!=");
            }
            
            $stock = $stock->get();
            
            foreach ($stock->data->all() as $data)
            {
                
                $unitPrice = $useUnitPrice
                    ? (str_replace(',', '', $data->unitPrice))
                    : (
                        ((float)$data->price == 0 || (float)$data->quantity == 0)
                            ? 0 
                            : (float)$data->price / (float)$data->quantity
                        );
                $stock_taking_trdata[] = [
                    'inventoryEndId' => $invEndId,
                    'inventoryHId' => $invHistId,
                    'inHospitalItemId' => $data->inHospitalItemId,
                    'hospitalId' => $user_info->getHospitalId(),
                    'divisionId' => $data->divisionId,
                    'price' => str_replace(',', '', $data->price),
                    'inventryNum' => 0, // 数は０で登録
                    'inventryAmount' => (float)$unitPrice * 0,
                    'quantity' => $data->quantity,
                    'quantityUnit' => $data->quantityUnit,
                    'itemUnit' => $data->itemUnit,
                    'unitPrice' => $unitPrice,
                    'invUnitPrice' => (float)$useUnitPrice,
                    'lotNumber' => '',
                    'lotDate' => '',
                    'lotUniqueKey' => $user_info->getHospitalId().$divisionId.$data->inHospitalItemId.''.''
                ];
            }
            */
            foreach ($inventory as $data)
            {
                if( (int)$data['countNum']  >= 0 )
                {
                    $unitPrice = $useUnitPrice
                        ? (str_replace(',', '', $data['unitPrice']))
                        : (
                            ((float)str_replace(',', '', $data['kakaku']) == 0 || (float)$data['irisu'] == 0)
                            ? 0 
                            : ((float)str_replace(',', '', $data['kakaku']) / (float)$data['irisu']) 
                        );
                    $stock_taking_trdata[] = [
                        'inventoryEndId' => $invEndId,
                        'inventoryHId' => $invHistId,
                        'inHospitalItemId' => $data['recordId'],
                        'hospitalId' => $user_info->getHospitalId(),
                        'divisionId' => $divisionId,
                        'price' => str_replace(',', '', $data['kakaku']),
                        'inventryNum' => (int)$data['countNum'],
                        'inventryAmount' => (float)$unitPrice * (float)$data['countNum'],
                        'quantity' => $data['irisu'],
                        'quantityUnit' => $data['unit'],
                        'itemUnit' => $data['itemUnit'],
                        'unitPrice' => $unitPrice,
                        'invUnitPrice' => (float)$useUnitPrice,
                        'lotNumber' => $data['lotNumber'],
                        'lotDate' => $data['lotDate'],
                        'lotUniqueKey' => $user_info->getHospitalId().$divisionId.$data['recordId'].$data['lotNumber'].$data['lotDate']
                    ];
                }
            }

            $result = StockTakingTransaction::insert($stock_taking_trdata);

            $inventory_history_data = Inventory::where('hospitalId',$user_info->getHospitalId())->where('inventoryHId',$invHistId)->get();
            $inventory_history_data = $inventory_history_data->data->all();
            $history_ids = [];
            $history_total_amount = 0;
            foreach ($inventory_history_data as $val)
            {
                $history_total_amount = $history_total_amount + (float)$val->inventryAmount;
                if (!in_array($val->inHospitalItemId, $history_ids)) {
                    $history_ids[] = $val->inHospitalItemId;
                }
            }

            $end_inventory_data = Inventory::where('hospitalId',$user_info->getHospitalId())->where('inventoryEndId',$invEndId)->get();
            $end_inventory_data = $end_inventory_data->data->all();
            $end_ids = [];
            $end_total_amount = 0;
            foreach ($end_inventory_data as $val)
            {
                $end_total_amount = $end_total_amount + (float)$val->inventryAmount;
                if (!in_array($val->inHospitalItemId, $end_ids)) {
                    $end_ids[] = $val->inHospitalItemId;
                }
            }

            $result = InventoryHistory::where('inventoryHId',$invHistId)->update([
                'updateTime' => 'now', 
                'itemsNumber' => count($history_ids), 
                'totalAmount' => $history_total_amount
            ]);
            
            $result = InventoryEnd::where('inventoryEndId',$invEndId)->where('hospitalId',$user_info->getHospitalId())->update([
                'itemsNumber' => count($end_ids), 
                'totalAmount' => $end_total_amount
            ]);

            $content = new ApiResponse($result->ids , $result->count , $result->code, $result->message, ['inventoryEntry']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['inventoryEntry']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    public function getLotAndStockApi()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);

            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $divisionId = $SPIRAL->getParam('divisionId');
            
            if($divisionId == '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $stock = StockView::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$divisionId)->where('stockQuantity',0,">")->get();
            
            $lot = Lot::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$divisionId)->where('stockQuantity',0,">")->get();
            
            $list = [];
            
			$in_hospital_item = InHospitalItemView::where('hospitalId',$user_info->getHospitalId());
			
            foreach($stock->data->all() as $stock_item )
            {
			    $in_hospital_item->orWhere('inHospitalItemId',$stock_item->inHospitalItemId);
            }
            foreach($lot->data->all() as $lot_item )
            {
			    $in_hospital_item->orWhere('inHospitalItemId',$lot_item->inHospitalItemId);
            }
            
			$in_hospital_item = $in_hospital_item->get();
            $data = [];
            foreach($stock->data->all() as $stock_item )
            {
                $stockQuantity = (int)$stock_item->stockQuantity;
                $lotManagement = 0;
                foreach($in_hospital_item->data->all() as $in_hp_item)
                {
                    if($in_hp_item->inHospitalItemId == $stock_item->inHospitalItemId)
                    {
                        $lotManagement = $in_hp_item->lotManagement;
                        break;
                    }
                }
                
                foreach($lot->data->all() as $lot_item)
                {
                    if($lot_item->inHospitalItemId == $stock_item->inHospitalItemId)
                    {
                        $stockQuantity = $stockQuantity - (int)$lot_item->stockQuantity;
                        $data[] = [
        					"divisionId" => '',
        					"maker" => $stock_item->makerName,
        					"shouhinName" => $stock_item->itemName,
        					"code" => $stock_item->itemCode,
        					"kikaku" => $stock_item->itemStandard,
        					"irisu" => $stock_item->quantity,
        					"kakaku" => $stock_item->price,
        					"jan" => $stock_item->itemJANCode,
        					"oroshi" => $stock_item->distributorName,
        					"recordId" => $stock_item->inHospitalItemId,
        					"unit" => $stock_item->quantityUnit,
        					"itemUnit" => $stock_item->itemUnit,
        					"distributorId" => $stock_item->distributorId,
        					"count" => (int)$lot_item->stockQuantity,
        					"countNum" => (int)$lot_item->stockQuantity,
        					"labelId" => $stock_item->labelId,
        					"unitPrice" => $stock_item->unitPrice,
        					"lotNumber" => $lot_item->lotNumber,
        					"lotDate" => \App\Lib\changeDateFormat('Y年m月d日' , $lot_item->lotDate , 'Y-m-d'),
        					"lotFlag" => ($lotManagement == 1 )? "はい": "",
        					"lotFlagBool" => $lotManagement,
        				];
                    }
                }
                
                if($stockQuantity != 0)
                {
                    $data[] = [
    					"divisionId" => '',
    					"maker" => $stock_item->makerName,
    					"shouhinName" => $stock_item->itemName,
    					"code" => $stock_item->itemCode,
    					"kikaku" => $stock_item->itemStandard,
    					"irisu" => $stock_item->quantity,
    					"kakaku" => $stock_item->price,
    					"jan" => $stock_item->itemJANCode,
    					"oroshi" => $stock_item->distributorName,
    					"recordId" => $stock_item->inHospitalItemId,
    					"unit" => $stock_item->quantityUnit,
    					"itemUnit" => $stock_item->itemUnit,
    					"distributorId" => $stock_item->distributorId,
    					"count" => (int)$stockQuantity,
    					"countNum" => (int)$stockQuantity,
    					"labelId" => $stock_item->labelId,
    					"unitPrice" => $stock_item->unitPrice,
    					"lotNumber" => '',
    					"lotDate" => '',
    					"lotFlag" => ($lotManagement == 1 )? "はい": "",
    					"lotFlagBool" => $lotManagement,
    				];
                }
            }

            $content = new ApiResponse($data , count($data) , 0 , "OK", ['getLotAndStockApi']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['getLotAndStockApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    public function inventoryEndList()
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = array();
        try {

            $user_info = new UserInfo($SPIRAL);
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $content = $this->view('NewJoyPla/view/InventoryHistoryList', [
                //'api_url' => $api_url,
                'user_info' => $user_info,
                'csrf_token' => Csrf::generate(16)
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 棚卸履歴一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }

}

/***
 * 実行
 */
$InventoryController = new InventoryController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'inventoryRegistApi')
    {
        echo $InventoryController->inventoryRegistAPI()->render();
    }
    else if($action === 'getLotAndStockApi')
    {
        echo $InventoryController->getLotAndStockApi()->render();
    }
    else if($action === 'inventoryEndList')
    {
        echo $InventoryController->inventoryEndList()->render();
    }
    else
    {
        echo $InventoryController->index()->render();
    }
}
