<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\ReceivingView;
use App\Model\ReceivingHistory;

use ApiErrorCode\FactoryApiErrorCode;
use App\Model\DistributorAffiliationView;
use App\Model\DistributorUser;
use stdClass;
use Exception;


/**
 * 発注書
 */
class AcceptanceDetailController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;

        $title = 'JoyPla 検収書';

        try {
            
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isHospitalUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $card_Id = (int)$SPIRAL->getCardId();
            if($card_Id == null)
            {   
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $card = ReceivingHistory::find($card_Id)->get();
            $card = $card->data->get(0);
            
            $affiliations = DistributorAffiliationView::where('loginId',$user_info->getLoginId())->where('distributorId',$card->distributorId)->where('invitingAgree','1')->get();
                        
            if($affiliations->count == '0'){
                  throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }

            $affiliations = $affiliations->data->get(0);

            DistributorUser::where('loginId',$user_info->getLoginId())->update([
                  'affiliationId' => $affiliations->affiliationId
            ]);

            $receiving_items = ReceivingView::where('receivingHId',$card->receivingHId)->get();
            $receiving_items = $receiving_items->data->all();

            $api_url = '%url/card:page_266218%';

            $link_title = "検収書一覧";
        	$link = '%url/rel:mpgt:OrderD%&Action=acceptanceFormList';
            
            $content = $this->view('NewJoyPla/view/Distributor/AcceptanceForm', [
                'user_info' => $user_info,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'receivingData' => $receiving_items,
                'link_title' => $link_title,
                'link' => $link,
            ] , false);

        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false);

        } finally {

            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => $title,
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
$AcceptanceDetailController = new AcceptanceDetailController();

$action = $SPIRAL->getParam('Action');

{
    echo $AcceptanceDetailController->index()->render();
}