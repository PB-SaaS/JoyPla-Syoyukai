<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Item;
use App\Model\Hospital;

use ApiErrorCode\FactoryApiErrorCode;
use StdClass;
use Exception;

class ItemsListForReferenceController extends Controller
{
    private $category = [
        '1' => ['label' => '医療材料'],
        '2' => ['label' => '薬剤'],
        '3' => ['label' => '試薬'],
        '4' => ['label' => '日用品'],
        '99' => ['label' => 'その他']
    ];

    public function __construct()
    {
    }

    public function index(): View
    {
        global $SPIRAL;
        try{
            $user_info = new UserInfo($SPIRAL);
            $keyword = '%sf:usr:search40%';
            $api_url = '%url/rel:mpgt:page_177993%';
            $content = $this->view('NewJoyPla/view/ItemsListForReference', [
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'page_title' => '商品情報一覧',
                'keyword' => $keyword,
                ] , false);
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            
            $head   = $this->view('NewJoyPla/view/template/parts/Head', [
                'new' => true
                ] , false);
            /*
            $header = $this->view('NewJoyPla/view/template/parts/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            */
            $style   = $this->view('NewJoyPla/view/template/parts/LabelsCss', [] , false)->render();
                
            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [] , false)->render();
                
            $script   = $this->view('NewJoyPla/view/template/parts/Script', [] , false)->render();
                
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 商品情報一覧',
                'script' => $script,
                'style' => $style,
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => '',
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function searchApi()
    {
        global $SPIRAL;

        try {
            $user_info = new UserInfo($SPIRAL);
    
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $itemId = $SPIRAL->getParam('itemId');
            if($itemId == '' || $itemId == null)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $item = Item::where('itemId',$itemId)->where('tenantId', $user_info->getTenantId())->get();    
            
            if($item->count == '0'){
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $result = $item->data->get(0);
        
            $data = [
                'id' => $result->id,
                'itemId' => $result->itemId,
                'maker' => $result->makerName,
                'category' => $this->category[$result->category]['label'],
                'itemName' => $result->itemName,
                'itemCode' => $result->itemCode,
                'itemStandard' => $result->itemStandard,
                'itemJANCode' => $result->itemJANCode,
                'quantity' => $result->quantity,
                'quantityUnit' => $result->quantityUnit,
                'itemUnit' => $result->itemUnit,
                'lotFlag' => ($result->lotManagement == 1 ) ? "はい": "",
                'lotFlagBool' => $result->lotManagement
            ];

            $content = new ApiResponse($data, $item->count , $item->code, $item->message, ['searchApi']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['searchApi']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
}

$ItemsListForReferenceController = new ItemsListForReferenceController();


$action = $SPIRAL->getParam('Action');

{
    if($action === 'searchApi')
    {
        echo $ItemsListForReferenceController->searchApi()->render();
    } 
    else 
    {
        echo $ItemsListForReferenceController->index()->render();
    }
}