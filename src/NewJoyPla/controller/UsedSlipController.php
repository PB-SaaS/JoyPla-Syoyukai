<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\UsedSlipHistoy;
use App\Model\Borrowing;
use App\Model\InHospitalItem;
use App\Model\AssociationTR;
use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class UsedSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;

        $title = 'JoyPla 未承認使用伝票一覧';

        try {
            
            $user_info = new UserInfo($SPIRAL);

            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            $cardId = $SPIRAL->getCardId();
            if($cardId == null)
            {   
                throw new Exception("ページが存在しません",404);
            }

            $used_slip_history = UsedSlipHistoy::where('id',$cardId)->get();
            $used_slip_history = $used_slip_history->data->all()[0];

            $borrowing = Borrowing::where('usedSlipId',$used_slip_history->usedSlipId)->get();
            $borrowing = $borrowing->data->all();

            $instance = InHospitalItem::getInstance();
            foreach($borrowing as $data)
            {
                $instance::orWhere('inHospitalItemId',$data->inHospitalItemId);
            }

            $in_Hospital_item = $instance::get();

            foreach($borrowing as $borrowing_key => $data)
            {
                foreach($in_Hospital_item->data->all() as $item)
                {
                    if($item->inHospitalItemId === $data->inHospitalItemId)
                    {
                        $borrowing[$borrowing_key]->makerName = $item->makerName;
                        $borrowing[$borrowing_key]->itemName = $item->itemName;
                        $borrowing[$borrowing_key]->itemCode = $item->itemCode;
                        $borrowing[$borrowing_key]->itemStandard = $item->itemStandard;
                        $borrowing[$borrowing_key]->itemJANCode = $item->itemJANCode;
                        break;
                    }
                }
            }

            $api_url = "%url/rel:mpgt:Borrowing%";
            if( $user_info->isDistributorUser())
            {
                $api_url = "%url/rel:mpgt:BorrowingForD%";
            }

            $association = [];
            if($used_slip_history->usedSlipStatus == '2')
            {
                $link = $api_url."&Action=approvedUsedSlip";
                $link_name = "承認済み使用伝票一覧";

                if( $user_info->isHospitalUser())
                {
                    $association = AssociationTR::where('usedSlipId',$used_slip_history->usedSlipId)->get();
                    $association = $association->data->all();
                }
            }
            else 
            {
                $link = $api_url."&Action=unapprovedUsedSlip";
                $link_name = "未承認使用伝票一覧";
            }

            $content = $this->view('NewJoyPla/view/UsedSlip', [
                'user_info' => $user_info,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'used_slip_history' => $used_slip_history,
                'borrowing' => $borrowing,
                'link' => $link ,
                'link_name' => $link_name,
                'association' => $association,
                'current_name' => '使用伝票',
            ] , false);

        } catch ( Exception $ex ) {
            $title = 'JoyPla エラー';
            $header = $this->view('NewJoyPla/src/Header', [], false);
            
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ] , false);

        } finally {
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
$UsedSlipController = new UsedSlipController();

$action = $SPIRAL->getParam('Action');

{
    echo $UsedSlipController->index()->render();
}