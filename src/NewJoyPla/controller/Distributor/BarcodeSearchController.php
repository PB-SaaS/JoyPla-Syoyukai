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

use ApiErrorCode\FactoryApiErrorCode;
use StdClass;
use Exception;

use App\Lib\SpiralTable;

class BarcodeSearchController extends Controller
{

      public function __construct()
      {
            global $SPIRAL;
            $this->card_title = array(
                  '03_order' => OROSHI_ORDER_PAGE,
                  '04' => OROSHI_RECEIVING_PAGE,
            );
            
            $this->jsessonId = '';
            if(isset($_COOKIE['JSESSIONID'])){
                  $this->jsessonId = $_COOKIE['JSESSIONID'];
            }

            $this->my_area_title = OROSHI_MY_AREA_TITLE;

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
                  
                  if(preg_match('/^03/', $search_value) && strlen($search_value) == 18)
                  {
                        //注文書
                        $result = OrderHistory::where('orderNumber',$search_value)->where('hospitalId',$user_info->getHospitalId())->where('distributorId',$user_info->getDistributorId())->get();

                        if($result->count == '0' || $record->orderStatus == '1'){
                            throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }

                        $record = $result->data->get(0);

                        if(isset($this->card_title['03_order'])){
                            $card_title = $this->card_title['03_order'];
                        }

                  } 
                  else if(preg_match('/^04/', $search_value) && strlen($search_value) == 18)
                  {
                        //検収書
                        $result = ReceivingHistory::where('receivingHId',$search_value)->where('hospitalId',$user_info->getHospitalId())->where('distributorId',$user_info->getDistributorId())->get();

                        if($result->count == '0'){
                              throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }

                        $record = $result->data->get(0);

                        if(isset($this->card_title['04'])){
                              $card_title = $this->card_title['04'];
                        }
                  }

                  if($card_title != '' && $record != '')
                  {
                        $this->spiral_table->setJsessionid($this->jsessonId);
                        $this->spiral_table->setMyAreaTitle($this->my_area_title);
                        $this->spiral_table->setCardTitle($card_title);
                        $this->spiral_table->addIds($record->id);
                        $content = $this->spiral_table->getCardUrls();
                        $content = json_encode($content);
                  }
                  else 
                  {
                        throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
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

$BarcodeSearchController = new BarcodeSearchController();
echo $BarcodeSearchController->index()->render();