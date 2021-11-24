<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Model\DistributorUser;
use App\Model\Distributor;
use App\Model\HospitalUser;
use App\Model\QuoteRequest;
use App\Model\Price;
use App\Model\QuoteItem;


use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class RegistRequestPriceController extends Controller
{
    public function __construct()
    {
    }
    
    public function thank()
    {
        global $SPIRAL;
        $request_id = $SPIRAL->getContextByFieldTitle('requestId');
        
        $quote_request = QuoteRequest::where('requestId',$request_id)->get();
        $quote_request = $quote_request->data->get(0);
        
        $price_data = Price::where('requestId',$request_id)->get();
        $price_data = $price_data->data->all();
        
        $quote_item = QuoteItem::where('requestId',$request_id)->get();
        $quote_item = $quote_item->data->all();
        
        $status = [];
        
        $saiyou = 0;
        $husaiyou = 0;
        $sonota = 0;
        
        foreach($price_data as $item)
        {
            $status[] = $item->requestFlg;
        }
        foreach($quote_item as $item)
        {
            $status[] = $item->requestFlg;
        }
        
        foreach($status as $s)
        {
            if($s == 1)
            {
                $saiyou++;
            } 
            else if($s == 2)
            {
                $husaiyou++;
            }
            else
            {
                $sonota++;
            }
        }
        
        $status_id = $quote_request->requestStatus;
        
        if(count($status) > 0 )
        {
    /**
     *  1	未開封
     *  2	開封
     *  3	商品記載有
     *  4	一部却下
     *  5	一部採用
     *  6	却下
     *  7	採用
         */
            if(count($status) == $sonota)
            {
                $status_id = 3;
            }
            else if(count($status) == $saiyou)
            {
                $status_id = 7;
            }
            else if(count($status) == $husaiyou)
            {
                $status_id = 6;
            }
            else if($husaiyou > 0 && $saiyou == 0 )
            {
                $status_id = 4;
            }
            else if($saiyou > 0 )
            {
                $status_id = 5;
            }
        }
        
        QuoteRequest::where('requestId',$request_id)->update(
            ['requestStatus' => $status_id]
            );
        
            
        $subject = "[JoyPla] 見積金額が登録されました";
        $mail_body = $this->view('NewJoyPlaTenantAdmin/view/Mail/RegistRequestPrice', [
            'name' => '%val:usr:name%',
            'request_title' => $quote_request->requestTitle,
            'request_Name' => $quote_request->requestUName,
            'url' => LOGIN_URL,
        ] , false)->render();
        
        $hospital_user = HospitalUser::getNewInstance();
        
        $select_name = $this->makeId($quote_request->hospitalId);
        $test = $hospital_user::selectName($select_name)
            ->rule(['name'=>'hospitalId','label'=>'name_'.$quote_request->hospitalId,'value1'=>$quote_request->hospitalId,'condition'=>'matches'])
            ->filterCreate();
            
        $test = $hospital_user::selectRule($select_name)
            ->body($mail_body)
            ->subject($subject)
            ->from(FROM_ADDRESS,FROM_NAME)
            ->send();
    }
}