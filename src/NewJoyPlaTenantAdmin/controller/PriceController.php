<?php
namespace App\Controller;

use Controller;
use Csrf;
use ApiResponse;
use App\Lib\Auth;
use App\Model\Tenant;
use App\Model\Hospital;
use App\Model\Distributor;
use App\Model\DistributorAndHospitalDB;
use App\Model\Item;
use App\Model\Price;
use App\Model\PriceUpsertTrDB;

use Validate\PriceTrDB;

use stdClass;
use Exception;

class PriceController extends Controller
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
            
            $session = $SPIRAL->getSession(true , 3600);
            $session->remove('back_url');
            $error = $SPIRAL->getParam('errorMsg');
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Price/Index', [
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
                'n3_4' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 金額管理',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function bulkUpsert()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('PriceBulkUpsert');
                
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            $hospital = $hospital->data->all();
            
            $session = $SPIRAL->getSession(true , 3600);
            $session->remove('back_url');
            $error = $SPIRAL->getParam('errorMsg');
            
            $api_url = "%url/rel:mpgt:PriceCont%";
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Price/BulkUpsert', [
                'error' => $error,
                'api_url' => $api_url,
                'hospital' => $hospital,
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
                'n3_5' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 金額情報一括登録',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    
    public function bulkUpsertValidateCheckApi()
    {
        global $SPIRAL;
        
        $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
        Csrf::validate($token,true);
        
        $target = new PriceTrDB();
        $content =  json_encode(array_map(function($t) { return $t->getValue(); }, $target->getTryDbFieldList()->getFailedObjects()), JSON_UNESCAPED_UNICODE);
        return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
            'content'   => $content,
        ],false);
    }
    
    public function bulkUpsertDistributorIdValidateCheckApi()
    {
        global $SPIRAL;
        $auth = new Auth();
        
        $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
        Csrf::validate($token,true);
        
        
        $distributor = Distributor::where('hospitalId',$_POST['hospitalId']);
        $item = Item::where('tenantId',$auth->tenantId);
        $price = Price::where('hospitalId',$_POST['hospitalId']);
        
        $rowData = $this->requestUrldecode($SPIRAL->getParam('rowData'));
        $messages = [];
        
        foreach ( $rowData as $row )
        {
            $distributor->orWhere('distributorId',$row['data'][1]);
            $item->orWhere('itemId',$row['data'][2]);
            if($row['data'][0] !== "")
            {
                $price->orWhere('priceId',$row['data'][0]);
            }
        }
        
        $distributor = $distributor->get();
        $item = $item->get();
        $price = $price->get();
        
        foreach ( $rowData as $row )
        {
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
            
            $check = false;
            foreach($item->data->all() as $i )
            {
                if($i->itemId === $row['data'][2])
                {
                    $check = true;
                }
            }
            if(!$check)
            {
                $messages[] = ((int)$row['index'] + 1) . "行目の商品ID：存在しません";
            }
            
            if($row['data'][0] !== "")
            {
                $exist = false;
                foreach($price->data->all() as $p )
                {
                    if($p->priceId === $row['data'][0])
                    {
                        $exist = true;
                        $change = false;
                        if($p->distributorId === $row['data'][1])
                        {
                            $change = true;
                        }
                        if(!$change)
                        {
                            $messages[] = ((int)$row['index'] + 1) . "行目の金額管理ID：卸業者IDは変更できません";
                        }
                    }
                }
                if(!$exist)
                {
                    $messages[] = ((int)$row['index'] + 1) . "行目の金額管理ID：存在しません";
                }
            }
        }
        
        $content = json_encode($messages);
        return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
            'content'   => $content,
        ],false);
    }
    
    public function bulkUpsertApi()
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
                        "priceId" => $rows['data'][0],
                        "distributorId" => $rows['data'][1],
                        "itemId" => $rows['data'][2],
                        "hospitalId" => $_POST['hospitalId'],
                        "quantity" => $rows['data'][3],
                        "quantityUnit" => $rows['data'][4],
                        "itemUnit" => $rows['data'][5],
                        "price" => $rows['data'][6],
                        "notice" => $rows['data'][7],
                    ];
            }
            $result = PriceUpsertTrDB::insert($insert_data);
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
$PriceController = new PriceController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "bulkUpsert")
    {
        echo $PriceController->bulkUpsert()->render();
    }
    else if($action === "bulkUpsertValidateCheckApi")
    {
        echo $PriceController->bulkUpsertValidateCheckApi()->render();
    }
    else if($action === "bulkUpsertDistributorIdValidateCheckApi")
    {
        echo $PriceController->bulkUpsertDistributorIdValidateCheckApi()->render();
    }
    else if($action === "bulkUpsertApi")
    {
        echo $PriceController->bulkUpsertApi()->render();
    }
    else
    {
        echo $PriceController->index()->render();
    }
}