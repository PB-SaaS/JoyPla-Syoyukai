<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Payout;
use App\Model\Division;
use App\Model\Hospital;
use App\Model\InHospitalItemView;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class PayoutMRController extends Controller
{
    private $category = [
        '1' => ['label' => '医療材料', 'checked' => ''],
        '2' => ['label' => '薬剤', 'checked' => ''],
        '3' => ['label' => '試薬', 'checked' => ''],
        '4' => ['label' => '日用品', 'checked' => ''],
        '99' => ['label' => 'その他', 'checked' => '']
    ];
    
    public $payout_data = [];

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

            $result = $this->dataSelect($startMonth,$endMonth,$divisionId,$itemName,$itemCode,$itemStandard,$category_ids,$smallCategory,$page,$limit);

            if( ($user_info->isHospitalUser() &&  ( $user_info->isAdmin() || $user_info->isApprover() )))
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            }
            else
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }

            $api_url = '%url/rel:mpgt:PayoutMR%';

            $content = $this->view('NewJoyPla/view/PayoutMR', [
                'api_url' => $api_url,
                'userInfo' => $user_info,
                'division' => $division,
                'startMonth' => $startMonth,
                'endMonth' => $endMonth,
                'divisionId' => $divisionId,
                'smallCategory' => $smallCategory,
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
                'title'     => 'JoyPla 月次レポート【払出】',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    private function dataSelect(string $startMonth = null, string $endMonth = null, string $divisionId = null, string $itemName = null, string $itemCode = null, string $itemStandard = null, array $category_ids = array(), string $smallCategory = null, int $page = null, int $maxCount = null)
    {
        global $SPIRAL;
        
        $user_info = new UserInfo($SPIRAL);
        /*
        $useUnitPrice = '';
        $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
        $hospital_data = $hospital_data->data->get(0);
        $useUnitPrice = $hospital_data->payoutUnitPrice;
        */
        $report = [];
        $total_amount = 0;
    
        Payout::where('hospitalId',$user_info->getHospitalId());
        if ($startMonth) { Payout::where('registrationTime', $startMonth, '>='); }
        if ($endMonth) { Payout::where('registrationTime', (date('Y-m-d', strtotime($endMonth . '+1 day'))), '<='); }
        if ($divisionId) { Payout::where('targetDivisionId', $divisionId); }
        $payoutDB = Payout::get();

        if ($payoutDB->count == 0)
        {
            return ['data' => [], 'count' => 0, 'totalAmount' => 0];
        }

        $this->payout_data = $payoutDB->data->all();

        $instance = InHospitalItemView::getNewInstance()->where('hospitalId',$user_info->getHospitalId());
        foreach ($this->payout_data as $payout)
        {
            $instance::orWhere('inHospitalItemId',$payout->inHospitalItemId);
        }
        if ($itemName) { $instance::where('itemName',"%$itemName%",'LIKE'); }
        if ($itemCode) { $instance::where('itemCode',"%$itemCode%",'LIKE'); }
        if ($itemStandard) { $instance::where('itemStandard',"%$itemStandard%",'LIKE'); }
        if ($smallCategory) { $instance::where('smallCategory',"%$smallCategory%",'LIKE'); }
        if ($category_ids)
        {
            foreach ($category_ids as $val)
            {
                $instance::orWhere('category',$val);
            }
        }
        if ($page) { $instance::page($page); }
        $inHPItem = $instance::paginate($maxCount);
        $inHPItem_data = $inHPItem->data->all();

        $report['data'] = [];
        foreach ($inHPItem_data as $row)
        {
            $getInformationByPrice = $this->getInformationByPrice($row->inHospitalItemId);
            $report['data'][] = [
                'id' => $row->id,
                'inHospitalItemId' => $row->inHospitalItemId,
                'makerName' => $row->makerName,
                'smallCategory' => $row->smallCategory,
                'category' => $this->category[$row->category]['label'],
                'itemName' => $row->itemName,
                'itemCode' => $row->itemCode,
                'itemStandard' => $row->itemStandard,
                'itemJANCode' => $row->itemJANCode,
                'price' => $getInformationByPrice['price'],
                'unitPrice' => $getInformationByPrice['unitPrice'],
                'quantity' => $getInformationByPrice['quantity'],
                'payoutQuantity' => $getInformationByPrice['payoutQuantity'],
                'totalAmount' => $getInformationByPrice['totalAmount'],
                'adjAmount' => $getInformationByPrice['adjAmount'],
                'priceAfterAdj' => $getInformationByPrice['priceAfterAdj'],
                'quantityUnit'=> $getInformationByPrice['quantityUnit']
            ];
            
            foreach($getInformationByPrice['priceAfterAdj'] as $p)
            {
                $total_amount += (float)$p;
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
        $payoutDataByPrice = [
            'key' => [],
            'price' => [],
            'quantity' => [],
            'payoutQuantity' => [],
            'totalAmount' => [],
            'adjAmount' => [],
            'quantityUnit' => [],
            'unitPrice' => []
        ];

        foreach ($this->payout_data as $payoutItem)
        {
            if ($inHospitalItemId == $payoutItem->inHospitalItemId)
            {
                $search_key = $payoutItem->price .'_'.$payoutItem->unitPrice .'_'. $payoutItem->quantity . '_' . $payoutItem->quantityUnit ;
                
                $key = array_search($search_key, $payoutDataByPrice['key']);
                
                if ($key === false)
                {
                    $payoutDataByPrice['key'][] = $search_key;
                    $key = array_search($search_key, $payoutDataByPrice['key']);
                    $payoutDataByPrice['price'][$key] = $payoutItem->price;
                    $payoutDataByPrice['unitPrice'][$key] = $payoutItem->unitPrice;
                    $payoutDataByPrice['quantity'][$key] = $payoutItem->quantity;
                    $payoutDataByPrice['quantityUnit'][$key] = $payoutItem->quantityUnit;
                    $payoutDataByPrice['payoutQuantity'][$key] = 0;
                    $payoutDataByPrice['adjAmount'][$key] = 0;
                    $payoutDataByPrice['priceAfterAdj'][$key] = 0;
                    $payoutDataByPrice['totalAmount'][$key] = 0;
                }
                $payoutDataByPrice['totalAmount'][$key] = $payoutDataByPrice['totalAmount'][$key] + $payoutItem->payoutAmount;
                $payoutDataByPrice['payoutQuantity'][$key] = $payoutDataByPrice['payoutQuantity'][$key] + $payoutItem->payoutQuantity;
                $payoutDataByPrice['adjAmount'][$key] = $payoutDataByPrice['adjAmount'][$key] + $payoutItem->adjAmount;
                $payoutDataByPrice['priceAfterAdj'][$key] = $payoutDataByPrice['priceAfterAdj'][$key] + $payoutItem->priceAfterAdj;
            }
        }
        /*
        if (!$useUnitPrice)
        {
            foreach ($payoutDataByPrice['price'] as $key => $byPriceData)
            {
                $payoutDataByPrice['totalAmount'][$key] = ( $byPriceData / $payoutDataByPrice['quantity'][$key] ) * $payoutDataByPrice['payoutQuantity'][$key];
                $payoutDataByPrice['priceAfterAdj'][$key] = $payoutDataByPrice['totalAmount'][$key] + $payoutDataByPrice['adjAmount'][$key];
            }
        }
        if ($useUnitPrice)
        {
            foreach ($payoutDataByPrice['price'] as $key => $byPriceData)
            {
                $payoutDataByPrice['totalAmount'][$key] = $payoutDataByPrice['unitPrice'][$key] * $payoutDataByPrice['payoutQuantity'][$key];
                $payoutDataByPrice['priceAfterAdj'][$key] = $payoutDataByPrice['totalAmount'][$key] + $payoutDataByPrice['adjAmount'][$key];
            }
        }
        */
        return $payoutDataByPrice;
    }

    private function html($string = '')
    {
        return htmlspecialchars($string, REPLACE_FLAGS, CHARSET);
    }
}

/***
 * 実行
 */

$action = $SPIRAL->getParam('Action');

{
    $PayoutMRController = new PayoutMRController();
    echo $PayoutMRController->index()->render();
}
