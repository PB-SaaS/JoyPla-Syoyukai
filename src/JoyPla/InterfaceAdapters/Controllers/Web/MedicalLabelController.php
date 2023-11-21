<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web;

use Exception;
use framework\Http\Request;
use framework\Http\Controller;
use framework\Http\View;
use JoyPla\Enterprise\Models\HospitalId;
use JoyPla\Enterprise\Models\DivisionId;
use JoyPla\Enterprise\Models\InHospitalItemId;
use JoyPla\InterfaceAdapters\GateWays\ModelRepository;
use JoyPla\Service\Repository\RepositoryProvider;

class MedicalLabelController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function MedicalOrderLabelPrint(array $vars)
    {
        $orderId = $vars['targetId'];

        $request = $this->request->get('request' , []);

        $order = ModelRepository::getOrderInstance()
        ->where('hospitalId', $this->request->user()->hospitalId)
        ->where('orderNumber', $orderId)
        ->get()
        ->first();

        if(
            gate('is_user') &&
            $order->divisionId !== $this->request->user()->divisionId
        ){
            Router::abort(403);
        }

        $orderItems = ModelRepository::getOrderItemInstance()
        ->where('hospitalId', $this->request->user()->hospitalId)
        ->where('orderNumber', $orderId)
        ->get();

        $orderItems = $orderItems->toArray();

        foreach( $orderItems as $orderItem )
        {
            $requestItem = array_find($request, function($req) use ($orderItem){
                return $orderItem['inHospitalItemId'] === $req['targetItemId'];
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
        $divisionId = new DivisionId($order->divisionId);
        $division = $repository->getDivisionRepository()->find(new HospitalId($order->hospitalId), new DivisionId($order->divisionId));

        $inHospitalItems = $repository->getInHospitalItemRepository()->getInHospitalItemViewByInHospitalItemIds(
            new HospitalId($this->request->user()->hospitalId),
            array_map(function($item){
                return new InHospitalItemId($item['inHospitalItemId']);
            },$requestPrint)
        );

        foreach($inHospitalItems as $key => $item)
        {
            foreach($requestPrint as $rKey => $printItem)
            {
                if($item->inHospitalItemId === $printItem['inHospitalItemId'])
                {
                    if(!$inHospitalItems[$key]->target){
                        $inHospitalItems[$key]->target = [];
                    }
                    $inHospitalItems[$key]->target[] = $printItem;
                }
                $inHospitalItems[$key]->quantity = $printItem['orderQuantity'];
            }
            $inHospitalItems[$key]->divisionName = $division->getDivisionName()->value();
        }

        $inHospitalItems = array_map(function($inHospitalItem){
            return $inHospitalItem->toArray();
        },$inHospitalItems);

        $labeldesign = $hospital->labelDesign2 !== '' ?  $hospital->labelDesign2 : $this->defaultDesign2();

        $words = $this->convertInputDateForOrder($inHospitalItems);
        $body = View::forge('html/LabelPrint/Medical/Label', [
            'targetId' => $orderId,
            'targetPath' => '/label/medicalOrder/',
            'inHospitalItems' => $inHospitalItems,
            'totalPrintCount' => count($words),
            'labelHtml' => $this->convertKeyword($labeldesign , $words),
        ], false)->render();

        echo view('html/Common/Template', compact('body'), false)->render();
    }
    public function MedicalReceivedLabelPrint(array $vars) {
        $receivingHId = $vars['targetId'];

        $request = $this->request->get('request' , []);

        $received = ModelRepository::getReceivedInstance()
        ->where('hospitalId', $this->request->user()->hospitalId)
        ->where('receivingHId', $receivingHId)
        ->get()
        ->first();

        if(
            gate('is_user') &&
            $received->divisionId !== $this->request->user()->divisionId
        ){
            Router::abort(403);
        }

        $receivedItems = ModelRepository::getReceivedItemInstance()
        ->where('hospitalId', $this->request->user()->hospitalId)
        ->where('receivingHId', $receivingHId)
        ->get();

        $receivedItems = $receivedItems->toArray();

        foreach( $receivedItems as $receivedItem )
        {
            $requestItem = array_find($request, function($req) use ($receivedItem){
                return $receivedItem['inHospitalItemId'] === $req['targetItemId'];
            });

            $print = [];
            if(empty($requestItem['print']))
            {
                $print[] = [
                    'count' => $receivedItem['quantity'], //数量欄
                    'print' => 1 // 初期値
                ];
            } else {
                $print = $requestItem['print'];
            }

            $requests[] = [
                'inHospitalItemId' => $receivedItem['inHospitalItemId'],
                'print'            => $print,
            ];
        }

        $requestPrint = $requests;

        $repository = new RepositoryProvider();
        $hospital = $repository->getHospitalRepository()->findRow(new HospitalId($this->request->user()->hospitalId));
        $divisionId = new DivisionId($received->divisionId);
        $division = $repository->getDivisionRepository()->find(new HospitalId($received->hospitalId), $divisionId);

        $inHospitalItems = $repository->getInHospitalItemRepository()->getInHospitalItemViewByInHospitalItemIds(
            new HospitalId($this->request->user()->hospitalId),
            array_map(function($item){
                return new InHospitalItemId($item['inHospitalItemId']);
            },$requestPrint)
        );

        foreach($inHospitalItems as $key => $item)
        {
            foreach($requestPrint as $rKey => $printItem)
            {
                if($item->inHospitalItemId === $printItem['inHospitalItemId'])
                {
                    if(!$inHospitalItems[$key]->target){
                        $inHospitalItems[$key]->target = [];
                    }
                    $inHospitalItems[$key]->target[] = $printItem;
                }
                // $inHospitalItems[$key]->quantity = $printItem['orderQuantity'];
            }
            $inHospitalItems[$key]->divisionName = $division->getDivisionName()->value();
        }

        $inHospitalItems = array_map(function($inHospitalItem){
            return $inHospitalItem->toArray();
        },$inHospitalItems);

        $labeldesign = $hospital->labelDesign2 !== '' ?  $hospital->labelDesign2 : $this->defaultDesign2();

        $words = $this->convertInputDateForOrder($inHospitalItems);
        $body = View::forge('html/LabelPrint/Medical/Label', [
            'targetId' => $receivingHId,
            'targetPath' => '/label/medicalReceived/',
            'inHospitalItems' => $inHospitalItems,
            'totalPrintCount' => count($words),
            'labelHtml' => $this->convertKeyword($labeldesign , $words),
        ], false)->render();

        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function MedicalPayoutLabelPrint(array $vars)
    {
        $payoutHistoryId = $vars['targetId'];

        $request = $this->request->get('request' , []);

        $payout = ModelRepository::getPayoutInstance()
        ->where('hospitalId', $this->request->user()->hospitalId)
        ->where('payoutHistoryId', $payoutHistoryId)
        ->get()
        ->first();

        if(
            gate('is_user') &&
            $payout->sourceDivisionId !== $this->request->user()->divisionId &&
            $payout->targetDivisionId !== $this->request->user()->divisionId
        ){
            Router::abort(403);
        }

        $payoutItems = ModelRepository::getPayoutItemInstance()
        ->where('hospitalId', $this->request->user()->hospitalId)
        ->where('payoutHistoryId', $payoutHistoryId)
        ->get();

        $payoutItems = $payoutItems->toArray();

        foreach( $payoutItems as $payoutItem )
        {
            $requestItem = array_find($request, function($req) use ($payoutItem){
                return $payoutItem['inHospitalItemId'] === $req['targetItemId'];
            });

            $print = [];
            if(empty($requestItem['print']))
            {
                $print[] = [
                    'count' => $payoutItem['quantity'], //数量欄
                    'print' => $payoutItem['payoutQuantity'],
                ];
            } else {
                $print = $requestItem['print'];
            }

            $requests[] = [
                'inHospitalItemId' => $payoutItem['inHospitalItemId'],
                'print'            => $print,
            ];
        }

        $requestPrint = $requests;

        $repository = new RepositoryProvider();
        $hospital = $repository->getHospitalRepository()->findRow(new HospitalId($this->request->user()->hospitalId));
        $divisionId = new DivisionId($payout->sourceDivisionId);
        $division = $repository->getDivisionRepository()->find(new HospitalId($payout->hospitalId), $divisionId);

        $inHospitalItems = $repository->getInHospitalItemRepository()->getInHospitalItemViewByInHospitalItemIds(
            new HospitalId($this->request->user()->hospitalId),
            array_map(function($item){
                return new InHospitalItemId($item['inHospitalItemId']);
            },$requestPrint)
        );

        foreach($inHospitalItems as $key => $item)
        {
            foreach($requestPrint as $rKey => $printItem)
            {
                if($item->inHospitalItemId === $printItem['inHospitalItemId'])
                {
                    if(!$inHospitalItems[$key]->target){
                        $inHospitalItems[$key]->target = [];
                    }
                    $inHospitalItems[$key]->target[] = $printItem;
                }
                $inHospitalItems[$key]->quantity = $printItem['payoutQuantity'];
            }
            $inHospitalItems[$key]->divisionName = $division->getDivisionName()->value();
        }

        $inHospitalItems = array_map(function($inHospitalItem){
            return $inHospitalItem->toArray();
        },$inHospitalItems);

        $labeldesign = $hospital->labelDesign2 !== '' ?  $hospital->labelDesign2 : $this->defaultDesign2();

        $words = $this->convertInputDateForOrder($inHospitalItems);
        $body = View::forge('html/LabelPrint/Medical/Label', [
            'targetId' => $payoutHistoryId,
            'targetPath' => '/label/medicalPayout/',
            'inHospitalItems' => $inHospitalItems,
            'totalPrintCount' => count($words),
            'labelHtml' => $this->convertKeyword($labeldesign , $words),
        ], false)->render();

        echo view('html/Common/Template', compact('body'), false)->render();


    }

    private function convertInputDateForOrder(array $requestData){
        $response = [];
        foreach($requestData as $rkey => $rdata){
            foreach($rdata['target'] as $targetKey => $targetData){
                foreach($targetData['print'] as $tkey => $tdata){
                    for($num = 0 ; $num < $tdata['print'] ; $num++){
                        $response[] = [
                            'printDate' => date('y/m/d'),
                            'itemName' => $rdata['itemName'],
                            'itemStandard' => $rdata['itemStandard'],
                            'itemUnit' => $rdata['itemUnit'],
                            'count' => $tdata['count'],
                            'catalogNo' => $rdata['catalogNo'],
                            'labelId' => $rdata['labelId'],
                            'printCount' => $tdata['print'],
                            'distributorName' => $rdata['distributorName'],
                            'makerName' => $rdata['makerName'],
                            'quantityUnit' => $rdata['quantityUnit'],
                            'divisionName' => $rdata['divisionName'],
                            'officialFlag' => $rdata['officialFlag'],
                            'medicineCategory' => $rdata['medicineCategory'],
                            'officialprice' => $rdata['officialprice'],
                        ];
                    }
                }
            }
        }
        return $response;
    }

    private function defaultDesign2()
    {
        return <<<EOM
    <div class="printarea uk-margin-remove">
    <div class="uk-child-width-1-2 uk-grid" uk-grid="">
        <div class="uk-first-column">
            <b class="font-size-16">償還</b><br>
            <span>%JoyPla:itemName%</span><br>
            <span>%JoyPla:makerName%</span><br>
        </div>
        <div class="uk-text-right uk-padding-remove">
            <span>%JoyPla:printDate%</span><br>
            <b>入数単位:1%JoyPla:quantityUnit%</b><br>
            <b>償還価格:%JoyPla:officialprice%円</b><br>
        </div>
    </div>
    <span>%JoyPla:catalogNo%</span><br>
    <span>%JoyPla:itemStandard%</span><br>
    <span>%JoyPla:medicineCategory%</span><br>
    <br>
    <span>%JoyPla:distributorName%</span><br>
    </div>

EOM;
    }

    private function convertKeyword(string $template, array $inputData){
        $html = '';
        foreach($inputData as $key => $input)
        {
            $design = $template;
            $design = str_replace('%JoyPla:itemName%',			$input['itemName'],                 $design);//商品名
            $design = str_replace('%JoyPla:makerName%',		$input['makerName'], 		        $design);//メーカー名
            $design = str_replace('%JoyPla:printDate%',		$input['printDate'], 					$design);//印刷日
            $design = str_replace('%JoyPla:quantityUnit%',		$input['quantityUnit'],	            $design);//入数単位
            $design = str_replace('%JoyPla:officialprice%',	number_format_jp((float)$input['officialprice']),  $design);//償還価格
            $design = str_replace('%JoyPla:catalogNo%',		$input['catalogNo'], 		        $design);//カタログNo
            $design = str_replace('%JoyPla:itemStandard%',		$input['itemStandard'], 		    $design);//規格
            $replacedMedicineCategory = str_replace("\n", '<br>', $input['medicineCategory']);
            $design = str_replace('%JoyPla:medicineCategory%',	$replacedMedicineCategory, 		$design);//(特定保険材料名称「保険請求分類(医科)」)
            $design = str_replace('%JoyPla:distributorName%',		$input['distributorName'], 		        $design);//卸業者            
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
                            'printDate' => date('y/m/d'),
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
                            'lotDate' => $paydata['lotDate'],
                            'medicineCategory' => $rdata['medicineCategory'],
                            'officialprice' => $rdata['officialprice'],
                        ];
                    }
                }
            }
        }
        return $response;
    }


}