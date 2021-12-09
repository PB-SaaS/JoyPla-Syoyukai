<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\Auth;
use App\Model\Distributor;
use App\Model\Division;
use App\Model\InHospitalItemView;
use App\Model\OrderedItemView;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class OrderMRController extends Controller
{
    private $category = [
        '1' => ['label' => '医療材料', 'checked' => ''],
        '2' => ['label' => '薬剤', 'checked' => ''],
        '3' => ['label' => '試薬', 'checked' => ''],
        '4' => ['label' => '日用品', 'checked' => ''],
        '99' => ['label' => 'その他', 'checked' => '']
    ];

    public function __construct()
    {
    }
    
    public function index()
    {
        global $SPIRAL;
        $auth = new Auth();

        $startMonth = date('Y-m-01');
        $endMonth = '';
        $distributorId = '';
        $divisionId = '';
        $page = 1;
        $limit = 100;
        $itemName = '';
        $itemCode = '';
        $itemStandard = '';
        $category = $this->category;
        $category_ids = [];

        if ($SPIRAL->getParam('startMonth')) { $startMonth = $this->html($SPIRAL->getParam('startMonth')); }
        if ($SPIRAL->getParam('endMonth')) { $endMonth = $this->html($SPIRAL->getParam('endMonth')); }
        if ($SPIRAL->getParam('distributorId')) { $distributorId = $this->html($SPIRAL->getParam('distributorId')); }
       
        if ($SPIRAL->getParam('divisionId')) { $divisionId = $this->html($SPIRAL->getParam('divisionId')); }
        
        if ($SPIRAL->getParam('page')) { $page = $this->html($SPIRAL->getParam('page')); }
        if ($SPIRAL->getParam('limit')) { $limit = $this->html($SPIRAL->getParam('limit')); }
        if ($SPIRAL->getParam('itemName')) { $itemName = $this->html($SPIRAL->getParam('itemName')); }
        if ($SPIRAL->getParam('itemCode')) { $itemCode = $this->html($SPIRAL->getParam('itemCode')); }
        if ($SPIRAL->getParam('itemStandard')) { $itemStandard = $this->html($SPIRAL->getParam('itemStandard')); }
        if ($SPIRAL->getParams('category') && is_array($SPIRAL->getParams('category')))
        {
            foreach ($SPIRAL->getParams('category') as $checked)
            {
                if (array_key_exists($checked ,$category))
                {
                    $category[$checked]['checked'] = 'checked';
                    $category_ids[] = $this->html($checked);
                }
            }
        }

        $result = $this->dataSelect($startMonth,$endMonth,$distributorId,$divisionId,$itemName,$itemCode,$itemStandard,$category_ids,$page,$limit);

        $division = Division::where('hospitalId',$SPIRAL->getParam('hospitalId'))->get();

        $distributor_data = Distributor::where('hospitalId',$SPIRAL->getParam('hospitalId'))->get();
        $distributor_data = $distributor_data->data->all();

        $api_url = '%url/rel:mpgt:MonthlyReport%';

        $content = $this->view('NewJoyPlaTenantAdmin/view/History/OrderMR', [
            'api_url' => $api_url,
            'division' => $division,
            'distributor' => $distributor_data,
            'distributorId' => $distributorId,
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'divisionId' => $divisionId,
            'page' => $page,
            'limit' => $limit,
            'itemName' => $itemName,
            'itemCode' => $itemCode,
            'itemStandard' => $itemStandard,
            'category' => $category,
            'report' => $result,
            'hospitalId' => $SPIRAL->getParam('hospitalId'),
            'csrf_token' => Csrf::generate(16)
            ] , false)->render();
            
        return $content;
    }
    
    private function dataSelect(string $startMonth = null, string $endMonth = null, string $distributorId = null, string $divisionId = null, string $itemName = null, string $itemCode = null, string $itemStandard = null, array $category_ids = array(), int $page = null, int $maxCount = null)
    {
        global $SPIRAL;
        
        $report = [];
        $total_amount = 0;
    
        OrderedItemView::where('hospitalId',$SPIRAL->getParam('hospitalId'))->where('orderStatus','1','!=');
        if ($startMonth) { OrderedItemView::where('registrationTime', $startMonth, '>='); }
        if ($endMonth) { OrderedItemView::where('registrationTime', (date('Y-m-d', strtotime($endMonth . '+1 day'))), '<='); }
        if ($divisionId) { OrderedItemView::where('divisionId', $divisionId); }
        if ($distributorId) { OrderedItemView::where('distributorId',$distributorId); }
        $orderDB = OrderedItemView::get();

        if ($orderDB->count == 0)
        {
            return ['data' => [], 'count' => 0, 'totalAmount' => 0];
        }

        $this->order_data = $orderDB->data->all();

        InHospitalItemView::where('hospitalId',$SPIRAL->getParam('hospitalId'));
        foreach ($this->order_data as $order)
        {
            InHospitalItemView::orWhere('inHospitalItemId',$order->inHospitalItemId);
        }
        if ($itemName) { InHospitalItemView::where('itemName',"%$itemName%",'LIKE'); }
        if ($itemCode) { InHospitalItemView::where('itemCode',"%$itemCode%",'LIKE'); }
        if ($itemStandard) { InHospitalItemView::where('itemStandard',"%$itemStandard%",'LIKE'); }
        if ($category_ids)
        {
            foreach ($category_ids as $val)
            {
                InHospitalItemView::orWhere('category',$val);
            }
        }
        if ($page) { InHospitalItemView::page($page); }
        $inHPItem = InHospitalItemView::paginate($maxCount);

        $inHPItem_data = $inHPItem->data->all();

        $report['data'] = [] ;
        foreach ($inHPItem_data as $row)
        {
            $getInformationByPrice = $this->getInformationByPrice($row->inHospitalItemId);
            $report['data'][] = [
                'id' => $row->id,
                'inHospitalItemId' => $row->inHospitalItemId,
                'makerName' => $row->makerName,
                'category' => $this->category[$row->category]['label'],
                'itemName' => $row->itemName,
                'itemCode' => $row->itemCode,
                'itemStandard' => $row->itemStandard,
                'itemJANCode' => $row->itemJANCode,
                'price' => $getInformationByPrice['price'],
                'orderQuantity' => $getInformationByPrice['orderQuantity'],
                'totalAmount' => $getInformationByPrice['totalAmount'],
                'itemUnit'=> $getInformationByPrice['itemUnit'],
                'distributorName'=> $getInformationByPrice['distributorName']
            ];
            foreach($getInformationByPrice['totalAmount'] as $p)
            {
                $total_amount += (float)$p;
            }
        }
        if(count($report['data']) != 0)
        {
            array_multisort(array_column($report['data'], 'id'), SORT_ASC, $report['data']);
        }
        $report['count'] = $inHPItem->count;
        $report['totalAmount'] = $total_amount;

        return $report;
    }
    
    private function getInformationByPrice(string $inHospitalItemId)
    {
        $disAndPriceArray = [];
        $orderDataByPrice = [
            'price' => [],
            'quantity' => [],
            'receivingCount' => [],
            'returnCount' => [],
            'totalAmount' => [],
            'adjAmount' => [],
            'itemUnit' => [],
            'distributorName' => []
        ];

        foreach ($this->order_data as $order)
        {
            if ($inHospitalItemId == $order->inHospitalItemId)
            {
                $key = array_search($order->distributorId.'_'.$order->price, $disAndPriceArray);
                if ($key === false)
                {
                    $disAndPriceArray[] = $order->distributorId.'_'.$order->price;
                    $key = array_search($order->distributorId.'_'.$order->price, $disAndPriceArray);
                    $orderDataByPrice['price'][$key] = $order->price;
                    $orderDataByPrice['quantity'][$key] = 0;
                    $orderDataByPrice['orderQuantity'][$key] = 0;
                }
                $orderDataByPrice['distributorName'][$key] = $order->distributorName;
                $orderDataByPrice['quantity'][$key] = $order->quantity;
                $orderDataByPrice['itemUnit'][$key] = $order->itemUnit;
                $orderDataByPrice['orderQuantity'][$key] = $orderDataByPrice['orderQuantity'][$key] + $order->orderQuantity;
            }
        }
        
        foreach ($orderDataByPrice['price'] as $key => $byPriceData)
        {
            $orderDataByPrice['totalAmount'][$key] = $byPriceData * $orderDataByPrice['orderQuantity'][$key];
        }
        return $orderDataByPrice;
    }

    private function html($string = '')
    {
        return htmlspecialchars($string, REPLACE_FLAGS, CHARSET);
    }
}