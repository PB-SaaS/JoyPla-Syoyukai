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
        $orderId = $vars['orderId'];

        $request = $this->request->get('request' , []);

        $order = ModelRepository::getOrderInstance()
        ->where('hospitalId', $this->request->user()->hospitalId)
        ->where('orderNumber', $orderId)
        ->get()
        ->first();

        $inHospitalItemIds = [];

        $orderItems = ModelRepository::getOrderItemInstance()
        ->where('hospitalId', $this->request->user()->hospitalId)
        ->where('orderNumber', $orderId)
        ->get();

        $orderItems = $orderItems->toArray();

        foreach ($orderItems as $orderItem) {
            $inHospitalItemIds[] = $orderItem['inHospitalItemId'];
        }


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
        $divisionId = new DivisionId($order->divisionId);
        $division = $repository->getDivisionRepository()->find(new HospitalId($order->hospitalId), new DivisionId($order->divisionId));

        $inHospitalItems = $repository->getInHospitalItemRepository()->getInHospitalItemViewByInHospitalItemIds(
            new HospitalId($this->request->user()->hospitalId),
            array_map(function($item){
                return new InHospitalItemId($item['inHospitalItemId']);
            },$requestPrint)
        );



        // ob_start();
        // print_r($request);
        // $result = ob_get_clean();
        // $htmlList = "<pre>" . $result . "</pre>";

        // $listItems = array_map(function($itemId) {
        //     return "<li>{$itemId}</li>";
        // }, $inHospitalItemIds);
        
        // // リストの開始と終了タグを追加
        // $htmlList = "<ul>" . implode('', $listItems) . "</ul>";



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

        $labeldesign = $hospital->labelDesign2 !== '' ?  $hospital->labelDesign2 : $this->defaultDesign2();

        $words = $this->convertInputDateForOrder($inHospitalItems);
        // $body = View::forge('html/Label/Payout', [
        $body = View::forge('html/LabelPrint/Medical/Label', [
            'orderId' => $orderId,
            'inHospitalItems' => $inHospitalItems,
            'totalPrintCount' => count($words),
            'labelHtml' => $this->convertKeyword($labeldesign , $words),
            // 'inHospitalItems' => array(),
            // 'totalPrintCount' => 3,
            // 'labelHtml' => <<<EOM
            // <div class='printarea uk-margin-remove'>
            //     <div>
            //         <b class='font-size-16'>aaaaaaaaaa</b>
            //         $htmlList
            //     </div>
            // </div>
            // EOM,
        ], false)->render();

        echo view('html/Common/Template', compact('body'), false)->render();

    }
    public function MedicalReceivedLabelPrint(array $vars) {

    }
 
    public function index(array $vars)
    {

        $request = $this->request->get('request' , []);
        $inHospitalItems = [];
        $inHospitalItems = array_merge($inHospitalItems, $this->mockHospitalItem());
        $inHospitalItemLabels = [];
        

        function duplicateItemByPrintValue($item) {
            $result = [];
        
            if (isset($item['payout'])) {
                foreach ($item['payout'] as $payout) {
                    if (isset($payout['print'])) {
                        foreach ($payout['print'] as $print) {
                            $printValue = (int)$print['print'];
                            for ($i = 0; $i < $printValue; $i++) {
                                $result[] = $item;
                            }
                        }
                    }
                }
            }
        
            return $result;
        }
        

        // 印刷数を設定
        if(!empty($request)){
            for ($i = 0; $i < count($request); $i++) {
                $newPrintValue = $request[$i]['print'][0]['print'];
                $payoutItemId = $request[$i]['payoutItemId'];
                foreach ($inHospitalItems[$i]['payout'] as &$payout) {
                    if ($payout['payoutItemId'] == $payoutItemId && isset($payout['print'])) {
                        foreach ($payout['print'] as &$print) {
                            $print['print'] = $newPrintValue;
                        }
                    }
                }
            }
        }
       
        // 印刷数の数だけオブジェクトを複製する
        $duplicatedItems = [];
        foreach($inHospitalItems as &$item) {
            $duplicatedItems = array_merge($duplicatedItems, duplicateItemByPrintValue($item));
        }
        $inHospitalItemLabels = $duplicatedItems;
       

        $payoutId ="05652f5f66c6165";
        $labeldesign=$this->defaultDesign();

        $body = View::forge('labelPrint/medical/Label', [
            'payoutId' => $payoutId,
            'inHospitalItems' => $inHospitalItems,
            'totalPrintCount' => count($inHospitalItems),
            'labelHtml' => $this->convertKeyword($labeldesign , $inHospitalItemLabels),
            'request'=>$request, // Debug用
        ], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    private function defaultDesign()
    {

    }
    public function MedicalPayoutLabelPrint(array $vars)
    {

    }
}