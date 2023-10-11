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

class OrderLabelController extends Controller
{
    public function orderLabelPrint($vars)
    {
        $orderIds = $this->request->get('orderIds');

        $request = $this->request->get('request' , []);

        $orders = ModelRepository::getOrderInstance()->where('hospitalId',$this->request->user()->hospitalId)
            ->whereIn('orderNumber', $orderIds)->get();
        
        $divisionIds = [];
        foreach($orders as $order)
        {
            if(
                gate('is_user') &&
                $order->divisionId !== $this->request->user()->divisionId
            ){
                Router::abort(403);
            }
            $divisionIds[] = new DivisionId($order->divisionId);
        }

        $orderItems = ModelRepository::getOrderItemInstance()->where('hospitalId',$this->request->user()->hospitalId)->whereIn('receivingHId', $orderIds)->get();
        $orderItems = $orderItems->toArray();

        $requests = [];
        foreach( $orderItems as $orderItem )
        {
            $requestItem = array_find($request, function($req) use ($orderItem){
                return $orderItem['orderCNumber'] === $req['orderItemId'];
            });

            $print = [];
            if(empty($requestItem['print']))
            {
                $print[] = [
                    'count' => ( $orderItem['orderCount'] != '' ) ? $orderItem['orderCount']  : $orderItem['orderQuantity'],
                    'print' => ( $orderItem['orderLabelCount'] != '' ) ? $orderItem['orderLabelCount']  : 1,
                ];
            } else {
                $print = $requestItem['print'];
            }
            
            $requests[] = [
                'orderItemId' => $orderItem['orderId'],
                'inHospitalItemId' => $orderItem['inHospitalItemId'],
                'lotNumber' => '',
                'lotDate' => '',
                'print' => $print,
            ];
        }

        $requestPrint = $requests;

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

        foreach( $orders as $key => $order ){
            $orders->{$key}->orderItem = [];
            foreach( $orderItems as $orderItem ){

                $orderItem->stock =  array_find(function($stock) use ($orderItem, $order) {
                    return $stock->inHospitalItemId === $orderItem->inHospitalItemId &&
                    $stock->divisionId === $order->divisionId;
                },$stocks);

                if( $order->orderNumber === $orderItem->orderNumber ){
                    $orders->{$key}->orderItem[] = $orderItem;
                }
            }
        }

        $repository = new RepositoryProvider();
        
        $hospital = $repository->getHospitalRepository()->findRow(new HospitalId($this->request->user()->hospitalId));
       
        $labeldesign = $hospital->labelDesign1 !== '' ?  $hospital->labelDesign1 : $this->defaultDesign();

        $words = $this->convertInputData($orders->toArray());
        $body = View::forge('html/Label/Payout', [
            'orderIds' => $orderIds,
            'totalPrintCount' => count($words),
            'labelHtml' => $this->convertKeyword($labeldesign , $words),
        ], false)->render();

        echo view('html/Common/Template', compact('body'), false)->render();
    }
    private function defaultDesign()
    {
        return <<<EOM
    <div class="printarea uk-margin-remove">
		<span>%JoyPla:distributorName%</span><br>
		<span>メーカー名：%JoyPla:itemMaker%</span><br>
		<span>商品名：%JoyPla:itemName%</span><br>
		<span>規格：%JoyPla:itemStandard%</span><br>
		<span>商品コード：%JoyPla:itemCode%</span>
		<span>入数：%JoyPla:quantity%%JoyPla:quantityUnit%</span><br>
		<span>%JoyPla:lotNumber% %JoyPla:lotDate%</span>
		<span>%JoyPla:nowTime%</span><br>
		<div class="uk-text-center" id="barcode_%JoyPla:num%">%JoyPla:barcodeId%</div>
	</div>
EOM;
    }

    private function convertKeyword(string $template, array $inputData){
        $html = '';
        foreach($inputData as $key => $input)
        {
            $design = $template;
            $design = str_replace('%JoyPla:nowTime%',			$input['nowTime'], 									$design);//バーコードの値
            $design = str_replace('%JoyPla:barcodeId%',			$input['barcode'], 								$design);//バーコードの値
            $design = str_replace('%JoyPla:num%',				$key + 1, 										$design);//枚目
            $design = str_replace('%JoyPla:inHPId%',			$input['inHospitalItemId'], 					$design);//院内商品ID
            $design = str_replace('%JoyPla:itemName%',			$input['itemName'],                    		$design);//商品名
            $design = str_replace('%JoyPla:itemCode%',			$input['itemCode'], 		                    $design);//製品コードb
            $design = str_replace('%JoyPla:itemStandard%',		$input['itemStandard'],	                    $design);//商品規格
            $design = str_replace('%JoyPla:itemJANCode%',		$input['itemJANCode'], 	                    $design);//JANコードb
            $design = str_replace('%JoyPla:itemUnit%',			$input['itemUnit'], 		                    $design);//個数単位
            $design = str_replace('%JoyPla:quantity%',			$input['count'], 		                $design);//入り数
            $design = str_replace('%JoyPla:catalogNo%',			$input['catalogNo'], 		                    $design);//カタログ名
            $design = str_replace('%JoyPla:labelId%',$input['labelId'], 		                    $design);//ラベルID
            $design = str_replace('%JoyPla:printCount%',		$input['printCount'],					$design);//印刷数
            $design = str_replace('%JoyPla:distributorName%',	$input['distributorName'],				       	$design);//卸業者名
            $design = str_replace('%JoyPla:itemMaker%',			$input['makerName'], 		                    $design);//メーカー名
            $design = str_replace('%JoyPla:quantityUnit%',		$input['quantityUnit'],	                    $design);//入数単位
            //$design = str_replace('%JoyPla:sourceDivisionName%',$input['sourceDivisionName'],				        $design);//払い出し元部署
            //$design = str_replace('%JoyPla:sourceRackName%',	($input['sourceRackName'])? $input['sourceRackName'] : '(登録なし)', $design);//払い出し元部署棚
            $design = str_replace('%JoyPla:divisionName%',		$input['divisionName'],					    $design);//払い出し先部署 
            $design = str_replace('%JoyPla:rackName%',			($input['rackName'])? $input['rackName'] : '(登録なし)',	$design);//払い出し先部署棚
            $design = str_replace('%JoyPla:constantByDiv%',		($input['constantByDiv'])? $input['constantByDiv'] : 0, $design);//払い出し先部署定数
            $design = str_replace('%JoyPla:officialFlag%',		($input['officialFlag'] === '1')?"償還" : "",								$design);//償還フラグ
            $design = str_replace('%JoyPla:officialFlag:id%',   $input['officialFlag'],					    $design);//償還フラグ id
            $design = str_replace('%JoyPla:lotNumber%',			$input['lotNumber'], 		                    $design);//ロット
            $design = str_replace('%JoyPla:lotDate%',			$input['lotDate'], 		                    $design);//使用期限
            $html .= $design;
        }   
        return $html;
    }
    private function convertInputData(array $orders)
    {
        $response = [];
        foreach($orders as $key => $order){
            var_dump($order);
        }
exit;
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
                            //'sourceDivisionName' => $rdata['source']['divisionName'],
                            //'sourceRackName' => $rdata['source']['rackName'],
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
