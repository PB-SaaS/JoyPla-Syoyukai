<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\OrderDataView;
use App\Model\OrderedItemView;
use App\Model\OrderHistory;
use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;


    /**
     * 発注書
     */
class OrderSlipDetailController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;

        $title = 'JoyPla 発注書';

        try {
            
            $user_info = new UserInfo($SPIRAL);

            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            $cardId = $SPIRAL->getCardId();
            if($cardId == null)
            {   
                throw new Exception('ページが存在しません',404);
            }

            $card = OrderDataView::where('id',$cardId)->get();
            $card = $card->data->all()[0];
         
            if($user_info->getUserPermission() != '1' && $card->divisionId != $user_info->getDivisionId())
            {
                App\Lib\viewNotPossible();
                exit;
            }
            
            $orderItems = OrderedItemView::where('orderNumber',$card->orderNumber)->get();
            $orderItems = $orderItems->data->all();

            $ItemsToJs = [];
            $Items = [];

            foreach ($orderItems as $record)
            {
                $Items[] = $record->orderCNumber;
            }

            foreach ($orderItems as $key => $record)
            {
                $orderItems[$key]->receivingNowCount = $record->receivingNum;
                $orderItems[$key]->receivingFlag = false;
                $orderItems[$key]->remainingCount = 0;
            }

            $num = 1;
            $minusOrder = false;
            
            foreach ($orderItems as $key => $record) 
            {
                $orderItems[$key]->remainingCount = $record->orderQuantity - $record->receivingNowCount;
            
                if ($record->orderQuantity < 0 ) { $minusOrder = true; }
            
                if ($record->orderQuantity > 0 && $record->orderQuantity <= $record->receivingNowCount)
                {
                    $orderItems[$key]->receivingFlag = true;
                } else if ($record->orderQuantity < 0 && $record->orderQuantity >= $record->receivingNowCount) {
                    $orderItems[$key]->receivingFlag = true;
                }
            
                $ItemsToJs[$record->inHospitalItemId] = [
                    'num' => $num,
                    'orderCNumber' => $record->orderCNumber,
                    'orderQuantity' => $record->orderQuantity,
                    'countNum' => $record->orderQuantity,
                    //'changeReceiving'=> '0',
                    'receivingFlag' => $orderItems[$key]->receivingFlag,
                    'receivingNowCount' => $orderItems[$key]->receivingNowCount, //入庫完了数
                    //'receivingBeforeCount'=> $orderItems[$key]['receivingNowCount'], //前回までの入庫数
                    'receivingCount' => 0, //今回入庫
                    'quantity' => $orderItems[$key]->quantity,
                    'makerName' => $orderItems[$key]->makerName,
                    'itemName' => $orderItems[$key]->itemName,
                    'itemStandard' => $orderItems[$key]->itemStandard,
                    'itemCode' => $orderItems[$key]->itemCode,
                    'quantityUnit' => $orderItems[$key]->quantityUnit,
                    'itemJANCode' => $orderItems[$key]->itemJANCode,
                    'receivingFlag' => $orderItems[$key]->receivingFlag,
                    'lotNum' => $orderItems[$key]->receivingNowCount,
                    'price' => $orderItems[$key]->price,
                    'labelId' => $orderItems[$key]->labelId,
                    'itemUnit' => $orderItems[$key]->itemUnit,
                    'remainingCount' => $orderItems[$key]->remainingCount, //今回入庫可能数
                    'lotFlag' => $orderItems[$key]->lotManagement
                ];
                $num++;
            }

            if ($card->orderStatus != 8) // 貸出品以外
            {
/*
                $makeOrderData = [];
                foreach ($orderItems as $data)
                {
                    if ($data->orderQuantity - $data->receivingNowCount <= 0)
                    {
                        $makeOrderData[] = [
                            $data->orderCNumber,
                            'now',
                            '1'
                        ];
                    }
                }
*/
                $pattern = $this->checkPattern($orderItems);
                $makeOrderHistoryData = [];
                $receivingTime = null;
                if ($pattern == 6) { $receivingTime = 'now'; }
                $makeOrderHistoryData = ['receivingTime' => $receivingTime];
                if ($pattern == 6 || $pattern == 5) { $makeOrderHistoryData += ['orderStatus' => $pattern]; }

                $result = OrderHistory::where('orderNumber',$card->orderNumber)->where('orderAuthKey',$card->orderAuthKey)->update($makeOrderHistoryData);
            }

            $crypt   = $SPIRAL->getSpiralCryptOpenSsl();
            $authKeyCrypt = $crypt->encrypt($card->orderAuthKey, 'JoyPla');

            $crypt   = $SPIRAL->getSpiralCryptOpenSsl();
            $divisionIdCrypt = $crypt->encrypt($card->divisionId, 'JoyPla');

            $api_url = '%url/rel:mpgt:Receipt%';

            $content = $this->view('NewJoyPla/view/OrderSlipDetail', [
                'user_info' => $user_info,
                'api_url' => $api_url,
                'csrf_token' => Csrf::generate(16),
                'orderItems' => $orderItems,
                'pattern' => $pattern,
                'ItemsToJs' => $ItemsToJs,
                'divisionIdCrypt' => $divisionIdCrypt,
                'authKeyCrypt' => $authKeyCrypt,
                'link' => '%url/rel:mpgt:Receipt%&Action=OrederList',
                'cardId' => $cardId
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

    private function checkPattern(array $array)
    {
        $checkList1 = [];
        $checkList2 = [];
        foreach ($array as $record)
        {
            if ($record->receivingNowCount != 0) { $checkList1[] = $record; }
            if ($record->receivingFlag) { $checkList2[] = $record; }
        }
                        
        if (count($array) == count($checkList2)) { return 6; }
        if (count($checkList1) != 0 ) {return 5; }
        return 0;
    }
}

/***
 * 実行
 */
$OrderSlipDetailControllerController = new OrderSlipDetailController();

$action = $SPIRAL->getParam('Action');

{
    echo $OrderSlipDetailControllerController->index()->render();
}