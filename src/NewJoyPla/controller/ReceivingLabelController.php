<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\ReceivingView;
use App\Model\Hospital;

use ApiErrorCode\FactoryApiErrorCode;
use StdClass;
use Exception;

class ReceivingLabelController extends Controller
{
    public function __construct()
    {
    }

    public function index(): View
    {
		global $SPIRAL;
        try{
            $user_info = new UserInfo($SPIRAL);
            
            $receiving_id_row = $SPIRAL->getParam('receivingId');
            $receiving_ids = explode(",", $receiving_id_row);
            
            $print_counts = ($SPIRAL->getParam('print_counts'))? $SPIRAL->getParam('print_counts') : [];

            if(count($receiving_ids) === 0)
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $receiving_items = ReceivingView::where('hospitalId',$user_info->getHospitalId());
            foreach($receiving_ids as $id)
            {
                $receiving_items->orWhere('receivingHId', $id);
            }
            if($user_info->isUser())
            {
                $receiving_items->where('divisionId',$user_info->getDivisionId());
            }
            $receiving_items = $receiving_items->get();
            $receiving_items = $receiving_items->data->all();

            $first_flg = (count($print_counts) == 0);
            foreach($receiving_items as &$i)
            {
                $i->print_count = 1;
                if(!$first_flg)
                {
                    foreach($print_counts as $p)
                    {
                        if($p['receivingNumber'] == $i->receivingNumber && $p['count'] > 0)
                        {
                            $i->print_count = $p['count'];
                            $exist = true;
                            break;
                        }
                    }
                }
                else
                {
                    $i->print_count = ( (int)$i->receivingCount - (int)$i->totalReturnCount > 0)? (int)$i->receivingCount - (int)$i->totalReturnCount : 0;
                    $print_counts[] = [
                        'receivingHId' => $i->receivingHId,
                        'receivingNumber' => $i->receivingNumber,
                        'count' => $i->print_count ,
                    ];
                }
            }

            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
        
            $content = $this->view('NewJoyPla/view/ReceivingLabel', [
                //'api_url' => $api_url,
                'user_info' => $user_info,
                'receiving_id' => $receiving_id_row,
                'form_action' => '%url/rel:mpgt:ReceivingLabel%',
                'receiving_items' => $receiving_items,
                'print_counts' => $print_counts,
                'hospital_data' => $hospital_data
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
                'title'     => 'JoyPla 入庫ラベル',
                'script' => $script,
                'style' => $style,
                'head' => $head->render(),
                'header' => $header->render(),
                'content'   => $content->render(),
                'baseUrl' => '',
            ],false);
        }
	}
}

$ReceivingLabelController = new ReceivingLabelController();
echo $ReceivingLabelController->index()->render();