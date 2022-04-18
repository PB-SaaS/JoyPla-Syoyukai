<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\Auth;
use App\Model\Hospital;

use ApiErrorCode\FactoryApiErrorCode;
use App\Model\Billing;
use App\Model\Division;
use App\Model\InHospitalItemView;
use App\Model\Inventory;
use App\Model\InventoryHistoryDivisionView;
use App\Model\ReceivingView;
use App\Model\StockView;
use stdClass;
use Exception;

class MonthlyReportController extends Controller
{
    
    public function __construct()
    {
    }
    
    public function index($pattern = "GoodsBilling")
    {
        
        global $SPIRAL;
        try {
            $auth = new Auth();
            
            if($pattern === "GoodsBilling")
            {
                $auth->browseAuthority('ConsumMR');
                
                $sidemenu = [
                'n4' => 'uk-active uk-open',
                'n4_6' => 'uk-active',
                ];
                $title = "消費";
            }
            else if($pattern === "Order")
            {
                $auth->browseAuthority('OrderMR');
                
                $sidemenu = [
                'n4' => 'uk-active uk-open',
                'n4_7' => 'uk-active',
                ];
                $title = "注文";
            }
            else if($pattern === "Payout")
            {
                $auth->browseAuthority('PayoutMR');
                
                $sidemenu = [
                'n4' => 'uk-active uk-open',
                'n4_9' => 'uk-active',
                ];
                $title = "払出";
            }
            else if($pattern === "Receiving")
            {
                $auth->browseAuthority('ReceivingMR');
                
                $sidemenu = [
                'n4' => 'uk-active uk-open',
                'n4_8' => 'uk-active',
                ];
                $title = "入庫";
            }
            
            $step = $SPIRAL->getParam('step');
            $step_check = 1;
            $error = [];
                
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            $hospital = $hospital->data->all();
            
            if($step == "" || $step == "1")
            {
                $step_check = 1;
            }
            
            if($step == "2")
            {
                if($SPIRAL->getParam('hospitalId') == "")
                {
                    $step_check = 1;
                    $error['hospitalId'] = "入力必須です";
                }
                else
                {
                    $hospitalId = $SPIRAL->getParam('hospitalId');
                    $check = false;
                    foreach($hospital as $h)
                    {
                        if($hospitalId === $h->hospitalId)
                        {
                            $check = true;
                        }
                    }
                    if(!$check)
                    {
                        $step_check = 1;
                        $error['hospitalId'] = "値を確認してください";
                    }
                    else 
                    {
                        $step_check = 2;
                    }
                }
            }
            
            $session = $SPIRAL->getSession(true , 3600);
            $content = $this->view('NewJoyPlaTenantAdmin/view/History/HospitalIdSelect', [
                'hospital' => $hospital,
                'current_hospitalId' => $SPIRAL->getParam('hospitalId'),
                'error' => $error,
                'api_url' => "%url/rel:mpgt:MonthlyReport%",
                'Action' => $pattern,
                'title' => $title,
                ] , false)->render();
                
            if($step_check == 2)
            {
                
                if($pattern === "GoodsBilling")
                {
                    $GoodsBillingMRController = new GoodsBillingMRController();
                    $content .= $GoodsBillingMRController->index();
                }
                else if($pattern === "Order")
                {
                    $OrderMRController = new OrderMRController();
                    $content .= $OrderMRController->index();
                }
                else if($pattern === "Payout")
                {
                    $PayoutMRController = new PayoutMRController();
                    $content .= $PayoutMRController->index();
                }
                else if($pattern === "Receiving")
                {
                    $ReceivingMRController = new ReceivingMRController();
                    $content .= $ReceivingMRController->index();
                }
            }
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', $sidemenu , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla 月次レポート',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
            ],false);
        }
    }
}

