<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
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
    
    public $billing_data;

    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

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
            if (!$user_info->isUser())
            {
                if ($SPIRAL->getParam('divisionId')) { $divisionId = $this->html($SPIRAL->getParam('divisionId')); }
            } else {
                $divisionId = $user_info->getDivisionId();
            }
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

            if( ($user_info->isHospitalUser() &&  ( $user_info->isAdmin() || $user_info->isApprover() )))
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            }
            else
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }

            $api_url = '%url/rel:mpgt:GoodsBillingMR%';

            $content = $this->view('NewJoyPla/view/GoodsBillingMR', [
                'api_url' => $api_url,
                'userInfo' => $user_info,
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
                'csrf_token' => Csrf::generate(16)
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 月次レポート【消費】',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    private function dataSelect(string $startMonth = null, string $endMonth = null, string $divisionId = null, string $itemName = null, string $itemCode = null, string $itemStandard = null, array $category_ids = array(), int $page = null, int $maxCount = null)
    {
        global $SPIRAL;
        
        $user_info = new UserInfo($SPIRAL);
        /*
        $useUnitPrice = '';
        $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
        $hospital_data = $hospital_data->data->get(0);
        $useUnitPrice = $hospital_data->billingUnitPrice;
        */
        $report = [];
        $total_amount = 0;
    
        Billing::where('hospitalId',$user_info->getHospitalId());
        if ($startMonth) { Billing::where('registrationTime', $startMonth, '>='); }
        if ($endMonth) { Billing::where('registrationTime', (date('Y-m-d', strtotime($endMonth . '+1 day'))), '<='); }
        if ($divisionId) { Billing::where('divisionId', $divisionId); }
        $billingDB = Billing::get();

        if ($billingDB->count == 0)
        {
            return ['data' => [], 'count' => 0, 'totalAmount' => 0];
        }

        $this->billing_data = $billingDB->data->all();

        InHospitalItemView::where('hospitalId',$user_info->getHospitalId());
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
/***
 * 実行
 */
$GoodsBillingMRController = new GoodsBillingMRController();

$action = $SPIRAL->getParam('Action');

{
    echo $GoodsBillingMRController->index()->render();
}
