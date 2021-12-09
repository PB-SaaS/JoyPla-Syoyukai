<?php
namespace App\Controller;

use Controller;
use Csrf;
use ApiResponse;
use App\Lib\Auth;
use App\Model\Tenant;
use App\Model\ItemBulkUpsertTrDB;

use Validate\itemDB;

use stdClass;
use Exception;

class BlukUpsertItemsController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            $auth->browseAuthority('ItemBulkUpsert');
                
            $api_url = "%url/rel:mpgt:BulkItem%";
            $error = $SPIRAL->getParam('errorMsg');
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/BlukUpsertItems/Index', [
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
                'n3_2' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 一括商品登録更新',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function regist()
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
                        "makerName" => $rows['data'][0],
                        "itemName" => $rows['data'][1],
                        "category" => $rows['data'][2],
                        "itemCode" => $rows['data'][3],
                        "itemStandard" => $rows['data'][4],
                        "itemJANCode" => $rows['data'][5],
                        "catalogNo" => $rows['data'][6],
                        "serialNo" => $rows['data'][7],
                        "minPrice" => $rows['data'][8],
                        "officialFlag" => $rows['data'][9],
                        "officialprice" => $rows['data'][10],
                        "quantity" => $rows['data'][11],
                        "quantityUnit" => $rows['data'][12],
                        "itemUnit" => $rows['data'][13],
                        "lotManagement" => $rows['data'][14],
                        "tenantId" => $auth->tenantId,
                    ];
            }
            
            $result = ItemBulkUpsertTrDB::insert($insert_data);
        
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
    
    public function validateCheckApi()
    {
        global $SPIRAL;
        
        $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
        Csrf::validate($token,true);
        
        $target = new itemDB();
        $content =  json_encode(array_map(function($t) { return $t->getValue(); }, $target->getTryDbFieldList()->getFailedObjects()), JSON_UNESCAPED_UNICODE);
        return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
            'content'   => $content,
        ],false);
    }
    
    public function logsList()
    {
        global $SPIRAL;
        try {
            $api_url = "%url/rel:mpgt:BulkItem%";
            $error = $SPIRAL->getParam('errorMsg');
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/BlukUpsertItems/LogsList', [
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
                'n3_3' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla-Tenant-Master 商品登録更新履歴',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
}

/***
 * 実行
 */
$BlukUpsertItemsController = new BlukUpsertItemsController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'validateCheckApi')
    {
        echo $BlukUpsertItemsController->validateCheckApi()->render();
    }
    else if($action === 'regist')
    {
        echo $BlukUpsertItemsController->regist()->render();
    }
    else if($action === 'logsList')
    {
        echo $BlukUpsertItemsController->logsList()->render();
    }
    else
    {
        echo $BlukUpsertItemsController->index()->render();
    }
}