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
use App\Model\InHospitalItem;
use App\Model\StockView;

use ApiErrorCode\FactoryApiErrorCode;
use StdClass;
use Exception;

class CreateLabelController extends Controller
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
            
            $labelPrint = true;
            $pattern = '';
            $inHpItemMaster = array();
            $nowTime = date('Y年m月d日 H時i分s秒');
            $api_url = "%url/rel:mpgt:Card%";
            $title = '払出';
            
            $pattern = $SPIRAL->getParam('pattern');
            $itemsData = $SPIRAL->getParam('itemsData');
            $itemsData = $this->requestUrldecode($itemsData);
            $sourceDivision = $this->sanitize($SPIRAL->getParam('sourceDivision'));
            $targetDivision = $this->sanitize($SPIRAL->getParam('targetDivision'));
            
            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
            
            $defaultDesign = $hospital->labelDesign2;
            $defaultDesign = $this->design();
            if($hospital->labelDesign2 != '')
            {
                $defaultDesign = htmlspecialchars_decode($hospital->labelDesign2);
            }
            
            $inHospitalItem = InHospitalItem::where('hospitalId',$user_info->getHospitalId());
            
            foreach($itemsData as $inHPId => $item){
                $inHospitalItem->orWhere('inHospitalItemId',$inHPId);
            }
            
            $inHospitalItem = $inHospitalItem->get();
            $inHospitalItem = $inHospitalItem->data->all();
            
            $inHpItemMaster = [];
            foreach($inHospitalItem as $item)
            {
                $inHpItemMaster[$item->inHospitalItemId] = (array)$item;
            }
            
            if($sourceDivision !== "")
            {
                $sourceStock = StockView::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$sourceDivision);
                foreach($itemsData as $inHPId => $item){
                    $sourceStock->orWhere('inHospitalItemId',$inHPId);
                }
                $sourceStock = $sourceStock->get();
                
                if($sourceStock->count === "0")
                {
                    foreach($itemsData as $inHPId => $item){
                        $division = Division::where('divisionId',$sourceDivision)->get();
                        $division = $division->data->get(0);
                		$itemsData[ $inHPId ]['sourceDivisionName'] = $division->divisionName;
                    }
                }
                else
                {
                	foreach($sourceStock->data->all() as $stock){
                		$itemsData[ $stock->inHospitalItemId ]['sourceDivisionName'] = $stock->divisionName;
                		$itemsData[ $stock->inHospitalItemId ]['sourceRackName'] = $stock->rackName;
                	}
                }
            	
            }
            
            $targetStock = StockView::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$targetDivision);
            foreach($itemsData as $inHPId => $item){
                $targetStock->orWhere('inHospitalItemId',$inHPId);
            }
            
            $targetStock = $targetStock->get();
        	foreach($targetStock->data->all() as $stock){
        		$itemsData[ $stock->inHospitalItemId ]['divisionName'] = $stock->divisionName;
        		$itemsData[ $stock->inHospitalItemId ]['rackName'] = $stock->rackName;
        		$itemsData[ $stock->inHospitalItemId ]['constantByDiv'] = $stock->constantByDiv;
        		if($stock->distributorName !== "")
        		{
        		    $itemsData[ $stock->inHospitalItemId ]['distributorName'] = $stock->distributorName;
        		}
        	}
            
            $content = $this->view('NewJoyPla/view/CreateLabel', [
                'original_design' => $defaultDesign,
                'itemsData' => $itemsData,
                'inHpItemMaster' => $inHpItemMaster,
                'title' => $title,
                'api_url' => $api_url,
                'userInfo' => $user_info,
                'hospitalData'=> $hospital,
                'nowTime' => $nowTime
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                'csrf_token' => Csrf::generate(16)
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
                'title'     => 'JoyPla ラベル発行',
                'content'   => $content->render(),
                'script' => $script,
                'style' => $style,
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
	}


    private function design()
    {
        
    return <<<EOM
	<div class='printarea uk-margin-remove'>
		<div>
			<b class='font-size-16'>%JoyPla:itemName%</b>
			<div class='uk-child-width-1-2' uk-grid>
				<div class=''>
					<span>%JoyPla:itemMaker%</span><br>
					<span>%JoyPla:catalogNo% %JoyPla:itemStandard%</span><br>
					<span>%JoyPla:inHPId%</span><br>
				</div>
				<div class='uk-text-right uk-padding-remove'>
					<b>%JoyPla:sourceDivisionName%</b> <span>元棚番:%JoyPla:sourceRackName%</span><br>
					<b>%JoyPla:divisionName%</b> <span>払出棚番:%JoyPla:rackName%</span><br>
					<span>定数:%JoyPla:constantByDiv%%JoyPla:quantityUnit%</span><br>
					<span class='uk-text-bold' style='font-size:1.25em'>入数:%JoyPla:quantity%%JoyPla:quantityUnit%</span><br>
				</div>
			</div>
			<div class='uk-text-center' id='barcode_%JoyPla:num%'>%JoyPla:barcodeId%</div>
			<div class='uk-text-right'>%JoyPla:distributorName%</div>
		</div>
	</div>
EOM;
    }
	
}

$CreateLabelController = new CreateLabelController();

$action = $SPIRAL->getParam('Action');

{
    {
        echo $CreateLabelController->index()->render();  
    }
}