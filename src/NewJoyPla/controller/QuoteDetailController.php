<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Tenant;
use App\Model\QuoteRequest;
use App\Model\Price;
use App\Model\QuoteItem;
use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class QuoteDetailController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        try {
            
            $user_info = new UserInfo($SPIRAL);

            $cardId = $SPIRAL->getCardId();
            if($cardId == null)
            {
                throw new Exception("ページが存在しません",404);
            }

            $tenantKind = '';
            $tenantId = $user_info->getTenantId();
            if ($tenantId)
            {
                $tenant = Tenant::where('tenantId',$tenantId)->get();
                $tenant = $tenant->data->all()[0];
                $tenantKind = $tenant->tenantKind;
            }

            $card = QuoteRequest::find($cardId)->get();
            $card = $card->data->all()[0];

            $getReqPriceDB = Price::where('requestId',$card->requestId)->get();
            $reqPrice = [];
            foreach ($getReqPriceDB->data->all() as $val)
            {
                $reqPrice[] = ['id' => $val->id, 'requestFlg' => $val->requestFlg];
            }

            $getReqItemDB = QuoteItem::where('requestId',$card->requestId)->get();
            $reqItem = [];
            foreach ($getReqItemDB->data->all() as $val)
            {
                $reqItem[] = ['id' => $val->id, 'requestFlg' => $val->requestFlg];
            }

            $marge_data = array_merge($reqPrice, $reqItem);
            $status = $this->checkStaus($marge_data);
            if ($status !== 0)
            {
                $result = QuoteRequest::where('requestId',$card->requestId)->update(['requestStatus' => $status]);
            }

            $api_url = "%url/rel:mpgt:Price%";

            $content = $this->view('NewJoyPla/view/QuoteOrderDetail', [
                'userInfo' => $user_info,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'tenantKind' => $tenantKind
            ] , false);

        } catch ( Exception $ex ) {
            $title = 'JoyPla エラー';
            $header = $this->view('NewJoyPla/src/Header', [], false);
            
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
                'title'     => 'JoyPla 見積依頼詳細',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }

    private function checkStaus(array $reqItemData)
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
        $recordCount = count($reqItemData);
        $rec = 0;
        $not = 0;
        $mitumori = 0;
        $gyosya = 0;
        foreach ($reqItemData as $record)
        {
           if ($record['1'] == '1') { $rec++; }
           
           if ($record['1'] == '2') { $not++; }
           
           if ($record['1'] == '3') { $mitumori++; }
           
           if ($record['1'] == '4') { $gyosya++; }
        }
        if ($recordCount == 0) { return 0; }
        if ($rec == 0 && $not == 0 && $gyosya > 0) { return 3; }
        if ($rec == 0 && $not == 0 && $mitumori > 0) { return 0; }
        if ($recordCount == $rec) { return 7; }
        if ($recordCount == $not) { return 6; }
        if ($rec > 0) { return 5; }
        if ($not > 0) { return 4; }
        return 0;
    }
}

/***
 * 実行
 */
$QuoteDetailController = new QuoteDetailController();

$action = $SPIRAL->getParam('Action');

{
    echo $QuoteDetailController->index()->render();
}