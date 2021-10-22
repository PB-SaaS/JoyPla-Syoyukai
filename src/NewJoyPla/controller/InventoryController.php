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
use App\Model\StockTakingTransaction;

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
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            if (($user_info->isHospitalUser() && $user_info->getUserPermission() == '1')) 
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
            

            $getInventory = $SPIRAL->getParam('inventory');
            $inventory = $this->requestUrldecode($getInventory);
            $divisionId = $SPIRAL->getParam('divisionId');

            foreach ($inventory as $rows)
            {
                foreach ($rows as $record)
                {
                    if ((int)$record['lotFlag'])
                    {
                        if (($record['lotNumber'] == '') || ($record['lotDate'] == ''))
                        {
                            throw new Exception('invalid lot',100);
                        }
                    }
                    if (($record['lotNumber'] != '' && $record['lotDate'] == '' ) || ($record['lotNumber'] == '' && $record['lotDate'] != ''))
                    {
                        throw new Exception('invalid lotNumber input',101);
                    }
                    if (($record['lotNumber'] != '') && ($record['lotDate'] != '')) 
                    {
                        if ((!ctype_alnum($record['lotNumber'])) || (strlen($record['lotNumber']) > 20))
                        {
                            throw new Exception('invalid lotNumber format',102);
                        }
                    }
                }
            }

            $inventoryEnd = InventoryEnd::where('hospitalId',$user_info->getHospitalId())->where('inventoryStatus','1')->get();
            if ($inventoryEnd->count == 0)
            {
                $invEndId = $this->makeId('09');
                $result = InventoryEnd::create(['hospitalId' => $user_info->getHospitalId(), 'inventoryEndId' => $invEndId]);
            } else {
                $invEndId = $inventoryEnd->data->all()[0]->inventoryEndId;
            }

            $inventoryHistory = InventoryHistory::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$divisionId)->where('inventoryEndId',$invEndId)->get();
            if ($inventoryHistory->count == 0)
            {
                $invHistId = $this->makeId('08');
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
                $invHistId = $inventoryHistory->data->all()[0]->inventoryHId;
            }

            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->all();
            $useUnitPrice = $hospital_data[0]->invUnitPrice;
    
            $stock_taking_trdata = [];
            foreach ($inventory as $rows)
            {
                foreach ($rows as $key => $data)
                {
                    if( (int)$data['countNum']  >= 0 )
                    {
                        $unitPrice = $useUnitPrice
                            ? (str_replace(',', '', $data['unitPrice']))
                            : (str_replace(',', '', $data['kakaku']) / $data['irisu']);
                        $stock_taking_trdata[] = [
                            'registrationTime' => 'now',
                            'inventoryEndId' => $invEndId,
                            'inventoryHId' => $invHistId,
                            'inHospitalItemId' => $data['recordId'],
                            'hospitalId' => $user_info->getHospitalId(),
                            'divisionId' => $divisionId,
                            'price' => str_replace(',', '', $data['kakaku']),
                            'inventryNum' => (int)$data['countNum'],
                            'inventryAmount' => (int)$unitPrice * (int)$data['countNum'],
                            'quantity' => $data['irisu'],
                            'quantityUnit' => $data['unit'],
                            'itemUnit' => $data['itemUnit'],
                            'unitPrice' => $unitPrice,
                            'invUnitPrice' => (int)$useUnitPrice,
                            'lotNumber' => $data['lotNumber'],
                            'lotDate' => $data['lotDate'],
                            'lotUniqueKey' => $user_info->getHospitalId().$divisionId.$data['recordId'].$data['lotNumber'].$data['lotDate']
                        ];
                    }
                }
            }

            $result = StockTakingTransaction::insert($stock_taking_trdata);

            $inventory_history_data = Inventory::where('hospitalId',$user_info->getHospitalId())->where('inventoryHId',$invHistId)->get();
            $inventory_history_data = $inventory_history_data->data->all();
            $history_ids = [];
            $history_total_amount = 0;
            foreach ($inventory_history_data as $val)
            {
                $history_total_amount += (float)$val->inventryAmount;
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
                $end_total_amount += (float)$val->inventryAmount;
                if (!in_array($val->inHospitalItemId, $end_ids)) {
                    $end_ids[] = $val->inHospitalItemId;
                }
            }

            $result = InventoryHistory::where('inventoryHId',$invHistId)->update(['updateTime' => 'now', 'itemsNumber' => count($history_ids), 'totalAmount' => $history_total_amount]);
            $result = InventoryEnd::where('inventoryEndId',$invEndId)->where('hospitalId',$user_info->getHospitalId())->update(['itemsNumber' => count($end_ids), 'totalAmount' => $end_total_amount]);

            $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['payoutRegistApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }

    private function makeId($id = '00')
    {
        /*
        '02' => HP_BILLING_PAGE,
        '03_unorder' => HP_UNORDER_PAGE,
        '03_order' => HP_ORDER_PAGE,
        '04' => HP_RECEIVING_PAGE,
        '06' => HP_RETERN_PAGE,
        '05' => HP_PAYOUT_PAGE,
        */
        $id .= date("ymdHis");
        $id .= str_pad(substr(rand(),0,3) , 4, "0"); 
    
        return $id;
    }

    private function requestUrldecode(array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->requestUrldecode($value);
            } else {
                $result[$key] = urldecode($value);
            }
        }
        return $result;
    }

}

/***
 * 実行
 */
$InventoryController = new InventoryController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'inventoryRegistAPI')
    {
        echo $InventoryController->inventoryRegistAPI()->render();
    }
    else
    {
        echo $InventoryController->index()->render();
    }
}
