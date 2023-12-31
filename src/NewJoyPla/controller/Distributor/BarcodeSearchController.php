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
use App\Model\DistributorAffiliationView;
use App\Model\DistributorUser;

use ApiErrorCode\FactoryApiErrorCode;
use StdClass;
use Exception;

use App\Lib\SpiralTable;
use Validate\DistributorDB;

class BarcodeSearchController extends Controller
{
      public $card_title = [];
      public $jsessonId = '';
      public $my_area_title = '';
      public $spiral_table = '';
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

      public function apiResponse($SPIRAL)
      {
            $search_value = $SPIRAL->getParam('searchValue');
            $content = $this->search($search_value);

            return $this->view('NewJoyPla/view/template/ApiResponse', [
                  'content'   => $content,
            ],false);
      }

      
      public function index($SPIRAL)
      {
            $search_value = $SPIRAL->getParam('searchValue');
            $content = json_decode($this->search($search_value) , true);
            if(! $content['urls'][0]) {
                  $content = $this->view('NewJoyPla/view/template/Error', [
                      'code' => FactoryApiErrorCode::factory(404)->getCode(),
                      'message'=> FactoryApiErrorCode::factory(404)->getMessage(),
                      ] , false);
                      
                  $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
                  $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                  'SPIRAL' => $SPIRAL
                  ], false);
                  // テンプレートにパラメータを渡し、HTMLを生成し返却
                  return $this->view('NewJoyPla/view/template/Template', [
                        'title'     => 'JoyPla Not Found',
                        'script' => '',
                        'content'   => $content->render(),
                        'head' => $head->render(),
                        'header' => $header->render(),
                        'baseUrl' => '',
                  ],false);
            } else {
                  return $this->view('NewJoyPla/view/template/Redirect', [
                        'url'   => $content['urls'][0],
                  ],false);
            }
      }


      public function search($search_value)
      {
		global $SPIRAL;

            try {
                  $user_info = new UserInfo($SPIRAL);
            
                  //$token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
                  //Csrf::validate($token,true);

                  //検索キーワード を取得
                  //$search_value = $SPIRAL->getParam('searchValue');
                  $card_title = '';
                  $record = '';
                  
                  if(preg_match('/^03/', $search_value) && ( strlen($search_value) == 18 || strlen($search_value) == 15 ))
                  {
                        //注文書
                        //$result = OrderHistory::where('orderNumber',$search_value)->where('hospitalId',$user_info->getHospitalId())->where('distributorId',$user_info->getDistributorId())->get();
                        $result = OrderHistory::getNewInstance()->where('orderNumber',$search_value)->get();
                        
                        $record = $result->data->get(0);
                        if($result->count == '0' || $record->orderStatus == '1'){
                              throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }
                        
                        $affiliations = DistributorAffiliationView::where('loginId',$user_info->getLoginId())->where('distributorId',$record->distributorId)->where('invitingAgree','1')->get();
                        
                        if($affiliations->count == '0'){
                              throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
                        }

                        $affiliations = $affiliations->data->get(0);

                        $result = DistributorUser::where('loginId',$user_info->getLoginId())->update([
                              'affiliationId' => $affiliations->affiliationId
                        ]);

                        if($result->code != '0')
                        {
                              throw new Exception(FactoryApiErrorCode::factory($result->code)->getMessage(),FactoryApiErrorCode::factory($result->code)->getCode());
                        }

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
                  return $content;
            }
      }
}

$BarcodeSearchController = new BarcodeSearchController();
$Action = $SPIRAL->getParam('Action');

if($Action == "search")
{
      echo $BarcodeSearchController->apiResponse($SPIRAL)->render();
}
else
{
      echo $BarcodeSearchController->index($SPIRAL)->render();
}
