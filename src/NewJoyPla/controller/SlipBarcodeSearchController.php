<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\BillingHistory;
use App\Model\OrderHistory;
use App\Model\ReceivingHistory;
use App\Model\ReturnHistory;
use App\Model\PayoutHistory;
use App\Model\InventoryHistory;
use App\Model\InventoryEnd;
use App\Model\PickingHistory;

use ApiErrorCode\FactoryApiErrorCode;
use StdClass;
use Exception;

use App\Lib\SpiralTable;

class SlipBarcodeSearchController extends Controller
{
      public $card_title = "";
      public $jsessonId = "";
      public $my_area_title = "";
      public $spiral_table = "";
      public function __construct()
      {
            global $SPIRAL;
            $this->card_title = array(
                  '02' => HP_BILLING_PAGE,
                  '03_unorder' => HP_UNORDER_PAGE,
                  '03_order' => HP_ORDER_PAGE,
                  '04' => HP_RECEIVING_PAGE,
                  '06' => HP_RETERN_PAGE,
                  '05' => HP_PAYOUT_PAGE,
                  '08' => HP_DIVISION_INVENTORY_PAGE,
                  '09' => HP_END_INVENTORY_PAGE,
            );
            
            $this->jsessonId = '';
            if(isset($_COOKIE['JSESSIONID'])){
                  $this->jsessonId = $_COOKIE['JSESSIONID'];
            }

            $this->my_area_title = MY_AREA_TITLE;

            
            $spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
            $spiralApiRequest = new SpiralApiRequest();
            $this->spiral_table = new SpiralTable($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
      }

      public function index(): View
      {
		global $SPIRAL;

            try {
                  $user_info = new UserInfo($SPIRAL);
            
                  //$token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
                  //Csrf::validate($token,true);

                  //検索キーワード を取得
                  $search_value = $SPIRAL->getParam('searchValue');
                  $card_title = '';
                  $record = '';
                  $content = '';
                  
                  if(preg_match('/^02/', $search_value) && strlen($search_value) == 18)
                  {
                        //物品請求
                        $result = BillingHistory::where('billingNumber',$search_value)->where('hospitalId',$user_info->getHospitalId())->get();

                        if($result->count == '0'){
                              throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }

                        $record = $result->data->get(0);

                        if(isset($this->card_title['02'])){
                              $card_title = $this->card_title['02'];
                        }
                  } 
                  else if(preg_match('/^03/', $search_value) && strlen($search_value) == 18)
                  {
                        //注文書
                        $result = OrderHistory::where('orderNumber',$search_value)->where('hospitalId',$user_info->getHospitalId())->get();

                        if($result->count == '0'){
                              throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }

                        $record = $result->data->get(0);
                        if($record->orderStatus == '1'){
                              $card_title = $this->card_title['03_unorder'];
                        }
                        else
                        if(isset($this->card_title['03_order'])){
                              $card_title = $this->card_title['03_order'];
                        }

                  } 
                  else if(preg_match('/^04/', $search_value) && strlen($search_value) == 18)
                  {
                        //検収書
                        $result = ReceivingHistory::where('receivingHId',$search_value)->where('hospitalId',$user_info->getHospitalId())->get();

                        if($result->count == '0'){
                              throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }

                        $record = $result->data->get(0);

                        if(isset($this->card_title['04'])){
                              $card_title = $this->card_title['04'];
                        }
                  } 
                  else if(preg_match('/^06/', $search_value) && strlen($search_value) == 18)
                  {
                        //返品書
                        $result = ReturnHistory::where('returnHistoryID',$search_value)->where('hospitalId',$user_info->getHospitalId())->get();

                        if($result->count == '0'){
                              throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }

                        $record = $result->data->get(0);

                        if(isset($this->card_title['06'])){
                              $card_title = $this->card_title['06'];
                        }
                  } 
                  else if(preg_match('/^05/', $search_value) && strlen($search_value) == 18)
                  {
                        //払出
                        $result = PayoutHistory::where('payoutHistoryId',$search_value)->where('hospitalId',$user_info->getHospitalId())->get();

                        if($result->count == '0'){
                              throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }

                        $record = $result->data->get(0);

                        if(isset($this->card_title['05'])){
                              $card_title = $this->card_title['05'];
                        }
                  }
                  else if(preg_match('/^08/', $search_value) && strlen($search_value) == 18)
                  {
                        //棚卸
                        $result = InventoryHistory::where('inventoryHId',$search_value)->where('hospitalId',$user_info->getHospitalId())->get();

                        if($result->count == '0'){
                              throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }

                        $record = $result->data->get(0);

                        if(isset($this->card_title['08'])){
                              $card_title = $this->card_title['08'];
                        }
                  }
                  else if(preg_match('/^09/', $search_value) && strlen($search_value) == 18)
                  {
                        //棚卸
                        $result = InventoryEnd::where('inventoryEndId',$search_value)->where('hospitalId',$user_info->getHospitalId())->get();

                        if($result->count == '0'){
                              throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }

                        $record = $result->data->get(0);

                        if(isset($this->card_title['09'])){
                              $card_title = $this->card_title['09'];
                        }
                  }
                  else if(preg_match('/^12/', $search_value) && strlen($search_value) == 18)
                  {
                        //ピッキングリスト
                        $search = PickingHistory::where('hospitalId',$user_info->getHospitalId())->where('pickingId',$search_value)->get();
                        if($search->count == 0)
                        {
                              throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }
                        $search = $search->data->get(0);
                        $content = json_encode( [ 'code' => 0 , 'message' => 'OK' , 'urls' => [ '%url/rel:mpgt:Payout%&Action=pickingListSlip&id=' . $search->id ]],JSON_UNESCAPED_SLASHES);
                  }
                  
                  if($content == ''){
                        
                        if($card_title != '' && $record != '' )
                        {
                              $this->spiral_table->setJsessionid($this->jsessonId);
                              $this->spiral_table->setMyAreaTitle($this->my_area_title);
                              $this->spiral_table->setCardTitle($card_title);
                              $this->spiral_table->addIds($record->id);
                              $content = $this->spiral_table->getCardUrls();
                              $content = json_encode($content,JSON_UNESCAPED_SLASHES);
                        }
                        else 
                        {
                              throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
                        }
                  }


            } catch ( Exception $ex ) {
                  $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['SlipBarcodeSearch']);
                  $content = $content->toJson();
            }finally {
                  return $this->view('NewJoyPla/view/template/ApiResponse', [
                        'content'   => $content,
                  ],false);
            }
      }
}

$SlipBarcodeSearchController = new SlipBarcodeSearchController();
echo $SlipBarcodeSearchController->index()->render();