<?php
namespace App\Controller;

use Controller;
use Csrf;
use ApiResponse;
use App\Lib\Auth;
use App\Model\Tenant;
use App\Model\Hospital;
use App\Model\Distributor;
use App\Model\AllNewItemInsert;
use App\Model\Item;

use Validate\AllNewItemInsertDB;
use stdClass;
use Exception;

class GoodsController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $error = $SPIRAL->getParam('errorMsg');
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Goods/Index', [
                'error' => $error,
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
                'n3_1' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla 商品管理',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
            
        }
    }
    
    public function insert()
    {
        global $SPIRAL;
        try {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/IframeContent', [
                'title' => '商品情報登録',
                'width' => '100%',
                'height'=> '100%',
                'url' => '/regist/is',
                'hiddens' => [
                        'SMPFORM'=> '%smpform:T_itemReg%',
                        'tenantId' => '%val:usr:tenantId%',
                    ]
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false)->render();
        } finally {
            
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n3' => 'uk-active uk-open',
                'n3_8' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla 商品情報登録',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    
    public function allBulkInsert()
    {
        global $SPIRAL;
        try {
            $api_url = "%url/rel:mpgt:Goods%";
            $error = $SPIRAL->getParam('errorMsg');
            
            $auth = new Auth();
            $auth->browseAuthority('NewFacilityItemsBulkInsert');
            
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Goods/AllBulkInsert', [
                'api_url' => $api_url,
                'hospital' => $hospital->data->all(),
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
                'n3_9' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla 新規導入用一括登録',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function bulkInsertApi()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            
            $auth = new Auth();
            
            $rowData = $this->requestUrldecode($SPIRAL->getParam('rowData'));
            //$rowData = $SPIRAL->getParam('rowData');
            
            $insert_data = [];
            foreach($rowData as $row)
            {
                $insert_data[] = 
                    [
                        'distributorId' => $row['data'][0],
                        'itemName' => $row['data'][1],
                        'category' => $row['data'][2],
                        'category' => $row['data'][3],
                        'itemCode' => $row['data'][4],
                        'itemStandard' => $row['data'][5],
                        'itemJANCode' => $row['data'][6],
                        'makerName' => $row['data'][7],
                        'lotManagement' => $row['data'][8],
                        'officialFlag' => $row['data'][9],
                        'officialprice' => $row['data'][10],
                        'catalogNo' => $row['data'][11],
                        'serialNo' => $row['data'][12],
                        'medicineCategory' => $row['data'][13],
                        'homeCategory' => $row['data'][14],
                        'quantity' => $row['data'][15],
                        'quantityUnit' => $row['data'][16],
                        'itemUnit' => $row['data'][17],
                        'price' => $row['data'][18],
                        'minPrice' => $row['data'][19],
                        'unitPrice' => $row['data'][20],
                        'measuringInst' => $row['data'][21],
                        'notice' => $row['data'][22],
                        'hospitalId' => $SPIRAL->getParam('hospitalId'),
                        'tenantId' => $auth->tenantId,
                    ];
            }
            $result = AllNewItemInsert::insert($insert_data);
        
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
    
    public function bulkInsertValidateCheckApi()
    {
        global $SPIRAL;
        
        $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
        Csrf::validate($token,true);
        
        $target = new AllNewItemInsertDB();
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
        //$rowData = $SPIRAL->getParam('rowData');
        $messages = [];
        
        $distributor = Distributor::where('hospitalId',$_POST['hospitalId']);
        $item = Item::where('tenantId',$auth->tenantId);
        
        foreach($rowData as $row)
        {
            $distributor->orWhere('distributorId',$row['data'][0]);
            $item->orWhere('itemJANCode',$row['data'][5]);
        }
        
        $distributor = $distributor->get();
        $item = $item->get();
        
        foreach($rowData as $row)
        {
            $check = false;
            foreach($distributor->data->all() as $d )
            {
                if($d->distributorId === $row['data'][0])
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
                if($i->itemJANCode === $row['data'][5])
                {
                    $check = true;
                }
            }
            if($check)
            {
                $messages[] = ((int)$row['index'] + 1) . "行目のJANコード：すでに存在します";
            }
        }
        
        $content = json_encode($messages);
        return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
            'content'   => $content,
        ],false);
    }
    
}

/***
 * 実行
 */
$GoodsController = new GoodsController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "insert")
    {
        echo $GoodsController->insert()->render();
    }
    else if($action === "allBulkInsert")
    {
        echo $GoodsController->allBulkInsert()->render();
    }
    else if($action === "bulkInsertValidateCheckApi")
    {
        echo $GoodsController->bulkInsertValidateCheckApi()->render();
    }
    else if($action === "bulkInsertValidateCheck2Api")
    {
        echo $GoodsController->bulkInsertValidateCheck2Api()->render();
    }
    else if($action === "bulkInsertApi")
    {
        echo $GoodsController->bulkInsertApi()->render();
    }
    else
    {
        echo $GoodsController->index()->render();
    }
}