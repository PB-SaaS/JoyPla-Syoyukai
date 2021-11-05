<?php

namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\Price;
use App\Model\QuoteItem;
use App\Model\QuoteRequest;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class UpdateRequestController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(){
        global $SPIRAL;
        $requestId = $SPIRAL->getContextByFieldTitle("requestId");
        
        $price = Price::where('requestId',$requestId)->get();
        $price = $price->data->all();
        
        $quoteItem = QuoteItem::where('requestId',$requestId)->get();
        $quoteItem = $quoteItem->data->all();
        
        $status = [];
        foreach($price as $data)
        {
            $status[] = $data->requestFlg;
        }
        
        foreach($quoteItem as $data)
        {
            $status[] = $data->requestFlg;
        }
        
        $status = $this->checkStaus($status);
        if($status != 0)
        {
            QuoteRequest::where('requestId',$requestId)->update([
                'requestStatus'=> $status
            ]);
        }
    }

    private function checkStaus(array $status){
        
        /**
         *  1	未開封
         *  2	開封
         *  3	商品記載有
         *  4	一部却下
         *  5	一部採用
         *  6	却下
         *  7	採用
         */
        $recordCount = count($status);
        $rec = 0;
        $not = 0;
        $mitumori = 0;
        $gyosya = 0;
        foreach($status as $s){
           if($s == '1'){
            $rec++;
           }
           
           if($s == '2'){
            $not++;
           }
           
           if($s == '3'){
            $mitumori++;
           }
           
           if($s == '4'){
            $gyosya++;
           }
        }
        if($recordCount == 0){
            return 0;
        }
        if($rec == 0 && $not == 0 && $gyosya > 0 ){
            return 3;
        }
        if($rec == 0 && $not == 0 && $mitumori > 0 ){
            return 0;
        }
        if($recordCount == $rec){
            return 7;
        }
        if($recordCount == $not){
            return 6;
        }
        if($rec > 0){
            return 5;
        }
        if($not > 0){
            return 4;
        }
        return 0;
    }
}