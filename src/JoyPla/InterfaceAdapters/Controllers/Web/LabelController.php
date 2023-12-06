<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\Enterprise\Models\Stock;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use JoyPla\Service\Repository\RepositoryProvider;

class LabelController extends Controller
{
    public function payoutLabelPrintForAcceptance($vars)
    {
        $acceptanceId = $vars['acceptanceId'];
        $request = $this->request->get('request' , []);
        $acceptance = ModelRepository::getAcceptanceInstance()->where('hospitalId',$this->request->user()->hospitalId)->where('acceptanceId', $acceptanceId)->get()->first();
        
        if(
            gate('is_user') &&
            $acceptance->sourceDivisionId !== $this->request->user()->divisionId &&
            $acceptance->targetDivisionId !== $this->request->user()->divisionId
        ){
            Router::abort(403);
        }
        $sourceDivisionId = new DivisionId($acceptance->sourceDivisionId);
        $divisionId = new DivisionId($acceptance->targetDivisionId);

        $acceptanceItems = ModelRepository::getAcceptanceItemInstance()->where('acceptanceId', $acceptanceId)->get();
        $acceptanceItems = $acceptanceItems->toArray();
        
        $requests = [];
        foreach( $acceptanceItems as $acceptanceItem )
        {
            $requestItem = array_find($request, function($req) use ($acceptanceItem){
                return $acceptanceItem['acceptanceItemId'] === $req['acceptanceItemId'];
            });

            $print = [];
            if(empty($requestItem['print']))
            {
                $print[] = [
                    'count' => $acceptanceItem['acceptanceCount'],
                    'print' => 1,
                ];
            } else {
                $print = $requestItem['print'];
            }
            
            $requests[] = [
                'acceptanceItemId' => $acceptanceItem['acceptanceItemId'],
                'inHospitalItemId' => $acceptanceItem['inHospitalItemId'],
                'lotNumber' => $acceptanceItem['lotNumber'],
                'lotDate' => $acceptanceItem['lotDate'],
                'print' => $print,
            ];
        }
        
        $requestPrint = $requests;

        $repository = new RepositoryProvider();
        
        $hospital = $repository->getHospitalRepository()->findRow(new HospitalId($this->request->user()->hospitalId));
       
        $divisionIds = [];
        $divisionIds[] = $divisionId;

        if($sourceDivisionId){
            $divisionIds[] =  $sourceDivisionId;
        } 

        $inHospitalItems = $repository->getInHospitalItemRepository()->getInHospitalItemViewByInHospitalItemIds(
            new HospitalId($this->request->user()->hospitalId),
            array_map(function($item){
                return new InHospitalItemId($item['inHospitalItemId']);
            },$requestPrint)
        );

        $stocksInstance = ModelRepository::getStockItemViewInstance()->where('hospitalId', $this->request->user()->hospitalId);
        foreach($divisionIds as $did){
            $stocksInstance->orWhere('divisionId', $did->value());
        }
        foreach($requestPrint as $item){
            $stocksInstance->orWhere('inHospitalItemId', $item['inHospitalItemId']);
        }

        $stocks = $stocksInstance->resetValue([
            'divisionId', 'divisionName', 'inHospitalItemId', 'rackName','constantByDiv'
        ])->get();

        foreach($inHospitalItems as $key => $item)
        {
            foreach($requestPrint as $rKey => $printItem)
            {
                if($item->inHospitalItemId === $printItem['inHospitalItemId'])
                {
                    if(!$inHospitalItems[$key]->payout){
                        $inHospitalItems[$key]->payout = [];
                    }
                    $inHospitalItems[$key]->payout[] = $printItem;
                }
            }
        }

        $inHospitalItems = array_map(function($inHospitalItem){
            return $inHospitalItem->toArray();
        },$inHospitalItems);

        $inHospitalItems = array_map(function($inHospitalitem) use ( $stocks , $divisionId , $sourceDivisionId){
            $stock = array_find( $stocks, function($item) use ($inHospitalitem, $divisionId){
                return $item->inHospitalItemId === $inHospitalitem['inHospitalItemId'] && $item->divisionId === $divisionId->value();
            });
        
            $inHospitalitem['target'] = $stock->toArray();
            if($sourceDivisionId != ''){
                $stock = array_find( $stocks, function($item) use ($inHospitalitem, $sourceDivisionId){
                    return $item->inHospitalItemId === $inHospitalitem['inHospitalItemId'] && $item->divisionId === $sourceDivisionId->value();
                });
                $inHospitalitem['source'] = $stock->toArray();
            }
            return $inHospitalitem;
        },$inHospitalItems);
         
        $labeldesign = $hospital->labelDesign2 !== '' ?  $hospital->labelDesign2 : $this->defaultDesign2();

        $words = $this->convertInputDataForAcceptance($inHospitalItems);
        $body = View::forge('html/Label/Acceptance', [
            'acceptanceId' => $acceptanceId,
            'inHospitalItems' => $inHospitalItems,
            'totalPrintCount' => count($words),
            'labelHtml' => $this->convertKeyword($labeldesign , $words),
        ], false)->render();

        echo view('html/Common/Template', compact('body'), false)->render();
    }
    
