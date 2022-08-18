<?php
namespace App\Controller;

use Controller;
use Csrf;
use ApiResponse;
use App\Lib\Auth;
use App\Model\Tenant;
use App\Model\Hospital;
use App\Model\DistributorAndHospitalDB;
use App\Model\Division;
use App\Model\Card;
use App\Model\InHospitalItem;

use Validate\DistributorDB;
use Validate\CardDB;

use stdClass;
use Exception;

class StocksController extends Controller
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
            $content = $this->view('NewJoyPlaTenantAdmin/view/Stocks/Index', [
                //'error' => $error,
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
                'n5' => 'uk-active uk-open',
                'n5_1' => 'uk-active'
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla 在庫管理',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function Lots()
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
            $content = $this->view('NewJoyPlaTenantAdmin/view/Stocks/Lots', [
                //'error' => $error,
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [
                'select_hospital'=>$select_hospital,
                ] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n5' => 'uk-active uk-open',
                'n5_2' => 'uk-active'
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla ロット管理',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function Cards()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();
            
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            $select_hospital = [['text'=> '----- 選択してください -----' ,'value'=> '' ]];
            foreach($hospital->data->all() as $h)
            {
                $select_hospital[] = ['text'=> $h->hospitalName ,'value'=> $h->hospitalName ];
            }
            $content = $this->view('NewJoyPlaTenantAdmin/view/Stocks/Cards', [
                //'error' => $error,
                ] , false)->render();
            
        } catch ( Exception $ex ) {
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false)->render();
            
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [
                'select_hospital'=>$select_hospital,
                ] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n5' => 'uk-active uk-open',
                'n5_3' => 'uk-active'
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla カード情報管理',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }

    public function CardsBulkInsert()
    {
        global $SPIRAL;
        try {
            $auth = new Auth();

            $api_url = "%url/rel:mpgt:Stocks%";
            $error = $SPIRAL->getParam('errorMsg');
            
            $hospital = Hospital::where('tenantId',$auth->tenantId)->get();
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/BulkInsertCards/Index', [
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
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [
                'select_hospital'=>$select_hospital,
                ] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n5' => 'uk-active uk-open',
                'n5_4' => 'uk-active'
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'title'     => 'JoyPla カード情報一括登録',
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
        
        $target = new CardDB();
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
        
        $division = Division::where('hospitalId',$_POST['hospitalId'])->whereDeleted();
        $inHospitalItem = InHospitalItem::where('hospitalId',$_POST['hospitalId']);
        
        foreach($rowData as $row)
        {
            $division->orWhere('divisionId',$row['data'][0]);
            $inHospitalItem->orWhere('inHospitalItemId',$row['data'][1]);
        }
        
        $division = $division->get();
        
        $inHospitalItem = $inHospitalItem->get();
        
        foreach($rowData as $row)
        {
            $check = false;
            foreach($division->data->all() as $d )
            {
                if($d->divisionId === $row['data'][0])
                {
                    $check = true;
                }
            }
            if(!$check)
            {
                $messages[] = ((int)$row['index'] + 1) . "行目の部署ID：存在しません";
            }

            $check = false;
            foreach($inHospitalItem->data->all() as $d )
            {
                if($d->inHospitalItemId === $row['data'][1])
                {
                    $check = true;
                }
            }
            if(!$check)
            {
                $messages[] = ((int)$row['index'] + 1) . "行目の院内商品ID：存在しません";
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
            //$rowData =  $SPIRAL->getParam('rowData');
            
            $insert_data = [];
            $ids = [];
            foreach($rowData as $key => $rows)
            {
                $id = $this->makeIds('90' , $key );
                if(in_array($id , $ids, true) === true){ //var_dump($id); 
                }
                $ids[] = $id;
                $insert_data[] = 
                    [
                        "cardId" => $id,
                        "hospitalId" => $SPIRAL->getParam('hospitalId'),
                        "divisionId" => $rows['data'][0],
                        "inHospitalItemId" => $rows['data'][1],
                        "quantity" => $rows['data'][2]
                    ];
            }
            $result = Card::insert($insert_data);
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

    function makeIds($id = '00' ,  $key)
    { 
        $id .= date("ymdHis");
        $id .= str_pad($key , 4, "0", STR_PAD_LEFT); 
        //if(in_array($id , $ids, true)){ $this->makeId('90' , $ids); }
        return $id;
    }
    
}

/***
 * 実行
 */
$StocksController = new StocksController();

$action = $SPIRAL->getParam('Action');

{
    if($action === "Lots")
    {
        echo $StocksController->Lots()->render();
    }
    else if($action === "Cards")
    {
        echo $StocksController->Cards()->render();
    }
    else if($action === "CardsBulkInsert")
    {
        echo $StocksController->CardsBulkInsert()->render();
    }
    else if($action === "bulkInsertValidateCheckApi")
    {
        echo $StocksController->bulkInsertValidateCheckApi()->render();
    }
    else if($action === "bulkInsertValidateCheck2Api")
    {
        echo $StocksController->bulkInsertValidateCheck2Api()->render();
    }
    else if($action === "bulkInsertApi")
    {
        echo $StocksController->bulkInsertApi()->render();
    }
    else
    {
        echo $StocksController->index()->render();
    }
}