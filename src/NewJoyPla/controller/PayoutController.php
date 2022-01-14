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
use App\Model\InHospitalItemView;
use App\Model\PayoutHistory;
use App\Model\Payout;
use App\Model\PayoutView;
use App\Model\PayScheduleItems;
use App\Model\PayScheduleItemsView;
use App\Model\Card;
use App\Model\Stock;
use App\Model\Distributor;
use App\Model\InventoryAdjustmentTransaction;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class PayoutController extends Controller
{
    public function __construct()
    {
    }
    /*
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
            $target_division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            if( ($user_info->isHospitalUser() && $user_info->getUserPermission() == '1')) 
            {
                $source_division = $target_division;
            } 
            else 
            {
                $source_division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }
    
            $api_url = "%url/rel:mpgt:Payout%";
    
            
            $content = $this->view('NewJoyPla/view/PayoutContent', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'source_division'=> $source_division,
                'target_division'=> $target_division,
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
                'title'     => 'JoyPla 払出登録',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    */

    public function payoutScheduled(): View
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
            
            $target_division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            if( ($user_info->isHospitalUser() && !$user_info->isUser())) 
            {
                $source_division = $target_division;
            } 
            else 
            {
                $source_division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }
    
            $api_url = "%url/rel:mpgt:Payout%";
    
            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $useUnitPrice = (int)$hospital_data->payoutUnitPrice;
            
            $content = $this->view('NewJoyPla/view/PayoutScheduled', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'source_division'=> $source_division,
                'target_division'=> $target_division,
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
                'title'     => 'JoyPla 払出予定商品登録',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }



    public function newPayout(): View
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
            $target_division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            if( ($user_info->isHospitalUser() && !$user_info->isUser())) 
            {
                $source_division = $target_division;
            } 
            else 
            {
                $source_division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }
    
            $api_url = "%url/rel:mpgt:Payout%";
    
            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $useUnitPrice = (int)$hospital_data->payoutUnitPrice;
            
            $content = $this->view('NewJoyPla/view/PayoutContent2', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'source_division'=> $source_division,
                'target_division'=> $target_division,
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
                'title'     => 'JoyPla 払出登録',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    public function payoutList(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if ($user_info->isHospitalUser() && ( $user_info->isAdmin() || $user_info->isApprover() ))
            {
                $content = $this->view('NewJoyPla/view/template/List', [
                        'title' => '払出履歴一覧',
                        'table' => '%sf:usr:payoutList:mstfilter%',
                        'csrf_token' => Csrf::generate(16)
                        ] , false);
            } else {
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'table' => '%sf:usr:search105:table%',
                    'title' => '払出履歴一覧 - 部署選択',
                    'param' => 'payoutListForDivision',
                    ] , false);
            }
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 払出履歴一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function payoutListForDivision(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ($user_info->isHospitalUser() && ( $user_info->isApprover() || $user_info->isAdmin()) )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ( \App\lib\isMypage() )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
    
            $api_url = "%url/rel:mpgt:Payout%";
            
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '払出履歴一覧',
                    'table' => '%sf:usr:payoutList:mstfilter%',
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
    
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 払出履歴一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function payoutLabel(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        try {

            $user_info = new UserInfo($SPIRAL);
            
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $payout_history_id = $SPIRAL->getParam('payoutHistoryId');
            
            if($payout_history_id == "" || $payout_history_id == null)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if($user_info->isAdmin() || $user_info->isApprover())
            {
                $payout_items = PayoutView::where('payoutHistoryId', $payout_history_id)->where('hospitalId',$user_info->getHospitalId())->get();
                $payout_items = $payout_items->data->all();
            }
            else
            {
                $payout_items = PayoutView::where('payoutHistoryId', $payout_history_id)->where('hospitalId',$user_info->getHospitalId())->where('sourceDivisionId',$user_info->getDivisionId())->get();
                $payout_items = $payout_items->data->all();
            }
            
	        Stock::where('hospitalId',$user_info->getHospitalId());
	        
	        foreach($payout_items as $item)
	        {
	            Stock::orWhere('inHospitalItemId',$item->inHospitalItemId);
	        }
	        
	        $stock_items        = Stock::get();
	        
	        foreach($payout_items as $key => $item)
	        {
	            foreach($stock_items->data->all() as $stock_item)
	            {
	                if($stock_item->inHospitalItemId == $item->inHospitalItemId && $stock_item->divisionId == $item->sourceDivisionId)
	                {
	                    $payout_items[$key]->sourceRackName = $stock_item->rackName;
	                    $payout_items[$key]->constantByDiv = $stock_item->constantByDiv;
	                }
	                if($stock_item->inHospitalItemId == $item->inHospitalItemId && $stock_item->divisionId == $item->targetDivisionId)
	                {
	                    $payout_items[$key]->targetRackName = $stock_item->rackName;
	                    $payout_items[$key]->constantByDiv = $stock_item->constantByDiv;
	                }
	            }
	            Distributor::orWhere('distributorId' , $item->distributorId);
	            Division::orWhere('divisionId', $item->targetDivisionId);
	            Division::orWhere('divisionId', $item->sourceDivisionId);
	        }
	        
	        $distributor = Distributor::get();
	        $division = Division::get();
            foreach($payout_items as $key => $item)
	        {
	            
                foreach($division->data->all() as $division_data)
                {
                    if($item->sourceDivisionId == $division_data->divisionId)
                    {
                        $payout_items[$key]->sourceDivision = $division_data->divisionName;
                    }
                    if($item->targetDivisionId == $division_data->divisionId)
                    {
                        $payout_items[$key]->targetDivision = $division_data->divisionName;
                    }
                }
                
                foreach($distributor->data->all() as $distributor_data)
                {
                    if($item->distributorId == $distributor_data->distributorId)
                    {
                        $payout_items[$key]->distributorName = $distributor_data->distributorName;
                    }
                }
	        }
            
            $default_design = $this->defaultDesign();
            
            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
        
            if($hospital_data->labelDesign2 != '')
            {
                $default_design = htmlspecialchars_decode($hospital_data->labelDesign2);
            }
            $content = $this->view('NewJoyPla/view/PayoutLabel', [
                //'api_url' => $api_url,
                'user_info' => $user_info,
                'payout_items' => $payout_items,
                'default_design' => $default_design
            ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
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
                'title'     => 'JoyPla 払出ラベル',
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
	<div class='printarea uk-margin-remove'>
		<div>
			<b class='font-size-16'>%JoyPla:itemName%</b>
			<div class='uk-child-width-1-2' uk-grid>
				<div class=''>
					<span>%JoyPla:itemMaker%</span><br>
					<span>%JoyPla:catalogNo% %JoyPla:itemStandard%</span><br>
					<span>%JoyPla:inHPId%</span><br>
					<span>%JoyPla:lotNumber%</span><br>
					<span>%JoyPla:lotDate%</span><br>
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
    
    public function payoutRegistApi()
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
            
            $payout = $SPIRAL->getParam('payout');
            $payout = $this->requestUrldecode($payout);
            $payout_date = $SPIRAL->getParam('payoutDate');
            if($payout_date == '')
            {
                $payout_date = 'now';
            }
            
            $in_hospital_item = InHospitalItemView::where('hospitalId', $user_info->getHospitalId());
            foreach($payout as $key => $record)
            {
                $in_hospital_item->orWhere('inHospitalItemId',$record['recordId']);
            }
            $in_hospital_item = $in_hospital_item->get();
            
            foreach($payout as $key => $record)
            {
                foreach($in_hospital_item->data->all() as $in_hp_item)
                {
                    $lot_flag = 0;
                    if($record['recordId'] == $in_hp_item->inHospitalItemId)
                    {
                        $lot_flag = (int)$in_hp_item->lotManagement;
                        break;
                    }
                }
                if($lot_flag && ( $record['lotNumber'] == '' || $record['lotDate'] == '' ) )
                {
                    throw new Exception('invalid lot',100);
                }
                if( ($record['lotNumber'] != '' && $record['lotDate'] == '' ) || ($record['lotNumber'] == '' && $record['lotDate'] != ''))
                {
                    throw new Exception('invalid lotNumber input',101);
                }
                if (($record['lotNumber'] != '') && ($record['lotDate'] != '')) 
                {
                    //if ((!ctype_alnum($item['lotNumber'])) || (strlen($item['lotNumber']) > 20))
                    if ((!preg_match('/^[a-zA-Z0-9!-\/:-@¥[-`{-~]+$/', $record['lotNumber'])) || (strlen($record['lotNumber']) > 20))
                    {
                        throw new Exception('invalid lotNumber format',102);
                    }
                }
                if( (int)$payout['countNum'] < 0 ||(int)$record['countLabelNum'] < 0)
                {
                    throw new Exception('invalid payout count',200);
                }
        		$payout[$key]['countNum'] = (int)$record['countNum'] * (int)$record['countLabelNum'] ;
        		$payout[$key]['payoutCount'] = $record['countNum'];
            }
            
            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            
            $use_unit_price = $hospital_data->payoutUnitPrice;
            
            $source_division_id = $SPIRAL->getParam('sourceDivisionId');
            $source_division_name =  $SPIRAL->getParam('sourceDivisionName');
            $target_division_id = $SPIRAL->getParam('targetDivisionId');
            $target_division_name = $SPIRAL->getParam('targetDivisionName');
            
            $payout = \App\Lib\requestUrldecode($payout);
    		$source_division_name = urldecode($source_division_name);
    		$target_division_name = urldecode($target_division_name);
    		
    		$payout_id = $this->makeId('05');
    		
    		/* インサートデータ作成 */
    		$insert_data = [];
    		$total_amount = 0;
    		$in_hospital_item_ids = [];
    		$card_ids = [];
    		$label_create_flg = false;
    		foreach($payout as $data)
    		{
				if( (int)$data['countNum']  > 0 ){
				    if (array_search($data['recordId'],$in_hospital_item_ids) === false)
				    {
				        $in_hospital_item_ids[] = $data['recordId'];
				    }
                    $unit_price = $use_unit_price
                        ?(float)(str_replace(',', '', $data['unitPrice']))
                        :(((float)str_replace(',', '', $data['kakaku']) == 0 || (float)$data['irisu'] == 0)
                            ? 0 
                            : (float)(str_replace(',', '', $data['kakaku']) / (float)$data['irisu']));
					$insert_data[] = [
					    'registrationTime' => $payout_date,
						'payoutHistoryId' => $payout_id,
						'inHospitalItemId' => $data['recordId'],
						'hospitalId' => $user_info->getHospitalId(),
						'sourceDivisionId' => $source_division_id,
						'targetDivisionId' => $target_division_id,
						'quantity' => $data['irisu'],
						'quantityUnit' => $data['unit'],
						'itemUnit' => $data['itemUnit'],
						'price' => str_replace(',', '', $data['kakaku']),
						'payoutQuantity' => (int)$data['countNum'],
						'payoutAmount' => (float)$unit_price * (int)$data['countNum'],
						'payoutCount' => $data['payoutCount'],
						'payoutLabelCount' => $data['countLabelNum'],
						'lotNumber' => $data['lotNumber'],
						'lotDate' => $data['lotDate'],
						'unitPrice' => $unit_price,
						'cardId' => $data['cardNum']
					];
					if($data['cardNum'] != "")
					{
					    $card_ids[] = $data['cardNum'];
					}
					
					if($data['cardNum'] == "")
					{
					    $label_create_flg = true; //一つでもあれば発行する
					}
					$total_amount = $total_amount + ((float)$unit_price * (int)$data['countNum']);
				}
    		}
    		
    		$insert_history_data = [
    		    [
				    'registrationTime' => $payout_date,
    		        'payoutHistoryId' => $payout_id,
    		        'hospitalId' => $user_info->getHospitalId(),
    		        'sourceDivisionId' => $source_division_id,
    		        'sourceDivision' => $source_division_name,
    		        'targetDivisionId' => $target_division_id,
    		        'targetDivision' => $target_division_name,
    		        'itemsNumber' => count($in_hospital_item_ids),
    		        'totalAmount' => $total_amount,
                ]
            ];
    		
    		$inventory_adjustment_trdata = [];
    		
    		
    		foreach($insert_data as $record)
    		{
    		    if($record['lotNumber'] && $record['lotDate'])
    		    {
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record['targetDivisionId'],
                        'inHospitalItemId' => $record['inHospitalItemId'],
                        'count' => $record['payoutQuantity'],
                        'pattern' => 5,
                        'hospitalId' => $user_info->getHospitalId(),
        		        'lotUniqueKey' => $user_info->getHospitalId().$record['targetDivisionId'].$record['inHospitalItemId'].$record['lotNumber'].$record['lotDate'],
        		        'stockQuantity' => $record['payoutQuantity'],
                        'lotNumber' =>  $record['lotNumber'],
                        'lotDate' =>    $record['lotDate'],
        		    ];
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record['sourceDivisionId'],
                        'inHospitalItemId' => $record['inHospitalItemId'],
                        'count' => -$record['payoutQuantity'],
                        'pattern' => 4,
                        'hospitalId' => $user_info->getHospitalId(),
        		        'lotUniqueKey' => $user_info->getHospitalId().$record['sourceDivisionId'].$record['inHospitalItemId'].$record['lotNumber'].$record['lotDate'],
        		        'stockQuantity' => -$record['payoutQuantity'],
                        'lotNumber' =>  $record['lotNumber'],
                        'lotDate' =>    $record['lotDate'],
        		    ];
    		    }
    		    else
    		    {
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record['targetDivisionId'],
                        'inHospitalItemId' => $record['inHospitalItemId'],
                        'pattern' => 5,
                        'count' => $record['payoutQuantity'],
                        'hospitalId' => $user_info->getHospitalId(),
        		    ];   
        		    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record['sourceDivisionId'],
                        'inHospitalItemId' => $record['inHospitalItemId'],
                        'pattern' => 4,
                        'count' => -$record['payoutQuantity'],
                        'hospitalId' => $user_info->getHospitalId(),
        		    ];   
    		    }
    		}
    		
    		if( count($insert_data) === 0 )
    		{
                throw new Exception('not payout data',200);
    		}
    		
    		$result = PayoutHistory::insert($insert_history_data);
    		$result = Payout::insert($insert_data);
    		
    		$payout = new Payout();
    		$payout->where('hospitalId',$user_info->getHospitalId())->where('payoutHistoryId', $payout_id);
    		foreach($card_ids as $id)
    		{
    		    $payout->orWhere('cardId',$id);
    		}
    		
    		$payout_data = $payout->get();
    		$card_update = [];
    		foreach($payout_data->data->all() as $payout_item)
    		{
    		    if($payout_item->cardId != '')
    		    {
    		        $card_update[] = [
    		        'cardId' => $payout_item->cardId,
    		        'payoutId' => $payout_item->payoutId,
    		        ];
    		    }
    		}
    		if(count($card_update) > 0)
    		{
    		    Card::bulkUpdate('cardId',$card_update);
    		}
    		$result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);
            
            $content = new ApiResponse(['payoutHistoryId'=>$payout_id,'labelCreateFlg' => $label_create_flg] , $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['payoutRegistApi']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }

    
    public function regPayoutScheduledApi()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);

            $items = $SPIRAL->getParam('items');
            $items = $this->requestUrldecode($items);

            $insert = [];

            foreach($items as $item)
            {
                $insert[] = [
                    "payoutPlanTime" => $item['payout_schedule'],
                    "payoutPlanId" => $this->makeId('psi'),//payout schedule item
                    "pickingId" => '',
                    "inHospitalItemId" => $item['recordId'],
                    "itemId" => $item['itemId'],
                    "hospitalId" => $user_info->getHospitalId(),
                    "cardId" => $item['cardNum'],
                    "sourceDivisionId" => $item['source_division'],
                    "targetDivisionId" => $item['target_division'],
                    "payoutQuantity" => $item['payoutCount'],
                ];
            }

            $result = PayScheduleItems::insert($insert);

            $content = new ApiResponse($result->ids, $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['payoutRegistApi']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }

    public function payoutScheduledItemList()
    {
        global $SPIRAL;
        try {
            //フルスクラッチでやってみる
            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            $sort_title = ( $SPIRAL->getParam('sortTitle'))? $SPIRAL->getParam('sortTitle') : 'id'; // 初期ソート id asc
            $sort_asc = ( $SPIRAL->getParam('sort') === 'asc' || $SPIRAL->getParam('sort') === 'desc' )? $SPIRAL->getParam('sort') : 'asc';
            /** 取得条件 */
            $limit = ( is_null($SPIRAL->getParam('limit')) )? 0 : $SPIRAL->getParam('limit') ;
            $limit = ( $limit >= 1 && $limit <= 1000 )? $limit : 10 ; //デフォルト値 10
            
            $page = ( is_null($SPIRAL->getParam('page')) )? 0 : $SPIRAL->getParam('page') ;
            $page = ( $page >= 1 )? $page : 1 ; //デフォルト値 1

            /** 値の取得 */
            /** 払出予定商品 */
            $pay_schedule_items = PayScheduleItemsView::where('pickingId','','ISNULL')
                                                        ->where('hospitalId',$user_info->getHospitalId())
                                                        ->orWhere('outOfStockStatus','2','!=')
                                                        ->sort($sort_title,$sort_asc)
                                                        ->page($page);

            /** 検索 */
            $registration_time_start = ( $SPIRAL->getParam('registration_time_start') )? $SPIRAL->getParam('registration_time_start') : '';
            $registration_time_end = ( $SPIRAL->getParam('registration_time_end') )? $SPIRAL->getParam('registration_time_end') : '';
            $payout_plan_time_start = ( $SPIRAL->getParam('payout_plan_time_start') )? $SPIRAL->getParam('payout_plan_time_start') : '';
            $payout_plan_time_end = ( $SPIRAL->getParam('payout_plan_time_end') )? $SPIRAL->getParam('payout_plan_time_end') : '';
            $source_division = ( $SPIRAL->getParam('source_division') )? $SPIRAL->getParam('source_division') : '';
            $target_division = ( $SPIRAL->getParam('target_division') )? $SPIRAL->getParam('target_division') : '';
            $category = ( $SPIRAL->getParams('category') )? $SPIRAL->getParams('category') : '';
            $out_of_stock_status = ( $SPIRAL->getParams('out_of_stock_status') )? $SPIRAL->getParams('out_of_stock_status') : '';
            
            if ($registration_time_start !== '') { 
                $pay_schedule_items->where('registrationTime', $registration_time_start, '>='); 
            }

            if ($registration_time_end !== '') { 
                $pay_schedule_items->where('registrationTime', (date('Y-m-d', strtotime($registration_time_end . '+1 day'))), '<');
            }

            if ($payout_plan_time_start !== '') { 
                $pay_schedule_items->where('payoutPlanTime', $payout_plan_time_start, '>='); 
            }

            if ($payout_plan_time_end !== '') { 
                $pay_schedule_items->where('payoutPlanTime', (date('Y-m-d', strtotime($payout_plan_time_end . '+1 day'))), '<');
            }

            if ($source_division !== '') { 
                $pay_schedule_items->where('sourceDivisionId', $source_division); 
            }

            if ($target_division !== '') { 
                $pay_schedule_items->where('targetDivisionId', $target_division); 
            }

            if ($category !== '') {
                foreach($category as $c)
                {
                    $pay_schedule_items->orWhere('category', $c ); 
                }
            }

            if ($out_of_stock_status !== '') {
                foreach($out_of_stock_status as $c)
                {
                    if($c == 2){ continue; } //払出可能は検索できなくする
                    $pay_schedule_items->orWhere('outOfStockStatus', $c ); 
                }
            }

            $pay_schedule_items = $pay_schedule_items->paginate($limit);

            $count = $pay_schedule_items->count;
            $pay_schedule_items_label = $pay_schedule_items->label->all();
            $pay_schedule_items = $pay_schedule_items->data->all();

            /** 部署情報 */
            $division = [];
            $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            $division = $division->data->all();
            /** 実体参照を行い、必要な情報をセット */
            foreach($pay_schedule_items as &$item)
            {
                $item->checked = false;
                $item->outOfStockStatus_id = $item->outOfStockStatus;
                $item->outOfStockStatus = $pay_schedule_items_label['outOfStockStatus']->all()[$item->outOfStockStatus];
                $item->category = $pay_schedule_items_label['category']->all()[$item->category];

                $sourceDivision = \App\Lib\array_obj_find($division,'divisionId',$item->sourceDivisionId);
                $targetDivision = \App\Lib\array_obj_find($division,'divisionId',$item->targetDivisionId);
                $item->sourceDivision = $sourceDivision->divisionName;
                $item->targetDivision = $targetDivision->divisionName;
            }

            $form_url = "%url/rel:mpgt:Payout%";
            
            $content = $this->view('NewJoyPla/view/PayoutScheduledItemList', [
                    'title' => '払出予定商品一覧',
                    'action' => 'payoutScheduledItemList',
                    'form_url' => $form_url,
                    'sort_title' => $sort_title,
                    'sort_asc' => $sort_asc,
                    'limit' => $limit,
                    'page' => $page,
                    'count' => $count,
                    'category' => $category,
                    'category_label' => $pay_schedule_items_label['category']->all(),
                    'out_of_stock_status' => $out_of_stock_status,
                    'out_of_stock_status_label' => $pay_schedule_items_label['outOfStockStatus']->all(),
                    'registration_time_start' => \App\Lib\html($registration_time_start),
                    'registration_time_end' => \App\Lib\html($registration_time_end),
                    'payout_plan_time_start' => \App\Lib\html($payout_plan_time_start),
                    'payout_plan_time_end' => \App\Lib\html($payout_plan_time_end),
                    'source_division' => \App\Lib\html($source_division),
                    'target_division' => \App\Lib\html($target_division),
                    'division_info' => $division,
                    'pay_schedule_items' => $pay_schedule_items,
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
    
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 払出予定商品一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
}



/***
 * 実行
 */
$PayoutController = new PayoutController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'payoutRegistApi')
    {
        echo $PayoutController->payoutRegistApi()->render();
    }
    else if($action === 'newPayout')
    {
        echo $PayoutController->newPayout()->render();
    }
    else if($action === 'payoutLabel')
    {
        echo $PayoutController->payoutLabel()->render();
    }
    else if($action === 'payoutList')
    {
        echo $PayoutController->payoutList()->render();
    }
    else if($action === 'payoutListForDivision')
    {
        echo $PayoutController->payoutListForDivision()->render();
    }
    else if($action === 'payoutScheduled')
    {
        echo $PayoutController->payoutScheduled()->render();
    }
    else if($action === 'regPayoutScheduledApi')
    {
        echo $PayoutController->regPayoutScheduledApi()->render();
    }
    else if($action === 'payoutScheduledItemList')
    {
        echo $PayoutController->payoutScheduledItemList()->render();
    }
    else 
    {
        echo $PayoutController->newPayout()->render();
    }
}