    public function payoutLabelPrint($vars)
    {
        $payoutId = $vars['payoutId'];

        $request = $this->request->get('request' , []);

        $payout = ModelRepository::getPayoutInstance()->where('hospitalId',$this->request->user()->hospitalId)->where('payoutHistoryId', $payoutId)->get()->first();

        if(
            gate('is_user') &&
            $payout->sourceDivisionId !== $this->request->user()->divisionId &&
            $payout->targetDivisionId !== $this->request->user()->divisionId
        ){
            Router::abort(403);
        }

        $sourceDivisionId = new DivisionId($payout->sourceDivisionId);
        $divisionId = new DivisionId($payout->targetDivisionId);

        $payoutItems = ModelRepository::getPayoutItemInstance()->where('hospitalId',$this->request->user()->hospitalId)->where('payoutHistoryId', $payoutId)->get();
        $payoutItems = $payoutItems->toArray();

        $requests = [];
        foreach( $payoutItems as $payoutItem )
        {
            $requestItem = array_find($request, function($req) use ($payoutItem){
                return $payoutItem['payoutId'] === $req['payoutItemId'];
            });

            $print = [];
            if(empty($requestItem['print']))
            {
                $print[] = [
                    'count' => ( $payoutItem['payoutCount'] != '' ) ? $payoutItem['payoutCount']  : $payoutItem['payoutQuantity'],
                    'print' => ( $payoutItem['payoutLabelCount'] != '' ) ? $payoutItem['payoutLabelCount']  : 1,
                ];
            } else {
                $print = $requestItem['print'];
            }
            
            $requests[] = [
                'payoutItemId' => $payoutItem['payoutId'],
                'inHospitalItemId' => $payoutItem['inHospitalItemId'],
                'lotNumber' => $payoutItem['lotNumber'],
                'lotDate' => $payoutItem['lotDate'],
                'print' => $print,
            ];
        }

        $requestPrint = $requests;

        $repository = new RepositoryProvider();
        
        $hospital = $repository->getHospitalRepository()->findRow(new HospitalId($this->request->user()->hospitalId));
       
        $divisionIds = [];
        $divisionIds[] = $divisionId;

        if($sourceDivisionId){
            $divisionIds[] =  $sourceDivisionId;
        } 

        $inHospitalItems = $repository->getInHospitalItemRepository()->getInHospitalItemViewByInHospitalItemIds(
            new HospitalId($this->request->user()->hospitalId),
            array_map(function($item){
                return new InHospitalItemId($item['inHospitalItemId']);
            },$requestPrint)
        );

        $stocksInstance = ModelRepository::getStockItemViewInstance()->where('hospitalId', $this->request->user()->hospitalId);
        foreach($divisionIds as $did){
            $stocksInstance->orWhere('divisionId', $did->value());
        }
        foreach($requestPrint as $item){
            $stocksInstance->orWhere('inHospitalItemId', $item['inHospitalItemId']);
        }

        $stocks = $stocksInstance->resetValue([
            'divisionId', 'divisionName', 'inHospitalItemId', 'rackName','constantByDiv'
        ])->get();

        foreach($inHospitalItems as $key => $item)
        {
            foreach($requestPrint as $rKey => $printItem)
            {
                if($item->inHospitalItemId === $printItem['inHospitalItemId'])
                {
                    if(!$inHospitalItems[$key]->payout){
                        $inHospitalItems[$key]->payout = [];
                    }
                    $inHospitalItems[$key]->payout[] = $printItem;
                }
            }
        }

        $inHospitalItems = array_map(function($inHospitalItem){
            return $inHospitalItem->toArray();
        },$inHospitalItems);
        $inHospitalItems = array_map(function($inHospitalitem) use ( $stocks , $divisionId , $sourceDivisionId){
            $stock = array_find( $stocks, function($item) use ($inHospitalitem, $divisionId){
                return $item->inHospitalItemId === $inHospitalitem['inHospitalItemId'] && $item->divisionId === $divisionId->value();
            });
        
            $inHospitalitem['target'] = $stock->toArray();
            if($sourceDivisionId != ''){
                $stock = array_find( $stocks, function($item) use ($inHospitalitem, $sourceDivisionId){
                    return $item->inHospitalItemId === $inHospitalitem['inHospitalItemId'] && $item->divisionId === $sourceDivisionId->value();
                });
                $inHospitalitem['source'] = $stock->toArray();
            }
            return $inHospitalitem;
        },$inHospitalItems);

        $labeldesign = $hospital->labelDesign2 !== '' ?  $hospital->labelDesign2 : $this->defaultDesign2();

        $words = $this->convertInputDataForPayout($inHospitalItems);
        $body = View::forge('html/Label/Payout', [
            'payoutId' => $payoutId,
            'inHospitalItems' => $inHospitalItems,
            'totalPrintCount' => count($words),
            'labelHtml' => $this->convertKeyword($labeldesign , $words),
        ], false)->render();

        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function orderLabelPrint($vars)
    {
        $orderNumber = $vars['orderNumber'];

        $request = $this->request->get('request' , []);

        $order = ModelRepository::getOrderInstance()->where('hospitalId',$this->request->user()->hospitalId)->where('orderNumber', $orderNumber)->get()->first();

        if(
            gate('is_user') &&
            $order->divisionId !== $this->request->user()->divisionId
        ){
            Router::abort(403);
        }

        $divisionId = new DivisionId($order->divisionId);

        $orderItems = ModelRepository::getOrderItemViewInstance()->where('hospitalId',$this->request->user()->hospitalId)->where('orderNumber', $orderNumber)->get();
        $orderItems = $orderItems->toArray();

        $requests = [];
        foreach( $orderItems as $orderItem )
        {
            $requestItem = array_find($request, function($req) use ($orderItem){
                return $orderItem['orderCNumber'] === $req['orderCNumber'];
            });

            $print = [];
            if(empty($requestItem['print']))
            {
                $print[] = [
                    'count' => $orderItem['quantity'], //数量欄
                    'print' => $orderItem['orderQuantity'], //多分枚数欄：発注数ベース
                ];
            } else {
                $print = $requestItem['print'];
            }
            
            $requests[] = [
                'orderCNumber'     => $orderItem['orderCNumber'],
                'inHospitalItemId' => $orderItem['inHospitalItemId'],
                'orderQuantity'    => $orderItem['orderQuantity'],
                'print'            => $print,
            ];
        }

        $requestPrint = $requests;

        $repository = new RepositoryProvider();

        $hospital = $repository->getHospitalRepository()->findRow(new HospitalId($this->request->user()->hospitalId));
        $division = $repository->getDivisionRepository()->find(new HospitalId($order->hospitalId), new DivisionId($order->divisionId));

        $inHospitalItems = $repository->getInHospitalItemRepository()->getInHospitalItemViewByInHospitalItemIds(
            new HospitalId($this->request->user()->hospitalId),
            array_map(function($item){
                return new InHospitalItemId($item['inHospitalItemId']);
            },$requestPrint)
        );
/* 
        $stocksInstance = ModelRepository::getStockItemViewInstance()->where('hospitalId', $this->request->user()->hospitalId)->orWhere('divisionId', $divisionId->value());
        foreach($requestPrint as $item){
            $stocksInstance->orWhere('inHospitalItemId', $item['inHospitalItemId']);
        }

        $stocks = $stocksInstance->resetValue([
            'divisionId', 'divisionName', 'inHospitalItemId', 'rackName','constantByDiv'
        ])->get();
 */
        foreach($inHospitalItems as $key => $item)
        {
            foreach($requestPrint as $rKey => $printItem)
            {
                if($item->inHospitalItemId === $printItem['inHospitalItemId'])
                {
                    if(!$inHospitalItems[$key]->order){
                        $inHospitalItems[$key]->order = [];
                    }
                    $inHospitalItems[$key]->order[] = $printItem;
                }
                $inHospitalItems[$key]->quantity = $printItem['orderQuantity'];
            }
            $inHospitalItems[$key]->divisionName = $division->getDivisionName()->value();
        }

        $inHospitalItems = array_map(function($inHospitalItem){
            return $inHospitalItem->toArray();
        },$inHospitalItems);
/* 
        $inHospitalItems = array_map(function($inHospitalitem) use ( $stocks , $divisionId , $sourceDivisionId){
            $stock = array_find( $stocks, function($item) use ($inHospitalitem, $divisionId){
                return $item->inHospitalItemId === $inHospitalitem['inHospitalItemId'] && $item->divisionId === $divisionId->value();
            });
        
            $inHospitalitem['target'] = $stock->toArray();
            if($sourceDivisionId != ''){
                $stock = array_find( $stocks, function($item) use ($inHospitalitem, $sourceDivisionId){
                    return $item->inHospitalItemId === $inHospitalitem['inHospitalItemId'] && $item->divisionId === $sourceDivisionId->value();
                });
                $inHospitalitem['source'] = $stock->toArray();
            }
            return $inHospitalitem;
        },$inHospitalItems);
 */

        $customLabelInstance = ModelRepository::getCustomLabelInstance()->where('hospitalId', $hospital->hospitalId)->where('designType', 1)->orderBy('id', 'desc')->get();
        $labeldesign = $customLabelInstance->count() !== 0 ? $customLabelInstance->first()->labelDesign : $this->orderLabelDefaultDesign();

        $words = $this->convertInputDateForOrder($inHospitalItems);
        $body = View::forge('html/Label/Order', [
            'orderNumber'     => $orderNumber,
            'inHospitalItems' => $inHospitalItems,
            'totalPrintCount' => count($words),
            'labelHtml'       => $this->convertKeyword($labeldesign , $words),
        ], false)->render();

        echo view('html/Common/Template', compact('body'), false)->render();
    }

    private function defaultDesign2()
    {
        return <<<EOM
<div class='printarea uk-margin-remove'>
    <div>
        <b class='font-size-16'>%JoyPla:itemName%</b>
        <div class='uk-child-width-1-2' uk-grid>
            <div class=''>
                <span>%JoyPla:itemMaker%</span><br>
                <span>%JoyPla:catalogNo% %JoyPla:itemStandard%</span><br>
                <span>%JoyPla:inHPId%</span><br>
                <span>%JoyPla:lotNumber%</span><br>
                <span>%JoyPla:lotDate%</span><br>
            </div>
            <div class='uk-text-right uk-padding-remove'>
                <b>%JoyPla:sourceDivisionName%</b> <span>元棚番:%JoyPla:sourceRackName%</span><br>
                <b>%JoyPla:divisionName%</b> <span>払出棚番:%JoyPla:rackName%</span><br>
                <span>定数:%JoyPla:constantByDiv%%JoyPla:quantityUnit%</span><br>
                <span class='uk-text-bold' style='font-size:1.25em'>入数:%JoyPla:quantity%%JoyPla:quantityUnit%</span><br>
            </div>
        </div>
        <div class='uk-text-center' id='barcode_%JoyPla:num%'>%JoyPla:barcodeId%</div>
        <div class='uk-text-right'>%JoyPla:distributorName%</div>
    </div>
</div>
EOM;
    }

    private function orderLabelDefaultDesign()
    {
        return <<<EOM
<div class='printarea uk-margin-remove'>
    <div>
        <div class='uk-child-width-1-2' uk-grid>
            <div class=''>
                <span>%JoyPla:distributorName%</span><br>
                <span>%JoyPla:itemMaker%</span><br>
                <span>%JoyPla:itemName%</span><br>
                <span>%JoyPla:itemStandard%</span><br>
                <span>%JoyPla:itemCode%</span><br>
                <span>%JoyPla:divisionName%</span><br>
            </div>
            <div class='uk-text-right uk-padding-remove'>
                <div class="h-12"></div>
                <span>入数:%JoyPla:quantity%%JoyPla:quantityUnit%</span><br>
            </div>
        </div>
        <div class='uk-text-center' id='barcode_%JoyPla:num%'>%JoyPla:barcodeId%</div>
    </div>
</div>
EOM;
    }

    private function convertKeyword(string $template, array $inputData){
        $html = '';
        foreach($inputData as $key => $input)
        {
            $design = $template;
            $design = str_replace('%JoyPla:nowTime%',              $input['nowTime'],                                                 $design);//バーコードの値
            $design = str_replace('%JoyPla:barcodeId%',            $input['barcode'],                                                 $design);//バーコードの値
            $design = str_replace('%JoyPla:num%',                  $key + 1,                                                          $design);//枚目
            $design = str_replace('%JoyPla:inHPId%',               $input['inHospitalItemId'],                                        $design);//院内商品ID
            $design = str_replace('%JoyPla:itemName%',             $input['itemName'],                                                $design);//商品名
            $design = str_replace('%JoyPla:itemCode%',             $input['itemCode'],                                                $design);//製品コードb
            $design = str_replace('%JoyPla:itemStandard%',         $input['itemStandard'],                                            $design);//商品規格
            $design = str_replace('%JoyPla:itemJANCode%',          $input['itemJANCode'],                                             $design);//JANコードb
            $design = str_replace('%JoyPla:itemUnit%',             $input['itemUnit'],                                                $design);//個数単位
            $design = str_replace('%JoyPla:quantity%',             $input['count'],                                                   $design);//入り数
            $design = str_replace('%JoyPla:catalogNo%',            $input['catalogNo'],                                               $design);//カタログ名
            $design = str_replace('%JoyPla:labelId%',              $input['labelId'],                                                 $design);//ラベルID
            $design = str_replace('%JoyPla:printCount%',           $input['printCount'],                                              $design);//印刷数
            $design = str_replace('%JoyPla:distributorName%',      $input['distributorName'],                                         $design);//卸業者名
            $design = str_replace('%JoyPla:itemMaker%',            $input['makerName'],                                               $design);//メーカー名
            $design = str_replace('%JoyPla:quantityUnit%',         $input['quantityUnit'],                                            $design);//入数単位
            $design = str_replace('%JoyPla:sourceDivisionName%',   $input['sourceDivisionName'],                                      $design);//払い出し元部署
            $design = str_replace('%JoyPla:sourceRackName%',       ($input['sourceRackName'])? $input['sourceRackName'] : '(登録なし)', $design);//払い出し元部署棚
            $design = str_replace('%JoyPla:divisionName%',         $input['divisionName'],                                            $design);//払い出し先部署 
            $design = str_replace('%JoyPla:rackName%',             ($input['rackName'])? $input['rackName'] : '(登録なし)',             $design);//払い出し先部署棚
            $design = str_replace('%JoyPla:constantByDiv%',        ($input['constantByDiv'])? $input['constantByDiv'] : 0,            $design);//払い出し先部署定数
            $design = str_replace('%JoyPla:officialFlag%',         ($input['officialFlag'] === '1')?"償還" : "",                       $design);//償還フラグ
            $design = str_replace('%JoyPla:officialFlag:id%',      $input['officialFlag'],                                            $design);//償還フラグ id
            $design = str_replace('%JoyPla:lotNumber%',            $input['lotNumber'],                                               $design);//ロット
            $design = str_replace('%JoyPla:lotDate%',              $input['lotDate'],                                                 $design);//使用期限
            $html .= $design;
        }
        return $html;
    }
    private function convertInputDataForPayout(array $requestData)
    {
        $response = [];
        foreach($requestData as $rkey => $rdata){
            foreach($rdata['payout'] as $paykey => $paydata){
                foreach($paydata['print'] as $pkey => $pdata){
                    for($num = 0 ; $num < $pdata['print'] ; $num++){
                        $response[] = [
                            'nowTime' => date('Y年m月d日 H時i分s秒'),
                            'barcode' => '30' . str_replace('payout_', '', $paydata['payoutItemId']) . $this->convertNumber($pdata['count']),
                            'inHospitalItemId' => $paydata['inHospitalItemId'],
                            'itemName' => $rdata['itemName'],
                            'itemCode' => $rdata['itemCode'],
                            'itemStandard' => $rdata['itemStandard'],
                            'itemJANCode' => $rdata['itemJANCode'],
                            'itemUnit' => $rdata['itemUnit'],
                            'count' => $pdata['count'],
                            'catalogNo' => $rdata['catalogNo'],
                            'labelId' => $rdata['labelId'],
                            'printCount' => $pdata['print'],
                            'distributorName' => $rdata['distributorName'],
                            'makerName' => $rdata['makerName'],
                            'quantityUnit' => $rdata['quantityUnit'],
                            'divisionName' => $rdata['target']['divisionName'],
                            'rackName' => $rdata['target']['rackName'],
                            'sourceDivisionName' => $rdata['source']['divisionName'],
                            'sourceRackName' => $rdata['source']['rackName'],
                            'constantByDiv' => $rdata['target']['constantByDiv'],
                            'officialFlag' => $rdata['officialFlag'],
                            'lotNumber' => $paydata['lotNumber'],
                            'lotDate' => $paydata['lotDate']
                        ];
                    }
                }
            }
        }
        return $response;
    }
    private function convertInputDataForAcceptance(array $requestData)
    {
        $response = [];
        foreach($requestData as $rkey => $rdata){
            foreach($rdata['payout'] as $paykey => $paydata){
                foreach($paydata['print'] as $pkey => $pdata){
                    for($num = 0 ; $num < $pdata['print'] ; $num++){
                        $response[] = [
                            'nowTime' => date('Y年m月d日 H時i分s秒'),
                            'barcode' => $paydata['acceptanceItemId']. $this->convertNumber($pdata['count']),
                            'inHospitalItemId' => $paydata['inHospitalItemId'],
                            'itemName' => $rdata['itemName'],
                            'itemCode' => $rdata['itemCode'],
                            'itemStandard' => $rdata['itemStandard'],
                            'itemJANCode' => $rdata['itemJANCode'],
                            'itemUnit' => $rdata['itemUnit'],
                            'count' => $pdata['count'],
                            'catalogNo' => $rdata['catalogNo'],
                            'labelId' => $rdata['labelId'],
                            'printCount' => $pdata['print'],
                            'distributorName' => $rdata['distributorName'],
                            'makerName' => $rdata['makerName'],
                            'quantityUnit' => $rdata['quantityUnit'],
                            'divisionName' => $rdata['target']['divisionName'],
                            'rackName' => $rdata['target']['rackName'],
                            'sourceDivisionName' => $rdata['source']['divisionName'],
                            'sourceRackName' => $rdata['source']['rackName'],
                            'constantByDiv' => $rdata['target']['constantByDiv'],
                            'officialFlag' => $rdata['officialFlag'],
                            'lotNumber' => $paydata['lotNumber'],
                            'lotDate' => $paydata['lotDate']
                        ];
                    }
                }
            }
        }
        return $response;
    }

    private function convertInputDateForOrder(array $requestData){
        $response = [];
        foreach($requestData as $rkey => $rdata){
            foreach($rdata['order'] as $orderKey => $orderData){
                foreach($orderData['print'] as $okey => $odata){
                    for($num = 0 ; $num < $odata['print'] ; $num++){
                        $response[] = [
                            'nowTime' => date('Y年m月d日 H時i分s秒'),
                            'barcode' => '80' . str_replace('BO', '', $orderData['orderCNumber']),
                            'inHospitalItemId' => $orderData['inHospitalItemId'],
                            'itemName' => $rdata['itemName'],
                            'itemCode' => $rdata['itemCode'],
                            'itemStandard' => $rdata['itemStandard'],
                            'itemJANCode' => $rdata['itemJANCode'],
                            'itemUnit' => $rdata['itemUnit'],
                            'count' => $odata['count'],
                            'catalogNo' => $rdata['catalogNo'],
                            'labelId' => $rdata['labelId'],
                            'printCount' => $odata['print'],
                            'distributorName' => $rdata['distributorName'],
                            'makerName' => $rdata['makerName'],
                            'quantityUnit' => $rdata['quantityUnit'],
                            'divisionName' => $rdata['divisionName'],
                            'officialFlag' => $rdata['officialFlag'],
                        ];
                    }
                }
            }
        }
        return $response;
    }
    private function convertNumber($num)
    {
        // 9999を超える場合に9999に制限する
        if ($num > 9999) {
            $num = 9999;
        }

        // 四桁の数値にゼロ埋めする
        $zeroPaddedNum = str_pad($num, 4, '0', STR_PAD_LEFT);

        return $zeroPaddedNum;
    }
}