class InventoryMovementController extends Controller
{
    public function __construct()
    {
    }
    public function index()
    {
        try{
            $auth = new Auth();
            
            $hospital = Hospital::where('tenantId',$auth->tenantId)->value('hospitalName')->value('hospitalId')->plain()->get();
            $hospital = $hospital->data->all();

            $content = $this->view('NewJoyPlaTenantAdmin/view/History/InventoryMovement', [
                'hospital' => $hospital,
                'csrf_token' => Csrf::generate(16),
            ] , false)->render();
            
        } catch ( Exception $ex ) {
                
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $style .= $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/DetailPrintCss', [] , false)->render();

            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n4' => 'uk-active uk-open',
                'n4_12' => 'uk-active',
            ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla 棚卸実績',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
            ],false);
        }
    }
    
    public function divisionSelectApi($SPIRAL)
    {
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $auth = new Auth();

            $hospitalId = $SPIRAL->getParam('hospitalId');

            if($hospitalId === "")
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $hospital = Hospital::where('hospitalId',$hospitalId)->where('tenantId',$auth->tenantId)->value('hospitalName')->value('hospitalId')->plain()->get();
            if($hospital->count == 0)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            $hospital = $hospital->data->get(0);
            
            $division = Division::where('hospitalId',$hospital->hospitalId)
            ->value('divisionId')
            ->value('divisionName')
            ->plain()->get();
            $division = $division->data->all();

            $content = new ApiResponse( $division , count($division) , 0 , "OK", ['divisionSelectApi']);
            $content = $content->toJson();

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['divisionSelectApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
                'content'   => $content,
            ],false);
        }
    }

    
    public function divisonInventorySelectApi($SPIRAL)
    {
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $auth = new Auth();
            
            $hospitalId = $SPIRAL->getParam('hospitalId');
            $divisionId = $SPIRAL->getParam('divisionId');
            if($divisionId === '' || $hospitalId === '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $hospital = Hospital::where('hospitalId',$hospitalId)->where('tenantId',$auth->tenantId)->value('hospitalName')->value('hospitalId')->plain()->get();
            if($hospital->count == 0)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            $hospital = $hospital->data->get(0);
            
            $content = InventoryHistoryDivisionView::where('divisionId',$divisionId)
            ->where('hospitalId',$hospital->hospitalId)
            ->sort('id','desc')
            ->value('inventoryTime')
            ->value('inventoryStatus')
            ->value('inventoryHId')
            ->plain()
            ->get();

            $data = [];

            foreach($content->data->all() as $key => $c)
            {
                $date = $c->inventoryTime;
                if($c->inventoryTime === ''){
                    $date = date("Y年m月d日 H時i分s秒");
                }
                $data[] = [
                    'inventoryTime' => $c->inventoryTime,
                    'inventoryStatus' => $c->inventoryStatus,
                    'inventoryHId' => $c->inventoryHId,
                    'searchStartDate' => '',
                    'searchEndDate' => $date,
                ];
            }

            foreach($data as $key => &$d)
            {
                if(array_key_exists( ($key+1) , $data))
                {
                    $data[$key]['searchStartDate'] = $data[$key+1]['searchEndDate'];
                }
            }

            $content = new ApiResponse($data ,$content->count , 0 , "OK", ['']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
                'content'   => $content,
            ],false);
        }
    }


    public function divisonItemsSelectApi($SPIRAL)
    {
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $auth = new Auth();
            $hospitalId = $SPIRAL->getParam('hospitalId');
            $divisionId = $SPIRAL->getParam('divisionId');
            if($divisionId === '' || $hospitalId === '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $hospital = Hospital::where('hospitalId',$hospitalId)->where('tenantId',$auth->tenantId)->value('hospitalName')->value('hospitalId')->value('invUnitPrice')->plain()->get();
            if($hospital->count == 0)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            $hospital = $hospital->data->get(0);
            
            $content = StockView::where('divisionId',$divisionId)
                ->where('hospitalId',$hospital->hospitalId)
                ->sort('id','desc')
                ->value('inHospitalItemId')
                ->value('rackName')
                ->value('distributorName')
                ->value('itemName')
                ->value('itemCode')
                ->value('itemStandard')
                ->value('itemJANCode')
                ->value('makerName')
                ->value('price')
                ->value('unitPrice')
                ->value('quantity')
                ->value('quantityUnit')
                ->plain()
                ->get();

            $data = $content->data->all();

            foreach($data as &$d)
            {
                $d->price = (int) $d->price ;
                if($hospital->invUnitPrice !== '1')
                {
                    $d->unitPrice = 0;
                    if((int)$d->quantity !== 0 && (int)$d->price !== 0)
                    {
                        $d->unitPrice = ((int)$d->price / (int)$d->quantity);
                    }
                }
            }

            $content = new ApiResponse( $data ,$content->count , 0 , "OK", ['divisonItemsSelectApi']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['divisonItemsSelectApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
                'content'   => $content,
            ],false);
        }
    }

    
    public function getInventoryItemNumsApi($SPIRAL)
    {
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $auth = new Auth();
            $hospitalId = $SPIRAL->getParam('hospitalId');
            $divisionId = $SPIRAL->getParam('divisionId');
            if($divisionId === '' || $hospitalId === '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $hospital = Hospital::where('hospitalId',$hospitalId)->where('tenantId',$auth->tenantId)->value('hospitalName')->value('hospitalId')->plain()->get();
            if($hospital->count == 0)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            $hospital = $hospital->data->get(0);
            
            $inventoryHId = $SPIRAL->getParam('inventoryHId');
            if($inventoryHId === '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $content = Inventory::where('inventoryHId',$inventoryHId)
                ->where('divisionId',$divisionId)
                ->where('hospitalId',$hospital->hospitalId)
                ->sort('id','desc')
                ->value('inHospitalItemId')
                ->value('inventryNum')
                ->plain()
                ->get();

            $data = [];
            $data['record'] = [];
            foreach($content->data->all() as $d)
            {
                $check = array_column($data['record'], 'inHospitalItemId');
                $key = array_search($d->inHospitalItemId, $check);
                if ($key === FALSE)
                {
                    $data['record'][] = [
                        'inHospitalItemId' => $d->inHospitalItemId,
                        'count' => (int)$d->inventryNum
                    ];
                } else {
                    $data['record'][$key]['count'] = $data['record'][$key]['count']  + (int)$d->inventryNum;
                }
            }

            $content = new ApiResponse( $data ,$content->count , 0 , "OK", ['getInventoryItemNumsApi']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['getInventoryItemNumsApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
                'content'   => $content,
            ],false);
        }
    }

    
    public function getBeforeInventoryItemNumsApi($SPIRAL)
    {
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $auth = new Auth();
            $hospitalId = $SPIRAL->getParam('hospitalId');
            $divisionId = $SPIRAL->getParam('divisionId');
            if($divisionId === '' || $hospitalId === '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $hospital = Hospital::where('hospitalId',$hospitalId)->where('tenantId',$auth->tenantId)->value('hospitalName')->value('hospitalId')->plain()->get();
            if($hospital->count == 0)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            $hospital = $hospital->data->get(0);
            
            $inventoryHId = $SPIRAL->getParam('inventoryHId');
            if($inventoryHId === '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $content = InventoryHistoryDivisionView::where('divisionId',$divisionId)
                ->where('hospitalId',$hospital->hospitalId)
                ->where('inventoryHId',$inventoryHId ,'<')
                ->sort('id','desc')
                ->value('inventoryTime')
                ->value('inventoryStatus')
                ->value('inventoryHId')
                ->plain()
                ->get();

            $data = [];
            $data['record'] = [];
            $data['date'] = '';
            if($content->count > 0){

                $before = $content->data->get(0);
                $beforeInventoryHId = $before->inventoryHId;
                $data['date'] = $before->inventoryTime;

                $hospital = Hospital::where('hospitalId', $hospital->hospitalId)->value('invUnitPrice')->plain()->get();
                $hospital = $hospital->data->get(0);
                
                $content = Inventory::where('inventoryHId',$beforeInventoryHId)
                    ->where('divisionId',$divisionId)
                    ->where('hospitalId',$hospital->hospitalId)
                    ->sort('id','desc')
                    ->value('inHospitalItemId')
                    ->value('inventryNum')
                    ->plain()
                    ->get();


                foreach($content->data->all() as $d)
                {
                    $check = array_column($data['record'], 'inHospitalItemId');
                    $key = array_search($d->inHospitalItemId, $check);
                    if ($key === FALSE)
                    {
                        $data['record'][] = [
                            'inHospitalItemId' => $d->inHospitalItemId,
                            'count' => (int)$d->inventryNum,
                        ];
                    } else {
                        $data['record'][$key]['count'] = $data['record'][$key]['count'] + (int)$d->inventryNum;
                    }
                }
            }

            $content = new ApiResponse( $data ,$content->count , 0 , "OK", ['getBeforeInventoryItemNumsApi']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['getBeforeInventoryItemNumsApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
                'content'   => $content,
            ],false);
        }
    }
    
    public function getReceivingItemNumsApi($SPIRAL)
    {
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $auth = new Auth();
            $hospitalId = $SPIRAL->getParam('hospitalId');
            $divisionId = $SPIRAL->getParam('divisionId');
            if($divisionId === '' || $hospitalId === '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $hospital = Hospital::where('hospitalId',$hospitalId)->where('tenantId',$auth->tenantId)->value('hospitalName')->value('hospitalId')->plain()->get();
            if($hospital->count == 0)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            $hospital = $hospital->data->get(0);
            
            $startDate = $SPIRAL->getParam('startDate');
            $endDate= $SPIRAL->getParam('endDate');

            $content = ReceivingView::where('divisionId',$divisionId)
                ->where('hospitalId',$hospital->hospitalId)
                ->sort('id','desc')
                ->value('inHospitalItemId')
                ->value('receivingCount')
                ->value('priceAfterAdj')
                ->value('quantity')
                ->plain();
                
            if($startDate)
            {
                $content = $content->where('registrationTime',urldecode($startDate) ,'>=');
            }
            if($endDate)
            {
                $content = $content->where('registrationTime',urldecode($endDate) ,'<=');
            }

            $content = $content->get();
            $data = [];
            $data['record'] = [];

            if($content->count > 0){

                foreach($content->data->all() as $d)
                {
                    $check = array_column($data['record'], 'inHospitalItemId');
                    $key = array_search($d->inHospitalItemId, $check);
                    if ($key === FALSE)
                    {
                        $data['record'][] = [
                            'inHospitalItemId' => $d->inHospitalItemId,
                            'count' => (int)$d->receivingCount * (int)$d->quantity,
                            'price' => (float)$d->priceAfterAdj,
                        ];
                    } else {
                        $data['record'][$key]['count'] = $data['record'][$key]['count'] + ((int)$d->receivingCount * (int)$d->quantity);
                        $data['record'][$key]['price'] = $data['record'][$key]['price'] + (float)$d->priceAfterAdj;
                    }
                }
            }

            $content = new ApiResponse( $data ,$content->count , 0 , "OK", ['getReceivingItemNumsApi']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['getReceivingItemNumsApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
                'content'   => $content,
            ],false);
        }
    }

    
    public function getConsumedItemNumsApi($SPIRAL)
    {
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            //$stock = Distributor::value('distributorId')->plain()->get();

            $auth = new Auth();
            $hospitalId = $SPIRAL->getParam('hospitalId');
            $divisionId = $SPIRAL->getParam('divisionId');
            if($divisionId === '' || $hospitalId === '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $hospital = Hospital::where('hospitalId',$hospitalId)->where('tenantId',$auth->tenantId)->value('hospitalName')->value('hospitalId')->plain()->get();
            if($hospital->count == 0)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            $hospital = $hospital->data->get(0);
            
            $startDate = $SPIRAL->getParam('startDate');
            $endDate= $SPIRAL->getParam('endDate');

            if($divisionId === '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $content = Billing::where('divisionId',$divisionId)
                ->where('hospitalId',$hospital->hospitalId)
                ->sort('id','desc')
                ->value('inHospitalItemId')
                ->value('billingQuantity')
                ->value('billingAmount')
                ->value('quantity')
                ->plain();
                
            if($startDate)
            {
                $content = $content->where('registrationTime',urldecode($startDate) ,'>=');
            }
            if($endDate)
            {
                $content = $content->where('registrationTime',urldecode($endDate) ,'<=');
            }

            $content = $content->get();
            $data = [];
            $data['record'] = [];
            if($content->count > 0){

                foreach($content->data->all() as $d)
                {
                    $check = array_column($data['record'], 'inHospitalItemId');
                    $key = array_search($d->inHospitalItemId, $check);
                    if ($key === FALSE)
                    {
                        $data['record'][] = [
                            'inHospitalItemId' => $d->inHospitalItemId,
                            'count' => (int)$d->billingQuantity,
                            'price' => (float)$d->billingAmount,
                        ];
                    } else {
                        $data['record'][$key]['count'] = $data['record'][$key]['count'] + (int)$d->billingQuantity;
                        $data['record'][$key]['price'] = $data['record'][$key]['price'] + (float)$d->billingAmount;
                    }
                }
            }

            $content = new ApiResponse( $data ,$content->count , 0 , "OK", ['getConsumedItemNumsApi']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['getConsumedItemNumsApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
                'content'   => $content,
            ],false);
        }
    }
    
    public function InventoryMovementAllDataDl($SPIRAL)
    {
        $auth = new Auth();
        $hospital = Hospital::where('tenantId',$auth->tenantId)->plain()->get();
        $stock = StockView::plain();
        
        if($hospital->count == 0)
        {
            throw new Exception('no data',404);
        }
        
        foreach($hospital->data->all() as $h)
        {
            $stock->orWhere('hospitalId',$h->hospitalId);
        }

        $stock = $stock->get();

    }
}

require_once "NewJoyPlaTenantAdmin/controller/GoodsBillingMRController.php";
require_once "NewJoyPlaTenantAdmin/controller/OrderMRController.php";
require_once "NewJoyPlaTenantAdmin/controller/ReceivingMRController.php";
require_once "NewJoyPlaTenantAdmin/controller/PayoutMRController.php";
/***
 * 実行
 */
$InventoryMovementController = new InventoryMovementController();
$MonthlyReportController = new MonthlyReportController();       
$action = $SPIRAL->getParam('Action');

{
    if($action === "InventoryMovement")
    {
        echo $InventoryMovementController->index()->render();

    }
    else if($action === 'divisionSelectApi')
    {
        echo $InventoryMovementController->divisionSelectApi($SPIRAL)->render();
    }
    else if($action === 'divisonInventorySelectApi')
    {
        echo $InventoryMovementController->divisonInventorySelectApi($SPIRAL)->render();
    }
    else if($action === 'divisonItemsSelectApi')
    {
        echo $InventoryMovementController->divisonItemsSelectApi($SPIRAL)->render();
    }
    else if($action === 'getInventoryItemNumsApi')
    {
        echo $InventoryMovementController->getInventoryItemNumsApi($SPIRAL)->render();
    }
    else if($action === 'getBeforeInventoryItemNumsApi')
    {
        echo $InventoryMovementController->getBeforeInventoryItemNumsApi($SPIRAL)->render();
    }
    else if($action === 'getReceivingItemNumsApi')
    {
        echo $InventoryMovementController->getReceivingItemNumsApi($SPIRAL)->render();
    }
    else if($action === 'getConsumedItemNumsApi')
    {
        echo $InventoryMovementController->getConsumedItemNumsApi($SPIRAL)->render();
    }
    else if($action === 'InventoryMovementAllDataDl')
    {
        //echo $InventoryMovementController->InventoryMovementAllDataDl($SPIRAL);
    }
    else
    { 
        echo $MonthlyReportController->index($action)->render();
    }
}
