<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
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
    
    public $order_data = [];

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
            if ($SPIRAL->getParam('smallCategory')) { $smallCategory = $this->html($SPIRAL->getParam('smallCategory')); }
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

            $result = $this->dataSelect($startMonth,$endMonth,$distributorId,$divisionId,$itemName,$itemCode,$itemStandard,$category_ids,$smallCategory,$page,$limit);

            if( ($user_info->isHospitalUser() && ( $user_info->isAdmin() || $user_info->isApprover() ) ))
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            }
            else
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }

            $distributor_data = Distributor::where('hospitalId',$user_info->getHospitalId())->get();
            $distributor_data = $distributor_data->data->all();

            $api_url = '%url/rel:mpgt:OrderMR%';

            $content = $this->view('NewJoyPla/view/OrderMR', [
                'api_url' => $api_url,
                'userInfo' => $user_info,
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
                'smallCategory' => $smallCategory,
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
                'title'     => 'JoyPla 月次レポート【注文】',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    private function dataSelect(string $startMonth = null, string $endMonth = null, string $distributorId = null, string $divisionId = null, string $itemName = null, string $itemCode = null, string $itemStandard = null, array $category_ids = array(), string $smallCategory = null , int $page = null, int $maxCount = null)
    {
        global $SPIRAL;
        
        $user_info = new UserInfo($SPIRAL);
        
        $report = [];
        $total_amount = 0;
    
        OrderedItemView::where('hospitalId',$user_info->getHospitalId())->where('orderStatus','1','!=');
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

        InHospitalItemView::where('hospitalId',$user_info->getHospitalId());
        foreach ($this->order_data as $order)
        {
            InHospitalItemView::orWhere('inHospitalItemId',$order->inHospitalItemId);
        }
        if ($smallCategory) { InHospitalItemView::where('smallCategory',"%$smallCategory%",'LIKE'); }
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
                'smallCategory' => $row->smallCategory,
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
/***
 * 実行
 */
$OrderMRController = new OrderMRController();

$action = $SPIRAL->getParam('Action');

{
    echo $OrderMRController->index()->render();
}
