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
use App\Model\ReceivingView;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class ReceivingMRController extends Controller
{
    private $category = [
        '1' => ['label' => '医療材料', 'checked' => ''],
        '2' => ['label' => '薬剤', 'checked' => ''],
        '3' => ['label' => '試薬', 'checked' => ''],
        '4' => ['label' => '日用品', 'checked' => ''],
        '99' => ['label' => 'その他', 'checked' => '']
    ];
    
    public $receiving_data = [];

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

            if( ($user_info->isHospitalUser() && ( $user_info->isAdmin() || $user_info->isApprover() )))
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->plain()->get();
            }
            else
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->plain()->get();
            }

            $distributor_data = Distributor::where('hospitalId',$user_info->getHospitalId())->plain()->get();
            $distributor_data = $distributor_data->data->all();

            $api_url = '%url/rel:mpgt:ReceivingMR%';

            $content = $this->view('NewJoyPla/view/ReceivingMR', [
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
                'title'     => 'JoyPla 月次レポート【入荷照合】',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    private function dataSelect(string $startMonth = null, string $endMonth = null, string $distributorId = null, string $divisionId = null, string $itemName = null, string $itemCode = null, string $itemStandard = null, array $category_ids = array(), int $page = null, int $maxCount = null)
    {
        global $SPIRAL;
        
        $user_info = new UserInfo($SPIRAL);
        
        $report = [];
        $total_amount = 0;
    
        ReceivingView::where('hospitalId',$user_info->getHospitalId())->plain();
        if ($startMonth) { ReceivingView::where('registrationTime', $startMonth, '>='); }
        if ($endMonth) { ReceivingView::where('registrationTime', (date('Y-m-d', strtotime($endMonth . '+1 day'))), '<='); }
        if ($divisionId) { ReceivingView::where('divisionId', $divisionId); }
        $receivingDB = ReceivingView::get();

        if ($receivingDB->count == 0)
        {
            return ['data' => [], 'count' => 0, 'totalAmount' => 0];
        }

        $this->receiving_data = $receivingDB->data->all();
        InHospitalItemView::where('hospitalId',$user_info->getHospitalId())->plain();
        foreach ($this->receiving_data as $receiving)
        {
            InHospitalItemView::orWhere('inHospitalItemId',$receiving->inHospitalItemId);
        }
        if ($itemName) { InHospitalItemView::where('itemName',"%$itemName%",'LIKE'); }
        if ($itemCode) { InHospitalItemView::where('itemCode',"%$itemCode%",'LIKE'); }
        if ($itemStandard) { InHospitalItemView::where('itemStandard',"%$itemStandard%",'LIKE'); }
        if ($distributorId) { InHospitalItemView::where('distributorId',$distributorId); }
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
                'distributorName' =>  $getInformationByPrice['distributorName'],
                'quantity' => $getInformationByPrice['quantity'],
                'price' => $getInformationByPrice['price'],
                'receivingCount' => $getInformationByPrice['receivingCount'],
                'totalAmount' => $getInformationByPrice['totalAmount'],
                'itemUnit' => $getInformationByPrice['itemUnit'],
                'totalReturnCount' => $getInformationByPrice['returnCount'],
                'adjAmount' => $getInformationByPrice['adjAmount'],
                'priceAfterAdj' => $getInformationByPrice['priceAfterAdj']
            ];
            foreach($getInformationByPrice['priceAfterAdj'] as $p)
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
        $receivingDataByPrice = [
            'price' => [],
            'quantity' => [],
            'receivingCount' => [],
            'returnCount' => [],
            'totalAmount' => [],
            'adjAmount' => [],
            'itemUnit' => [],
            'distributorName' => []
        ];

        foreach ($this->receiving_data as $receiving)
        {
            if ($inHospitalItemId == $receiving->inHospitalItemId)
            {
                $key = array_search($receiving->distributorId.'_'.$receiving->price, $disAndPriceArray);
                if ($key === false)
                {
                    $disAndPriceArray[] = $receiving->distributorId.'_'.$receiving->price;
                    $key = array_search($receiving->distributorId.'_'.$receiving->price, $disAndPriceArray);
                    $receivingDataByPrice['price'][$key] = $receiving->price;
                    $receivingDataByPrice['quantity'][$key] = 0;
                    $receivingDataByPrice['receivingCount'][$key] = 0;
                    $receivingDataByPrice['adjAmount'][$key] = 0;
                    $receivingDataByPrice['returnCount'][$key] = 0;
                }
                $receivingDataByPrice['distributorName'][$key] = $receiving->distributorName;
                $receivingDataByPrice['quantity'][$key] = $receiving->quantity;
                $receivingDataByPrice['itemUnit'][$key] = $receiving->itemUnit;
                $receivingDataByPrice['receivingCount'][$key] = $receivingDataByPrice['receivingCount'][$key] + $receiving->receivingCount;
                $receivingDataByPrice['adjAmount'][$key] = $receivingDataByPrice['adjAmount'][$key] + $receiving->adjAmount;
                $receivingDataByPrice['returnCount'][$key] = $receivingDataByPrice['returnCount'][$key] + $receiving->totalReturnCount;
            }
        }
        
        foreach ($receivingDataByPrice['price'] as $key => $byPriceData)
        {
            $receivingDataByPrice['totalAmount'][$key] = $byPriceData * $receivingDataByPrice['receivingCount'][$key];
            $receivingDataByPrice['priceAfterAdj'][$key] = $receivingDataByPrice['totalAmount'][$key] + $receivingDataByPrice['adjAmount'][$key];
        }
        return $receivingDataByPrice;
    }

    private function html($string = '')
    {
        return htmlspecialchars($string, REPLACE_FLAGS, CHARSET);
    }
}
/***
 * 実行
 */
$ReceivingMRController = new ReceivingMRController();

$action = $SPIRAL->getParam('Action');

{
    echo $ReceivingMRController->index()->render();
}
