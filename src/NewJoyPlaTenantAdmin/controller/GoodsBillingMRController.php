<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\Auth;
use App\Model\Billing;
use App\Model\Division;
use App\Model\Hospital;
use App\Model\InHospitalItemView;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class GoodsBillingMRController extends Controller
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

        $result = $this->dataSelect($startMonth,$endMonth,$divisionId,$itemName,$itemCode,$itemStandard,$category_ids,$page,$limit);

        $division = Division::where('hospitalId',$SPIRAL->getParam('hospitalId'))->get();
       

        $api_url = '%url/rel:mpgt:MonthlyReport%';

        $content = $this->view('NewJoyPlaTenantAdmin/view/History/GoodsBillingMR', [
            'api_url' => $api_url,
            'division' => $division,
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
    
    private function dataSelect(string $startMonth = null, string $endMonth = null, string $divisionId = null, string $itemName = null, string $itemCode = null, string $itemStandard = null, array $category_ids = array(), int $page = null, int $maxCount = null)
    {
        global $SPIRAL;
        
        $report = [];
        $total_amount = 0;
    
        Billing::where('hospitalId',$SPIRAL->getParam('hospitalId'));
        if ($startMonth) { Billing::where('registrationTime', $startMonth, '>='); }
        if ($endMonth) { Billing::where('registrationTime', (date('Y-m-d', strtotime($endMonth . '+1 day'))), '<='); }
        if ($divisionId) { Billing::where('divisionId', $divisionId); }
        $billingDB = Billing::get();

        if ($billingDB->count == 0)
        {
            return ['data' => [], 'count' => 0, 'totalAmount' => 0];
        }

        $this->billing_data = $billingDB->data->all();

        InHospitalItemView::where('hospitalId',$SPIRAL->getParam('hospitalId'));
        foreach ($this->billing_data as $billing)
        {
            InHospitalItemView::orWhere('inHospitalItemId',$billing->inHospitalItemId);
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
        $report['data'] = [];
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
                'unitPrice' => $getInformationByPrice['unitPrice'],
                'quantity' => $getInformationByPrice['quantity'],
                'quantityUnit' => $getInformationByPrice['quantityUnit'],
                'billingQuantity' => $getInformationByPrice['billingQuantity'],
                'totalAmount' => $getInformationByPrice['totalAmount']
            ];
            foreach($getInformationByPrice['totalAmount'] as $p)
            {
                $total_amount += $p;
            }
        }
        if(count($report['data']) != 0){
            array_multisort(array_column($report['data'], 'id'), SORT_ASC, $report['data']);
        }
        $report['count'] = $inHPItem->count;
        $report['totalAmount'] = $total_amount;

        return $report;
    }
    
    private function getInformationByPrice(string $inHospitalItemId)
    {
        $goodsDataByPrice = [
            'key' => [],
            'price' => [],
            'quantity' => [],
            'billingQuantity' => [],
            'quantityUnit' => [],
            'unitPrice' => []
        ];

        foreach ($this->billing_data as $billingItem)
        {
            if ($inHospitalItemId == $billingItem->inHospitalItemId)
            {
                $search_key = $billingItem->price .'_'.$billingItem->unitPrice .'_'. $billingItem->quantity . '_' . $billingItem->quantityUnit ;
                
                $key = array_search($search_key, $goodsDataByPrice['key']);
                if ($key === false)
                {
                    $goodsDataByPrice['key'][] = $search_key;
                    $key = array_search($search_key, $goodsDataByPrice['key']);
                    $goodsDataByPrice['price'][$key] = $billingItem->price;
                    $goodsDataByPrice['unitPrice'][$key] = $billingItem->unitPrice;
                    $goodsDataByPrice['quantity'][$key] = $billingItem->quantity;
                    $goodsDataByPrice['quantityUnit'][$key] = $billingItem->quantityUnit;
                    $goodsDataByPrice['billingQuantity'][$key] = 0;
                }
                
                $goodsDataByPrice['billingQuantity'][$key] = $goodsDataByPrice['billingQuantity'][$key] + $billingItem->billingQuantity;
                $goodsDataByPrice['totalAmount'][$key] = $goodsDataByPrice['totalAmount'][$key] + (float)$billingItem->billingAmount;
            }
        }
        /*
        if (!$useUnitPrice)
        {
            foreach ($goodsDataByPrice['price'] as $key => $byPriceData)
            {
                $goodsDataByPrice['totalAmount'][$key] = ( $byPriceData / $goodsDataByPrice['quantity'][$key] ) * $goodsDataByPrice['billingQuantity'][$key];
            }
        }
        if ($useUnitPrice)
        {
            foreach ($goodsDataByPrice['price'] as $key => $byPriceData)
            {
                $goodsDataByPrice['totalAmount'][$key] = $goodsDataByPrice['unitPrice'][$key] * $goodsDataByPrice['billingQuantity'][$key];
            }
        }
        */
        return $goodsDataByPrice;
    }

    private function html($string = '')
    {
        return htmlspecialchars($string, REPLACE_FLAGS, CHARSET);
    }
}