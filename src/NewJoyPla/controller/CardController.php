<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Division;
use App\Model\Card;
use App\Model\CardInfoView;
use App\Model\Hospital;
use App\Model\InHospitalItem;
use App\Model\Stock;

use ApiErrorCode\FactoryApiErrorCode;
use StdClass;
use Exception;

class CardController extends Controller
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
            
            if( ($user_info->isHospitalUser() && $user_info->getUserPermission() == '1')) 
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            } 
            else 
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }
    
            $api_url = "%url/rel:mpgt:Card%";
    
            
            $content = $this->view('NewJoyPla/view/CardContent', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'division'=> $division,
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
                'title'     => 'JoyPla カード内容入力',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
	}
	
	public function cardRegistrationApi()
	{
	    global $SPIRAL;
	    
	    try {
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            
            $user_info = new UserInfo($SPIRAL);
	        
	        $card_items = $SPIRAL->getParam('cardItems');
	        
	        $divisionId = $SPIRAL->getParam('divisionId');
	        
	        if($card_items == "" || $divisionId == "")
	        {
	            //TODO そのうち考える
	            throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
	        }
	        $insert_data = [];
            foreach($card_items as $key =>  $item)
            {
                $insert_data[] = [
                    'cardId' => $this->makeId('90'), //18
                    'inHospitalItemId' => $item['recordId'],
                    'hospitalId' => $user_info->getHospitalId(),
                    'quantity' => $item['countNum'],
                    'lotNumber' => $item['lotNumber'],
                    'lotDate' => $item['lotDate'],
                    'divisionId' => $divisionId,
                ];
            }
            
            $result = Card::insert($insert_data);
            
            $content = new ApiResponse($result->ids , $result->count , $result->code, $result->message, ['insert']);
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
	
	
	public function cardLabelPrint()
	{
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
	        $card_ids = $SPIRAL->getParam('card_ids');
	        
	        if($card_ids == "")
	        {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
	        }
	        
	        CardInfoView::where('hospitalId',$user_info->getHospitalId());
	        foreach($card_ids as $id)
	        {
	            CardInfoView::orWhere('id',$id);
	        }
	        
	        $card_items = CardInfoView::get();
	        $card_items = $card_items->data->all();
	        
	        Stock::where('hospitalId',$user_info->getHospitalId());
	        
	        foreach($card_items as $item)
	        {
	            Stock::orWhere('inHospitalItemId',$item->inHospitalItemId);
	        }
	        
	        $stock_items        = Stock::get();
	        
	        foreach($card_items as $key => $item)
	        {
	            foreach($stock_items->data->all() as $stock_item)
	            {
	                if($stock_item->inHospitalItemId == $item->inHospitalItemId && $stock_item->divisionId == $item->divisionId)
	                {
	                    $card_items[$key]->rackName = $stock_item->rackName;
	                    $card_items[$key]->constantByDiv = $stock_item->constantByDiv;
	                }
	            }
	        }
            
            $default_design = $this->defaultDesign();
            
            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
        
            if($hospital_data->labelDesign2 != '')
            {
                $default_design = htmlspecialchars_decode($hospital_data->labelDesign1);
            }
     
            $content = $this->view('NewJoyPla/view/CardLabel', [
                'default_design' => $default_design,
                'hospital_data' => $hospital_data,
                'card_items' => $card_items,
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
                
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            $style   = $this->view('NewJoyPla/view/template/parts/LabelsCss', [] , false)->render();
                
            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [] , false)->render();
                
            $script   = $this->view('NewJoyPla/view/template/parts/Script', [] , false)->render();
                
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla カード発行',
                'script' => $script,
                'style' => $style,
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
	}
	
	public function cardList()
	{
        global $SPIRAL;
        try {
            $api_url = "%url/rel:mpgt:Card%";
            
            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $content = $this->view('NewJoyPla/view/CardList', [
                'api_url' => $api_url
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
                
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            $style   = $this->view('NewJoyPla/view/template/parts/LabelsCss', [] , false)->render();
                
            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [] , false)->render();
                
            $script   = $this->view('NewJoyPla/view/template/parts/Script', [] , false)->render();
                
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla カード一覧',
                'script' => $script,
                'style' => $style,
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
	}
	
	private function defaultDesign()
	{
	    return <<<EOM
        	<div class="printarea uk-margin-remove">
        		<span>%JoyPla:distributorName%</span><br>
        		<span>メーカー名：%JoyPla:itemMaker%</span><br>
        		<span>商品名：%JoyPla:itemName%</span><br>
        		<span>規格：%JoyPla:itemStandard%</span><br>
        		<span>商品コード：%JoyPla:itemCode%</span>
        		<span>入数：%JoyPla:quantity%%JoyPla:quantityUnit%</span><br>
        		<span>%JoyPla:nowTime%</span><br>
        		<div class="uk-text-center" id="barcode_%JoyPla:num%">%JoyPla:barcodeId%</div>
        	</div>
EOM;
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
		$id .= date("ymdHis"); //YYMMDDHHIISS 12
		$id .= str_pad(substr(rand(),0,3) , 4, "0"); // 4
		
		return $id; // 18
    }
}

$CardController = new CardController();

$action = $SPIRAL->getParam('Action');

{
    if($action == 'cardRegistrationApi')
    {
        echo $CardController->cardRegistrationApi()->render();  
    } 
    else if($action == 'cardLabelPrint')
    {
        echo $CardController->cardLabelPrint()->render();  
    }
    else if($action == 'cardList')
    {
        echo $CardController->cardList()->render();  
    }
    else 
    {
        echo $CardController->index()->render();  
    }
}