<?php
namespace App\Controller;

use Controller;
use Csrf;
use ApiResponse;

use App\Lib\Auth;
use App\Model\Hospital;
use App\Model\Distributor;
use App\Model\DistributorAndHospitalDB;
use App\Model\Item;
use App\Model\InHospitalTrDb;


use Validate\InHospitalNewInsertDb;

use stdClass;
use Exception;

class InHospitalItemController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            
            $auth = new Auth();
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            $distributor = DistributorAndHospitalDB::where('tenantId',$auth->tenantId)->get();
            $select_hospital = [['text'=> '----- 選択してください -----' ,'value'=> '' ]];
            foreach($hospital->data->all() as $h)
            {
                $select_hospital[] = ['text'=> $h->hospitalName ,'value'=> $h->hospitalName ];
            }
            $select_distributor = [['text'=> '----- 選択してください -----' ,'value'=> '' ]];
            foreach($distributor->data->all() as $d)
            {
                $select_distributor[] = ['text'=> $d->distributorName ,'value'=> $d->distributorName ];
            }
            $error = $SPIRAL->getParam('errorMsg');
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/InHospitalItem/Index', [
                'error' => $error,
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [
                'select_hospital'=>$select_hospital,
                'select_distributor'=>$select_distributor,
                ] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n3' => 'uk-active uk-open',
                'n3_6' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 院内商品管理',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
            
        }
    }
    
    public function bulkInsert()
    {
        global $SPIRAL;
        try {
            $error = $SPIRAL->getParam('errorMsg');
            
            $auth = new Auth();
            $auth->browseAuthority('PriceAndInHospitalItemBulkInsert');
            
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            $api_url = "%url/rel:mpgt:InHospitalItem%";
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/InHospitalItem/BulkInsert', [
                'hospital' => $hospital->data->all(),
                'error' => $error,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16)
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n3' => 'uk-active uk-open',
                'n3_7' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 金額・院内商品一括登録',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
            
        }
    }
    
    public function bulkInsertValidateCheckApi()
    {
        global $SPIRAL;
        
        $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
        Csrf::validate($token,true);
        
        $target = new InHospitalNewInsertDb();
        $content =  json_encode(array_map(function($t) { return $t->getValue(); }, $target->getTryDbFieldList()->getFailedObjects()), JSON_UNESCAPED_UNICODE);
        return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
            'content'   => $content,
        ],false);
    }
    
    public function bulkInsertValidateCheck2Api()
    {
        global $SPIRAL;
        $auth = new Auth();
        
        $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
        Csrf::validate($token,true);
        
        $rowData = $this->requestUrldecode($SPIRAL->getParam('rowData'));
        $messages = [];
        
        $item = Item::where('tenantId',$auth->tenantId);
        $distributor = Distributor::where('hospitalId',$_POST['hospitalId']);
        
        foreach($rowData as $row)
        {
            $item->orWhere('itemId',$row['data'][0]);
            $distributor->orWhere('distributorId',$row['data'][1]);
        }
        
        $distributor = $distributor->get();
        $item = $item->get();
        
        foreach($rowData as $row)
        {
            $check = false;
            foreach($item->data->all() as $i )
            {
                if($i->itemId === $row['data'][0])
                {
                    $check = true;
                }
            }
            if(!$check)
            {
                $messages[] = ((int)$row['index'] + 1) . "行目の商品ID：存在しません";
            }
            
            $check = false;
            foreach($distributor->data->all() as $d )
            {
                if($d->distributorId === $row['data'][1])
                {
                    $check = true;
                }
            }
            if(!$check)
            {
                $messages[] = ((int)$row['index'] + 1) . "行目の卸業者ID：存在しません";
            }
        }
        
        
        $content = json_encode($messages);
        return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
            'content'   => $content,
        ],false);
    }
    
    
    public function bulkInsertApi()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            
            $auth = new Auth();
            
            $rowData = $this->requestUrldecode($SPIRAL->getParam('rowData'));
            
            $insert_data = [];
            foreach($rowData as $rows)
            {
                $insert_data[] = 
                    [
                        "hospitalId" => $_POST['hospitalId'],
                        "itemId" => $rows['data'][0],
                        "distributorId" => $rows['data'][1],
                        "notUsedFlag" => $rows['data'][2],
                        "minPrice" => $rows['data'][3],
                        "unitPrice" => $rows['data'][4],
                        "measuringInst" => $rows['data'][5],
                        "quantity" => $rows['data'][6],
                        "quantityUnit" => $rows['data'][7],
                        "itemUnit" => $rows['data'][8],
                        "price" => $rows['data'][9],
                        "notice" => $rows['data'][10],
                    ];
            }
            $result = InHospitalTrDb::insert($insert_data);
            $content = new ApiResponse($result->ids , count($insert_data) , $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
}

/***
 * 実行
 */
$InHospitalItemController = new InHospitalItemController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "bulkInsert")
    {
        echo $InHospitalItemController->bulkInsert()->render();
    }
    else if($action === "bulkInsertValidateCheckApi")
    {
        echo $InHospitalItemController->bulkInsertValidateCheckApi()->render();
    }
    else if($action === "bulkInsertValidateCheck2Api")
    {
        echo $InHospitalItemController->bulkInsertValidateCheck2Api()->render();
    }
    else if($action === "bulkInsertApi")
    {
        echo $InHospitalItemController->bulkInsertApi()->render();
    }
    else 
    {
        echo $InHospitalItemController->index()->render();
    }
}