<?php

namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\Division;
use App\Model\Hospital;
use App\Model\InHospitalItemView;
use App\Model\PayoutHistory;
use App\Model\Payout;
use App\Model\PayoutView;
use App\Model\PickingHistory;
use App\Model\PayScheduleItems;
use App\Model\PayScheduleItemsView;
use App\Model\Card;
use App\Model\Stock;
use App\Model\Distributor;
use App\Model\InventoryAdjustmentTransaction;
use App\Model\StockView;

use ApiErrorCode\FactoryApiErrorCode;
use App\Model\HospitalUser;
use stdClass;
use validate\FieldSet;
use Exception;
use DateTime;
use Error;

class PayoutController extends Controller
{
    public function __construct()
    {
    }
    /*
    public function index(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = array();
        try {

            $user_info = new UserInfo($SPIRAL);
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            $target_division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            if( ($user_info->isHospitalUser() && $user_info->getUserPermission() == '1')) 
            {
                $source_division = $target_division;
            } 
            else 
            {
                $source_division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }
    
            $api_url = "%url/rel:mpgt:Payout%";
    
            
            $content = $this->view('NewJoyPla/view/PayoutContent', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'source_division'=> $source_division,
                'target_division'=> $target_division,
                'csrf_token' => Csrf::generate(16)
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                'csrf_token' => Csrf::generate(16)
                ] , false);
        } finally {
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 払出登録',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    */

    public function payoutScheduled(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = array();
        try {

            $user_info = new UserInfo($SPIRAL);

            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            /* 
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $target_division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            if( ($user_info->isHospitalUser() && !$user_info->isUser())) 
            {
                $source_division = $target_division;
            } 
            else 
            {
                $source_division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }
    
            $api_url = "%url/rel:mpgt:Payout%";
    
            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $useUnitPrice = (int)$hospital_data->payoutUnitPrice;
            
            $content = $this->view('NewJoyPla/view/PayoutScheduled', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'source_division'=> $source_division,
                'target_division'=> $target_division,
                'useUnitPrice'=> $useUnitPrice,
                'csrf_token' => Csrf::generate(16)
                ] , false);
 */
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
                'csrf_token' => Csrf::generate(16)
            ], false);
        } finally {
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 払出予定商品登録',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }



    public function newPayout(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        $param = array();
        try {

            $user_info = new UserInfo($SPIRAL);

            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            if ($user_info->isDistributorUser()) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }
            $target_division = Division::where('hospitalId', $user_info->getHospitalId())->get();
            if (($user_info->isHospitalUser() && !$user_info->isUser())) {
                $source_division = $target_division;
            } else {
                $source_division = Division::where('hospitalId', $user_info->getHospitalId())->where('divisionId', $user_info->getDivisionId())->get();
            }

            $api_url = "%url/rel:mpgt:Payout%";

            $hospital_data = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $useUnitPrice = (int)$hospital_data->payoutUnitPrice;

            $content = $this->view('NewJoyPla/view/PayoutContent2', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'source_division' => $source_division,
                'target_division' => $target_division,
                'useUnitPrice' => $useUnitPrice,
                'csrf_token' => Csrf::generate(16)
            ], false);
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
                'csrf_token' => Csrf::generate(16)
            ], false);
        } finally {
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 払出登録',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }


    public function payoutList(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser()) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            if ($user_info->isHospitalUser() && ($user_info->isAdmin() || $user_info->isApprover())) {
                $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '払出履歴一覧',
                    'table' => '%sf:usr:payoutList:mstfilter%',
                    'csrf_token' => Csrf::generate(16),
                    'submenulink' => "%url/rel:mpg:top%&path=payout",
                    'submenu' => '払出メニュー',
                ], false);
            } else {
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'table' => '%sf:usr:search105:table%',
                    'title' => '払出履歴一覧 - 部署選択',
                    'param' => 'payoutListForDivision',
                ], false);
            }
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 払出履歴一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    public function payoutListForDivision(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            if ($user_info->isHospitalUser() && ($user_info->isApprover() || $user_info->isAdmin())) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            if (\App\lib\isMypage()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $api_url = "%url/rel:mpgt:Payout%";

            $content = $this->view('NewJoyPla/view/template/List', [
                'title' => '払出履歴一覧',
                'table' => '%sf:usr:payoutList:mstfilter%',
                'csrf_token' => Csrf::generate(16),
                'submenulink' => "%url/rel:mpg:top%&path=payout",
                'submenu' => '払出メニュー',
            ], false);
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却

            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 払出履歴一覧',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    public function payoutLabel(): View
    {
        global $SPIRAL;
        // GETで呼ばれた
        //$mytable = new mytable();
        // テンプレートにパラメータを渡し、HTMLを生成し返却
        try {

            $user_info = new UserInfo($SPIRAL);


            if ($user_info->isDistributorUser()) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            $payout_history_id = $SPIRAL->getParam('payoutHistoryId');

            if ($payout_history_id == "" || $payout_history_id == null) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            if ($user_info->isAdmin() || $user_info->isApprover()) {
                $payout_items = PayoutView::where('payoutHistoryId', $payout_history_id)->where('hospitalId', $user_info->getHospitalId())->get();
                $payout_items = $payout_items->data->all();
            } else {
                $payout_items = PayoutView::where('payoutHistoryId', $payout_history_id)->where('hospitalId', $user_info->getHospitalId())->where('sourceDivisionId', $user_info->getDivisionId())->get();
                $payout_items = $payout_items->data->all();
            }

            Stock::where('hospitalId', $user_info->getHospitalId());

            foreach ($payout_items as $item) {
                Stock::orWhere('inHospitalItemId', $item->inHospitalItemId);
            }

            $stock_items        = Stock::get();

            foreach ($payout_items as $key => $item) {
                foreach ($stock_items->data->all() as $stock_item) {
                    if ($stock_item->inHospitalItemId == $item->inHospitalItemId && $stock_item->divisionId == $item->sourceDivisionId) {
                        $payout_items[$key]->sourceRackName = $stock_item->rackName;
                        $payout_items[$key]->constantByDiv = $stock_item->constantByDiv;
                    }
                    if ($stock_item->inHospitalItemId == $item->inHospitalItemId && $stock_item->divisionId == $item->targetDivisionId) {
                        $payout_items[$key]->targetRackName = $stock_item->rackName;
                        $payout_items[$key]->constantByDiv = $stock_item->constantByDiv;
                    }
                }
                Distributor::orWhere('distributorId', $item->distributorId);
                Division::orWhere('divisionId', $item->targetDivisionId);
                Division::orWhere('divisionId', $item->sourceDivisionId);
            }

            $distributor = Distributor::get();
            $division = Division::get();
            foreach ($payout_items as $key => $item) {

                foreach ($division->data->all() as $division_data) {
                    if ($item->sourceDivisionId == $division_data->divisionId) {
                        $payout_items[$key]->sourceDivision = $division_data->divisionName;
                    }
                    if ($item->targetDivisionId == $division_data->divisionId) {
                        $payout_items[$key]->targetDivision = $division_data->divisionName;
                    }
                }

                foreach ($distributor->data->all() as $distributor_data) {
                    if ($item->distributorId == $distributor_data->distributorId) {
                        $payout_items[$key]->distributorName = $distributor_data->distributorName;
                    }
                }
            }

            $default_design = $this->defaultDesign();

            $hospital_data = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);

            if ($hospital_data->labelDesign2 != '') {
                $default_design = htmlspecialchars_decode($hospital_data->labelDesign2);
            }
            $content = $this->view('NewJoyPla/view/PayoutLabel', [
                //'api_url' => $api_url,
                'user_info' => $user_info,
                'payout_items' => $payout_items,
                'default_design' => $default_design
            ], false);
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            ], false);
        } finally {

            $head   = $this->view('NewJoyPla/view/template/parts/Head', [
                'new' => true
            ], false);

            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            $style   = $this->view('NewJoyPla/view/template/parts/LabelsCss', [], false)->render();

            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [], false)->render();

            $script   = $this->view('NewJoyPla/view/template/parts/Script', [], false)->render();

            // テンプレートにパラメータを渡し、HTMLを生成し返却

            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 払出ラベル',
                'script' => $script,
                'style' => $style,
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    private function defaultDesign()
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

    public function payoutRegistApi()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isDistributorUser()) {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(), FactoryApiErrorCode::factory(191)->getCode());
            }

            $payout = $SPIRAL->getParam('payout');
            $payout = $this->requestUrldecode($payout);
            $payout_date = $SPIRAL->getParam('payoutDate');
            if ($payout_date == '') {
                $payout_date = 'now';
            }

            $in_hospital_item = InHospitalItemView::where('hospitalId', $user_info->getHospitalId());
            foreach ($payout as $key => $record) {
                $in_hospital_item->orWhere('inHospitalItemId', $record['recordId']);
            }
            $in_hospital_item = $in_hospital_item->get();

            foreach ($payout as $key => $record) {
                foreach ($in_hospital_item->data->all() as $in_hp_item) {
                    $lot_flag = 0;
                    if ($record['recordId'] == $in_hp_item->inHospitalItemId) {
                        $lot_flag = (int)$in_hp_item->lotManagement;
                        break;
                    }
                }
                if ($lot_flag && ($record['lotNumber'] == '' || $record['lotDate'] == '')) {
                    throw new Exception('invalid lot', 100);
                }
                if (($record['lotNumber'] != '' && $record['lotDate'] == '') || ($record['lotNumber'] == '' && $record['lotDate'] != '')) {
                    throw new Exception('invalid lotNumber input', 101);
                }
                if (($record['lotNumber'] != '') && ($record['lotDate'] != '')) {
                    //if ((!ctype_alnum($item['lotNumber'])) || (strlen($item['lotNumber']) > 20))
                    if ((!preg_match('/^[a-zA-Z0-9!-\/:-@¥[-`{-~]+$/', $record['lotNumber'])) || (strlen($record['lotNumber']) > 20)) {
                        throw new Exception('invalid lotNumber format', 102);
                    }
                }
                if ((int)$payout['countNum'] < 0 || (int)$record['countLabelNum'] < 0) {
                    throw new Exception('invalid payout count', 200);
                }
                $payout[$key]['countNum'] = (int)$record['countNum'] * (int)$record['countLabelNum'];
                $payout[$key]['payoutCount'] = $record['countNum'];
            }

            $hospital_data = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);

            $use_unit_price = $hospital_data->payoutUnitPrice;

            $source_division_id = $SPIRAL->getParam('sourceDivisionId');
            $source_division_name =  $SPIRAL->getParam('sourceDivisionName');
            $target_division_id = $SPIRAL->getParam('targetDivisionId');
            $target_division_name = $SPIRAL->getParam('targetDivisionName');

            $payout = \App\Lib\requestUrldecode($payout);
            $source_division_name = urldecode($source_division_name);
            $target_division_name = urldecode($target_division_name);

            $payout_id = $this->makeId('05');

            /* インサートデータ作成 */
            $insert_data = [];
            $total_amount = 0;
            $in_hospital_item_ids = [];
            $card_ids = [];
            $label_create_flg = false;
            foreach ($payout as $data) {
                if ((int)$data['countNum']  > 0) {
                    if (array_search($data['recordId'], $in_hospital_item_ids) === false) {
                        $in_hospital_item_ids[] = $data['recordId'];
                    }
                    $unit_price = $use_unit_price
                        ? (float)(str_replace(',', '', $data['unitPrice']))
                        : (((float)str_replace(',', '', $data['kakaku']) == 0 || (float)$data['irisu'] == 0)
                            ? 0
                            : (float)(str_replace(',', '', $data['kakaku']) / (float)$data['irisu']));
                    $insert_data[] = [
                        'registrationTime' => $payout_date,
                        'payoutHistoryId' => $payout_id,
                        'inHospitalItemId' => $data['recordId'],
                        'hospitalId' => $user_info->getHospitalId(),
                        'sourceDivisionId' => $source_division_id,
                        'targetDivisionId' => $target_division_id,
                        'quantity' => $data['irisu'],
                        'quantityUnit' => $data['unit'],
                        'itemUnit' => $data['itemUnit'],
                        'price' => str_replace(',', '', $data['kakaku']),
                        'payoutQuantity' => (int)$data['countNum'],
                        'payoutAmount' => (float)$unit_price * (int)$data['countNum'],
                        'payoutCount' => $data['payoutCount'],
                        'payoutLabelCount' => $data['countLabelNum'],
                        'lotNumber' => $data['lotNumber'],
                        'lotDate' => $data['lotDate'],
                        'unitPrice' => $unit_price,
                        'cardId' => $data['cardNum'],
                        'payoutType' => 1
                    ];
                    if ($data['cardNum'] != "") {
                        $card_ids[] = $data['cardNum'];
                    }

                    if ($data['cardNum'] == "") {
                        $label_create_flg = true; //一つでもあれば発行する
                    }
                    $total_amount = $total_amount + ((float)$unit_price * (int)$data['countNum']);
                }
            }

            $insert_history_data = [
                [
                    'registrationTime' => $payout_date,
                    'payoutHistoryId' => $payout_id,
                    'hospitalId' => $user_info->getHospitalId(),
                    'sourceDivisionId' => $source_division_id,
                    'sourceDivision' => $source_division_name,
                    'targetDivisionId' => $target_division_id,
                    'targetDivision' => $target_division_name,
                    'itemsNumber' => count($in_hospital_item_ids),
                    'totalAmount' => $total_amount,
                ]
            ];

            $inventory_adjustment_trdata = [];


            foreach ($insert_data as $record) {
                if ($record['lotNumber'] && $record['lotDate']) {
                    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record['targetDivisionId'],
                        'inHospitalItemId' => $record['inHospitalItemId'],
                        'count' => $record['payoutQuantity'],
                        'pattern' => 5,
                        'hospitalId' => $user_info->getHospitalId(),
                        'lotUniqueKey' => $user_info->getHospitalId() . $record['targetDivisionId'] . $record['inHospitalItemId'] . $record['lotNumber'] . $record['lotDate'],
                        'stockQuantity' => $record['payoutQuantity'],
                        'lotNumber' =>  $record['lotNumber'],
                        'lotDate' =>    $record['lotDate'],
                    ];
                    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record['sourceDivisionId'],
                        'inHospitalItemId' => $record['inHospitalItemId'],
                        'count' => -$record['payoutQuantity'],
                        'pattern' => 4,
                        'hospitalId' => $user_info->getHospitalId(),
                        'lotUniqueKey' => $user_info->getHospitalId() . $record['sourceDivisionId'] . $record['inHospitalItemId'] . $record['lotNumber'] . $record['lotDate'],
                        'stockQuantity' => -$record['payoutQuantity'],
                        'lotNumber' =>  $record['lotNumber'],
                        'lotDate' =>    $record['lotDate'],
                    ];
                } else {
                    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record['targetDivisionId'],
                        'inHospitalItemId' => $record['inHospitalItemId'],
                        'pattern' => 5,
                        'count' => $record['payoutQuantity'],
                        'hospitalId' => $user_info->getHospitalId(),
                    ];
                    $inventory_adjustment_trdata[] = [
                        'divisionId' => $record['sourceDivisionId'],
                        'inHospitalItemId' => $record['inHospitalItemId'],
                        'pattern' => 4,
                        'count' => -$record['payoutQuantity'],
                        'hospitalId' => $user_info->getHospitalId(),
                    ];
                }
            }

            if (count($insert_data) === 0) {
                throw new Exception('not payout data', 200);
            }

            $result = PayoutHistory::insert($insert_history_data);
            $result = Payout::insert($insert_data);

            $payout = new Payout();
            $payout->where('hospitalId', $user_info->getHospitalId())->where('payoutHistoryId', $payout_id);
            foreach ($card_ids as $id) {
                $payout->orWhere('cardId', $id);
            }

            $payout_data = $payout->get();
            $card_update = [];
            foreach ($payout_data->data->all() as $payout_item) {
                if ($payout_item->cardId != '') {
                    $card_update[] = [
                        'cardId' => $payout_item->cardId,
                        'payoutId' => $payout_item->payoutId,
                    ];
                }
            }
            if (count($card_update) > 0) {
                Card::bulkUpdate('cardId', $card_update);
            }
            $result = InventoryAdjustmentTransaction::insert($inventory_adjustment_trdata);

            $content = new ApiResponse(['payoutHistoryId' => $payout_id, 'labelCreateFlg' => $label_create_flg], $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['payoutRegistApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }


    public function regPayoutScheduledApi()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            $items = $SPIRAL->getParam('items');
            $items = $this->requestUrldecode($items);

            $insert = [];

            $division = Division::getNewInstance()->plain()->value('divisionId')->value('divisionName')->where('hospitalId', $user_info->getHospitalId());

            foreach ($items as $item) {
                $division->orWhere('divisionId', $item['source_division'])->orWhere('divisionId', $item['target_division']);
            }

            $division = ($division->get())->data->all();

            $divisionCheck = function ($divisions, $divisionId) {
                foreach ($divisions as $division) {
                    if ($division->divisionId === $divisionId) {
                        return true;
                    }
                }
                return false;
            };
            $psis = [];

            foreach ($items as $item) {
                if (!$divisionCheck($division, $item['source_division'])) {
                    throw new Exception('source_division is not exist');
                }
                if (!$divisionCheck($division, $item['target_division'])) {
                    throw new Exception('target_division is not exist');
                }
                $psi = $this->makeId('psi');
                $psis[] = $psi;

                $insert[] = [
                    "payoutPlanTime" => $item['payout_schedule'],
                    "payoutPlanId" => $psi, //payout schedule item
                    "pickingId" => '',
                    "inHospitalItemId" => $item['recordId'],
                    "itemId" => $item['itemId'],
                    "hospitalId" => $user_info->getHospitalId(),
                    "cardId" => $item['cardNum'],
                    "sourceDivisionId" => $item['source_division'],
                    "targetDivisionId" => $item['target_division'],
                    "payoutQuantity" => $item['payoutCount'],
                ];
            }

            $result = PayScheduleItems::insert($insert);
            $instance = PayScheduleItemsView::getNewInstance();
            foreach ($psis as $psi) {
                $instance->orwhere('payoutPlanId', $psi);
            }

            $instance = $instance->get();

            $instance = $instance->data->all();

            $items = [];
            foreach ($instance as $i) {
                if (!array_key_exists($i->sourceDivisionId, $items)) {
                    $items[$i->sourceDivisionId] = [];
                }
                $items[$i->sourceDivisionId][] = [
                    'targetDivisionName' => $i->targetDivision,
                    'payoutPlanTime' => $i->payoutPlanTime,
                    'makerName' => $i->makerName,
                    'itemName' => $i->itemName,
                    'itemCode' => $i->itemCode,
                    'itemStandard' => $i->itemStandard,
                    'itemJANCode' => $i->itemJANCode,
                    'payoutQuantity' => $i->payoutQuantity,
                    'quantityUnit' => $i->quantityUnit,
                    'cardId' => $i->cardId,
                ];
            }

            foreach ($items as $divisionId => $item) {
                $hospital_user = HospitalUser::getNewInstance();
                $select_name = $this->makeId($user_info->getHospitalId());
                $test = $hospital_user::selectName($select_name)
                    ->rule(['name' => 'hospitalId', 'label' => 'name_' . $user_info->getHospitalId(), 'value1' => $user_info->getHospitalId(), 'condition' => 'matches'])
                    ->rule(['name' => 'userPermission', 'label' => 'permission_2', 'value1' => '2', 'condition' => 'contains'])
                    ->rule(['name' => 'divisionId', 'label' => 'permission_division', 'value1' => $divisionId, 'condition' => 'matches'])
                    ->filterCreate();

                $mail_body = $this->view('NewJoyPla/view/Mail/RegPayoutScheduled', [
                    'name' => '%val:usr:name%',
                    'items' => $item,
                    'login_url' => LOGIN_URL,
                ], false)->render();

                $test = $hospital_user::selectRule($select_name)
                    ->body($mail_body)
                    ->subject("[JoyPla] 払出予定商品が追加されました")
                    ->from(FROM_ADDRESS, FROM_NAME)
                    ->send();
            }

            $hospital_user = HospitalUser::getNewInstance();
            $select_name = $this->makeId($user_info->getHospitalId());
            $test = $hospital_user::selectName($select_name)
                ->rule(['name' => 'hospitalId', 'label' => 'name_' . $user_info->getHospitalId(), 'value1' => $user_info->getHospitalId(), 'condition' => 'matches'])
                ->rule(['name' => 'userPermission', 'label' => 'permission_admin', 'value1' => '1,3', 'condition' => 'contains'])
                ->filterCreate();

            $mail_body = $this->view('NewJoyPla/view/Mail/RegPayoutScheduled', [
                'name' => '%val:usr:name%',
                'items' => array_reduce($items, 'array_merge', []),
                'login_url' => LOGIN_URL,
            ], false)->render();

            $test = $hospital_user::selectRule($select_name)
                ->body($mail_body)
                ->subject("[JoyPla] 払出予定商品が追加されました")
                ->from(FROM_ADDRESS, FROM_NAME)
                ->send();


            $content = new ApiResponse($result->ids, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['payoutRegistApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }

    public function payoutScheduledItemList()
    {
        global $SPIRAL;
        try {

            throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            /* 
            //フルスクラッチでやってみる
            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            // * キャッシュから取得
            $table_cache = ($SPIRAL->getParam('table_cache') === 'true');
            
            $search = new StdClass;
            // * デフォルト
            $search->sort_title = 'id';
            $search->limit = 10;
            $search->page = 1;
            $search->sort_asc = 'asc';
            $search->registration_time_start = '';
            $search->registration_time_end = '';
            $search->payout_plan_time_start = '';
            $search->payout_plan_time_end = '';
            $search->source_division = '';
            $search->target_division = '';
            $search->category = [];
            $search->out_of_stock_status = [];

            if($table_cache && $cache->exists('joypla_payoutScheduledItemList')){
                $search = $cache->get('joypla_payoutScheduledItemList');
            }

            $search->sort_title = ( $SPIRAL->getParam('sortTitle'))? $SPIRAL->getParam('sortTitle') : $search->sort_title; // 初期ソート id asc
            $search->sort_asc = ( $SPIRAL->getParam('sort') === 'asc' || $SPIRAL->getParam('sort') === 'desc' )? $SPIRAL->getParam('sort') : $search->sort_asc;
            // * 取得条件
            $search->limit = ( ! is_null($SPIRAL->getParam('limit')) && ( $SPIRAL->getParam('limit') >= 1 && $SPIRAL->getParam('limit') <= 1000 ) )? $SPIRAL->getParam('limit') : $search->limit ;
            $search->page = ( ! is_null($SPIRAL->getParam('page')) && ( $SPIRAL->getParam('page') >= 1 ) )?  $SPIRAL->getParam('page')  : $search->page;


            // * 値の取得
            // * 払出予定商品
            $pay_schedule_items = PayScheduleItemsView::where('pickingId','','ISNULL')
                                                        ->where('hospitalId',$user_info->getHospitalId())
                                                        ->orWhere('outOfStockStatus','2','!=')
                                                        ->sort($search->sort_title,$search->sort_asc)
                                                        ->page($search->page);

            // * 検索
            $search->registration_time_start =  ($SPIRAL->getParam('registration_time_start'))? $SPIRAL->getParam('registration_time_start') : $search->registration_time_start;
            $search->registration_time_start = ( 
                FieldSet::validate(\field\DateYearMonthDay::FIELD_NAME , $search->registration_time_start)->isSuccess()
            )? $search->registration_time_start : '';
            
            $search->registration_time_end =  ($SPIRAL->getParam('registration_time_end'))? $SPIRAL->getParam('registration_time_start') : $search->registration_time_end;
            $search->registration_time_end = (
                FieldSet::validate(\field\DateYearMonthDay::FIELD_NAME, $search->registration_time_end)->isSuccess()
            )? $search->registration_time_end : '';


            $search->payout_plan_time_start =  ($SPIRAL->getParam('payout_plan_time_start'))? $SPIRAL->getParam('payout_plan_time_start') : $search->payout_plan_time_start;
            $search->payout_plan_time_start = ( 
                FieldSet::validate(\field\DateYearMonthDay::FIELD_NAME , $search->payout_plan_time_start)->isSuccess()
            )? $search->payout_plan_time_start : '';
            
            $search->payout_plan_time_end =  ($SPIRAL->getParam('payout_plan_time_end'))? $SPIRAL->getParam('payout_plan_time_end') : $search->payout_plan_time_end;
            $search->payout_plan_time_end = (
                FieldSet::validate(\field\DateYearMonthDay::FIELD_NAME, $search->payout_plan_time_end)->isSuccess()
            )? $search->payout_plan_time_end : '';

            if( $user_info->isUser())
            {
                $search->source_division = $user_info->getDivisionId();
            }
            else
            {
                $search->source_division = ( $SPIRAL->getParam('source_division') )? $SPIRAL->getParam('source_division') : $search->source_division;
                $search->source_division = (
                    FieldSet::validate(\field\NumberSymbolAlphabet32Bytes::FIELD_NAME, $search->source_division)->isSuccess()
                )? $search->source_division : '';
            }
            

            $search->target_division = ( $SPIRAL->getParam('target_division') )? $SPIRAL->getParam('target_division') : $search->target_division;
            $search->target_division = (
                FieldSet::validate(\field\NumberSymbolAlphabet32Bytes::FIELD_NAME, $search->target_division)->isSuccess()
            )? $search->target_division : '';
            

            $search->category = ( $SPIRAL->getParams('category') )? $SPIRAL->getParams('category') : $search->category;
            foreach($search->category as &$c)
            {
                $c = (
                    FieldSet::validate(\field\Select::FIELD_NAME, $c)->isSuccess()
                )? $c : '';
            }

            $search->out_of_stock_status = ( $SPIRAL->getParams('out_of_stock_status') )? $SPIRAL->getParams('out_of_stock_status') : $search->out_of_stock_status;

            foreach($search->out_of_stock_status as &$s)
            {
                $s = (
                    FieldSet::validate(\field\Select::FIELD_NAME, $s)->isSuccess()
                )? $s : '';
            }
            

            
            if ($search->registration_time_start !== '') { 
                $pay_schedule_items->where('registrationTime', $search->registration_time_start, '>='); 
            }

            if ($search->registration_time_end !== '') { 
                $pay_schedule_items->where('registrationTime', (date('Y-m-d', strtotime($search->registration_time_end . '+1 day'))), '<');
            }

            if ($search->payout_plan_time_start !== '') { 
                $pay_schedule_items->where('payoutPlanTime', $search->payout_plan_time_start, '>='); 
            }

            if ($search->payout_plan_time_end !== '') { 
                $pay_schedule_items->where('payoutPlanTime', (date('Y-m-d', strtotime($search->payout_plan_time_end . '+1 day'))), '<');
            }

            if ($search->source_division !== '') { 
                $pay_schedule_items->where('sourceDivisionId', $search->source_division); 
            }

            if ($search->target_division !== '') { 
                $pay_schedule_items->where('targetDivisionId', $search->target_division); 
            }

            if ($search->category !== '') {
                foreach($search->category as $c)
                {
                    $pay_schedule_items->orWhere('category', $c ); 
                }
            }

            if ($search->out_of_stock_status !== '') {
                foreach($search->out_of_stock_status as $c)
                {
                    if($c == 2){ continue; } //払出可能は検索できなくする
                    $pay_schedule_items->orWhere('outOfStockStatus', $c ); 
                }
            }

            $pay_schedule_items = $pay_schedule_items->paginate($search->limit);

            $count = $pay_schedule_items->count;
            $pay_schedule_items_label = $pay_schedule_items->label->all();
            $pay_schedule_items = $pay_schedule_items->data->all();
            // * 部署情報
            $division_info = [];
            $division_info = Division::where('hospitalId',$user_info->getHospitalId())->whereDeleted()->get();
            $division_info = $division_info->data->all();
            // * 実体参照を行い、必要な情報をセット
            $source_division_info = [];
            $target_division_info = $division_info;

            if($user_info->isUser())
            {
                $source_division_info[] = \App\Lib\array_obj_find($division_info,'divisionId',$user_info->getDivisionId());
            }
            else
            {
                $source_division_info = $division_info;
            }
            foreach($pay_schedule_items as &$item)
            {
                
                $payout_plan_time = \App\lib\changeDateFormat('Y年m月d日 H時i分s秒',$item->payoutPlanTime,'Y-m-d');
                $payout_plan_time = new DateTime($payout_plan_time);
                $today = new DateTime(date('Y-m-d'));
                $week_plus_2 = new DateTime(date('Y-m-d'));
                $week_plus_2 = $week_plus_2->modify('+2 week');
                $item->payoutPlanTimeStatus = 0;
                //あと2週間を切っている
                if( $today < $payout_plan_time && $week_plus_2 >= $payout_plan_time)
                {
                    $item->payoutPlanTimeStatus = 1;
                }
                //当日を過ぎている
                else if( $today >= $payout_plan_time )
                {
                    $item->payoutPlanTimeStatus = 2;
                }

                $item->checked = false;
                $item->outOfStockStatus_id = $item->outOfStockStatus;
                $item->outOfStockStatus = $pay_schedule_items_label['outOfStockStatus']->all()[$item->outOfStockStatus];
                $item->category = $pay_schedule_items_label['category']->all()[$item->category];

                $sourceDivision = \App\Lib\array_obj_find($division_info,'divisionId',$item->sourceDivisionId);
                $targetDivision = \App\Lib\array_obj_find($division_info,'divisionId',$item->targetDivisionId);
                $item->sourceDivision = $sourceDivision->divisionName;
                $item->targetDivision = $targetDivision->divisionName;
            }

            $form_url = "%url/rel:mpgt:Payout%";
            $api_url = "%url/rel:mpgt:Payout%";
            
            $content = $this->view('NewJoyPla/view/PayoutScheduledItemList', [
                    'title' => '払出予定商品一覧',
                    'search_action' => 'payoutScheduledItemList',
                    'form_url' => $form_url,
                    'api_url' => $api_url,
                    'count' => $count,
                    'search' => $search,
                    'source_division_info' => $source_division_info,
                    'target_division_info' => $target_division_info,
                    'payout_schedule_items' => $pay_schedule_items,
                    'csrf_token' => Csrf::generate(16),
                    'category_label' => $pay_schedule_items_label['category']->all(),
                    'out_of_stock_status_label' => $pay_schedule_items_label['outOfStockStatus']->all(),
                    ] , true);
 */
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [
                'new' => true
            ], false);
            $style   = $this->view('NewJoyPla/view/template/parts/FormPrintCss', [], false)->render();

            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [], false)->render();

            $script   = $this->view('NewJoyPla/view/template/parts/Script', [], false)->render();

            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 払出予定商品一覧',
                'script' => $script,
                'style' => $style,
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    public function pickingItemsRegistApi()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            $item_ids = $SPIRAL->getParam('ids');
            $item_ids = $this->requestUrldecode($item_ids);

            if (is_null($item_ids) || count($item_ids) === 0) {
                throw new Exception('not payout data', 200);
            }

            $payschedule_items_view = PayScheduleItemsView::where('hospitalId', $user_info->getHospitalId());

            foreach ($item_ids as $id) {
                $payschedule_items_view->orWhere('id', $id);
            }

            $payschedule_items_view = $payschedule_items_view->get();
            $payschedule_items_view = $payschedule_items_view->data->all();
            $source_division_ids = [];
            foreach ($payschedule_items_view as $p) {
                $source_division_ids[] = $p->sourceDivisionId;
            }

            $source_division_ids = array_unique($source_division_ids);

            $picking_item_update = [];
            $picking_insert = [];
            foreach ($source_division_ids as $division_id) {
                $picking_id = $this->makeId('12'); // picking
                foreach ($payschedule_items_view as $p) {
                    if ($division_id !== $p->sourceDivisionId) {
                        continue;
                    }
                    $picking_item_update[] = [
                        'payoutPlanId' => $p->payoutPlanId,
                        'pickingId' => $picking_id,
                    ];
                }

                $picking_insert[] = [
                    'pickingId' => $picking_id,
                    'hospitalId' => $user_info->getHospitalId(),
                    'divisionId' => $division_id,
                ];
            }

            $result = PickingHistory::insert($picking_insert);
            $result = PayScheduleItems::bulkUpdate('payoutPlanId', $picking_item_update);

            $content = new ApiResponse($result->ids, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['payoutRegistApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }


    public function pickingItemsDeleteApi()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            $item_ids = $SPIRAL->getParam('ids');
            $item_ids = $this->requestUrldecode($item_ids);

            if (is_null($item_ids) || count($item_ids) === 0) {
                throw new Exception('not delete data', 200);
            }

            $payschedule_items = PayScheduleItems::where('hospitalId', $user_info->getHospitalId());

            foreach ($item_ids as $id) {
                $payschedule_items->orWhere('id', $id);
            }

            $payschedule_items = $payschedule_items->get();

            if ($payschedule_items->count != count($item_ids)) {
                throw new Exception('datas error', 200);
            }

            $payschedule_items = PayScheduleItems::where('hospitalId', $user_info->getHospitalId());

            foreach ($item_ids as $id) {
                $payschedule_items->orWhere('id', $id);
            }

            $result = $payschedule_items->delete();

            $content = new ApiResponse($result->ids, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['payoutRegistApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }


    public function pickingList()
    {
        global $SPIRAL;
        try {

            throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            /* 
            //フルスクラッチでやってみる
            $user_info = new UserInfo($SPIRAL);
            $cache = $SPIRAL->getCache();
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            // * キャッシュから取得
            $table_cache = ($SPIRAL->getParam('table_cache') === 'true');
            
            $search = new StdClass;
            // * デフォルト
            $search->sort_title = 'id';
            $search->sort_asc = 'desc';
            $search->limit = 10;
            $search->page = 1;
            $search->registration_time_start = '';
            $search->registration_time_end = '';
            $search->division_id = '';
            $search->picking_status = [];

            if($table_cache && $cache->exists('joypla_pickingList')){
                $search = $cache->get('joypla_pickingList');
                $search = json_decode($search);
            }

            $search->sort_title = ( $SPIRAL->getParam('sortTitle'))? $SPIRAL->getParam('sortTitle') : $search->sort_title; // 初期ソート id asc
            $search->sort_asc = ( $SPIRAL->getParam('sort') === 'asc' || $SPIRAL->getParam('sort') === 'desc' )? $SPIRAL->getParam('sort') : $search->sort_asc;
            // * 取得条件
            $search->limit = ( ! is_null($SPIRAL->getParam('limit')) && ( $SPIRAL->getParam('limit') >= 1 && $SPIRAL->getParam('limit') <= 1000 ) )? $SPIRAL->getParam('limit') : $search->limit ;
            $search->page = ( ! is_null($SPIRAL->getParam('page')) && ( $SPIRAL->getParam('page') >= 1 ) )?  $SPIRAL->getParam('page')  : $search->page;

            // * 値の取得
            // * ピッキングリストの取得
            $picking_history = PickingHistory::where('hospitalId',$user_info->getHospitalId())
                                                ->sort($search->sort_title,$search->sort_asc)
                                                ->page((int)$search->page);

            // * 検索
            $search->registration_time_start =  ($SPIRAL->getParam('registration_time_start'))? $SPIRAL->getParam('registration_time_start') : $search->registration_time_start;
            $search->registration_time_start = ( 
                FieldSet::validate(\field\DateYearMonthDay::FIELD_NAME , $search->registration_time_start)->isSuccess()
            )? $search->registration_time_start : '';
            
            $search->registration_time_end =  ($SPIRAL->getParam('registration_time_end'))? $SPIRAL->getParam('registration_time_end') : $search->registration_time_end;
            $search->registration_time_end = (
                FieldSet::validate(\field\DateYearMonthDay::FIELD_NAME, $search->registration_time_end)->isSuccess()
            )? $search->registration_time_end : '';

            if($user_info->isUser())
            {
                $search->division_id = $user_info->getDivisionId();
            } 
            else 
            {
                $search->division_id = ( $SPIRAL->getParam('division_id') )? $SPIRAL->getParam('division_id') : $search->division_id;
                $search->division_id = (
                    FieldSet::validate(\field\NumberSymbolAlphabet32Bytes::FIELD_NAME, $search->division_id)->isSuccess()
                )? $search->division_id : '';
            }


            $search->picking_status = ( $SPIRAL->getParams('picking_status') )? $SPIRAL->getParams('picking_status') : $search->picking_status;
            foreach($search->picking_status as &$s)
            {
                $s = (
                    FieldSet::validate(\field\Select::FIELD_NAME, $s)->isSuccess()
                )? $s : '';
            }
            

            
            if ($search->registration_time_start !== '') { 
                $picking_history->where('registrationTime', $search->registration_time_start, '>='); 
            }

            if ($search->registration_time_end !== '') { 
                $picking_history->where('registrationTime', (date('Y-m-d', strtotime($search->registration_time_end . '+1 day'))), '<');
            }

            if ($search->division_id !== '') { 
                $picking_history->where('divisionId', $search->division_id); 
            }

            if ($search->picking_status !== '') {
                foreach($search->picking_status as $c)
                {
                    $picking_history->orWhere('pickingStatus', $c ); 
                }
            }

            $picking_history = $picking_history->paginate((int)$search->limit);
            $cache->set('joypla_pickingList', json_encode($search));

            $count = $picking_history->count;
            $picking_history_label = $picking_history->label->all();
            $picking_history = $picking_history->data->all();
            
            // * 部署情報
            $division_info = [];
            $division_info = Division::where('hospitalId',$user_info->getHospitalId())->whereDeleted();
            if($user_info->isUser())
            {
                $division_info->where('divisionId',$user_info->getDivisionId());
            }
            $division_info = $division_info->get();
            $division_info = $division_info->data->all();
            // * 実体参照を行い、必要な情報をセット
            foreach($picking_history as &$item)
            {
                $item->checked = false;
                $item->pickingStatus_id = $item->pickingStatus;
                $item->pickingStatus = $picking_history_label['pickingStatus']->all()[$item->pickingStatus];

                $division = \App\Lib\array_obj_find($division_info,'divisionId',$item->divisionId);
                $item->divisionName = $division->divisionName;
            }

            $form_url = "%url/rel:mpgt:Payout%";
            $api_url = "%url/rel:mpgt:Payout%";

            $content = $this->view('NewJoyPla/view/PickingList', [
                    'title' => 'ピッキングリスト',
                    'search_action' => 'pickingList',
                    'slip_link_action' => 'pickingListSlip',
                    'form_url' => $form_url,
                    'api_url' => $api_url,
                    'search' => $search,
                    'count' => $count,
                    'division_info' => $division_info,
                    'picking_history' => $picking_history,
                    'picking_status_label' => $picking_history_label['pickingStatus']->all(),
                    'csrf_token' => Csrf::generate(16)
                    ] , true);
 */
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [
                'new' => true
            ], false);
            $style   = $this->view('NewJoyPla/view/template/parts/FormPrintCss', [], false)->render();

            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [], false)->render();

            $script   = $this->view('NewJoyPla/view/template/parts/Script', [], false)->render();

            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla ピッキングリスト',
                'script' => $script,
                'style' => $style,
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }


    public function pickingListSlip()
    {
        global $SPIRAL;
        try {

            throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            /* 
            //フルスクラッチでやってみる
            $user_info = new UserInfo($SPIRAL);
            $cache = $SPIRAL->getCache();
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $id = $SPIRAL->getParam('id');
            $picking_history = PickingHistory::where('hospitalId',$user_info->getHospitalId())->find($id)->get();
            
            if($picking_history->count ==  0)
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $picking_history = $picking_history->data->get(0);
            $picking_id = $picking_history->pickingId;
            $picking_status = $picking_history->pickingStatus;
            $pay_schedule_items = PayScheduleItems::where('pickingId',$picking_history->pickingId)->where('hospitalId',$user_info->getHospitalId())->get();
            
            if($pay_schedule_items->count == 0 && $picking_history->pickingStatus == '1')
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }

            $stock = [];
            $in_item_ids = [];
            $count = $pay_schedule_items->count;
            $stocks_count = 0;
            $pay_schedule_items = $pay_schedule_items->data->all();
            if($count > 0){
                $stock = StockView::where('hospitalId',$user_info->getHospitalId())->where('divisionId', $picking_history->divisionId);
                foreach($pay_schedule_items as $item)
                {
                    $stock->orWhere('inHospitalItemId', $item->inHospitalItemId);
                }
                
                $stock = $stock->get();
                $stocks_count = $stock->count;
                $stock = $stock->data->all();
            }

            foreach($pay_schedule_items as $item)
            {
                if(array_search($item->inHospitalItemId, $in_item_ids) === false) {
                    $in_item_ids[] = $item->inHospitalItemId;
                }
            }
            
            foreach($stock as $s)
            {
                if(($key = array_search($s->inHospitalItemId, $in_item_ids)) !== false) {
                    unset($in_item_ids[$key]);
                }
            }

            $in_hospital_item_tmp = [];
            if(count($in_item_ids) > 0)
            {
                $in_hospital_items = InHospitalItemView::where('hospitalId',$user_info->getHospitalId());
                foreach($in_item_ids as $id)
                {
                    $in_hospital_items->orWhere('inHospitalItemId',$id);
                }
                $in_hospital_items = $in_hospital_items->get();
                $in_hospital_items = $in_hospital_items->data->all();

                //StockView と同じ形に成形
                foreach($in_hospital_items as $t)
                {
                    $tmp = new stdClass;
                    foreach(StockView::$fillable as $f )
                    {
                        if( ! isset($t->{$f}) )
                        {
                            $tmp->{$f} = '';
                            if($f === 'stockQuantity')
                            {
                                $tmp->{$f} = 0;
                            }
                        }
                        else if( isset($t->{$f}) )
                        {
                            $tmp->{$f} = $t->{$f};
                        }
                    }
                    $in_hospital_item_tmp[] = $tmp;
                }
            }
            $stock_view_model = array_merge($stock , $in_hospital_item_tmp);

            // * 部署情報
            $division_info = [];
            $division_info = Division::where('hospitalId',$user_info->getHospitalId())->whereDeleted()->get();
            $division_info = $division_info->data->all();
            $division = \App\Lib\array_obj_find($division_info,'divisionId',$picking_history->divisionId);
            $division_name = $division->divisionName;

            // * 実体参照を行い、必要な情報をセット
            foreach($pay_schedule_items as &$item)
            {
                $division = \App\Lib\array_obj_find($division_info,'divisionId',$item->sourceDivisionId);
                $item->sourceDivision = $division->divisionName;
                $division = \App\Lib\array_obj_find($division_info,'divisionId',$item->targetDivisionId);
                $item->targetDivision = $division->divisionName;
            }

            $form_url = "%url/rel:mpgt:Payout%";
            $api_url = "%url/rel:mpgt:Payout%";

            $content = $this->view('NewJoyPla/view/PickingListSlip', [
                    'title' => 'ピッキングリスト伝票',
                    'form_url' => $form_url,
                    'picking_id' => $picking_id,
                    'api_url' => $api_url,
                    'picking_list_url' => '%url/rel:mpgt:Payout%',
                    'stock' => $stock_view_model,
                    'picking_status' => $picking_status,
                    'division_name' => $division_name,
                    'registrationTime' => $picking_history->registrationTime,
                    'picking_history' => $picking_history,
                    'pay_schedule_items' => $pay_schedule_items,
                    'csrf_token' => Csrf::generate(16)
                    ] , true);
 */
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
            ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [
                'new' => true
            ], false);
            $style   = $this->view('NewJoyPla/view/template/parts/FormPrintCss', [], false)->render();

            $style   .= $this->view('NewJoyPla/view/template/parts/StyleCss', [], false)->render();

            $script   = $this->view('NewJoyPla/view/template/parts/Script', [], false)->render();

            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla ピッキングリスト伝票',
                'script' => $script,
                'style' => $style,
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    public function pickingSlipDelete()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            $pickingId = $SPIRAL->getParam('picking_id');

            $picking_history = PickingHistory::where('hospitalId', $user_info->getHospitalId())->where('pickingId', $pickingId)->where('pickingStatus', '1')->get();

            if ($picking_history->count == 0) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $picking_history = $picking_history->data->get(0);

            $result = PayScheduleItems::where('hospitalId', $user_info->getHospitalId())->where('pickingId', $pickingId)->update(['pickingId' => '']);
            PickingHistory::where('hospitalId', $user_info->getHospitalId())->where('pickingId', $pickingId)->where('pickingStatus', '1')->delete();
            $content = new ApiResponse($result->ids, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['payoutRegistApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }

    public function pickingSlipCommit()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            $picking_id = $SPIRAL->getParam('picking_id');
            $picking_history = PickingHistory::where('hospitalId', $user_info->getHospitalId())->where('pickingId', $picking_id)->where('pickingStatus', '1')->get();

            if ($picking_history->count == 0) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $picking_history = $picking_history->data->get(0);

            $post_items = $SPIRAL->getParam('pay_schedule_items');

            if (!is_array($post_items)) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $pay_schedule_items = PayScheduleItems::where('pickingId', $picking_id)->where('hospitalId', $user_info->getHospitalId());

            foreach ($post_items as $item) {
                $pay_schedule_items->orWhere('payoutPlanId', $item['payoutPlanId']);
            }
            $pay_schedule_items = $pay_schedule_items->get();

            if (count($post_items) != $pay_schedule_items->count) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $update = [];
            foreach ($post_items as $item) {
                if ($item['outOfStockStatus'] == '2') {
                    $update[] = [
                        'payoutPlanId' => $item['payoutPlanId'],
                        'pickingId' => $item['pickingId'],
                        'outOfStockStatus' => $item['outOfStockStatus'],
                    ];
                } else {
                    $update[] = [
                        'payoutPlanId' => $item['payoutPlanId'],
                        'pickingId' => '',
                        'outOfStockStatus' => $item['outOfStockStatus'],
                    ];
                }
            }

            $result = PayScheduleItems::bulkUpdate('payoutPlanId', $update);
            $result = PickingHistory::where('hospitalId', $user_info->getHospitalId())->where('pickingId', $picking_id)->update(['pickingStatus' => '2']);

            $content = new ApiResponse($result->ids, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['payoutRegistApi']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ], false);
        }
    }
}



/***
 * 実行
 */
$PayoutController = new PayoutController();

$action = $SPIRAL->getParam('Action'); {
    if ($action === 'payoutRegistApi') {
        echo $PayoutController->payoutRegistApi()->render();
    } else if ($action === 'newPayout') {
        echo $PayoutController->newPayout()->render();
    } else if ($action === 'payoutLabel') {
        echo $PayoutController->payoutLabel()->render();
    } else if ($action === 'payoutList') {
        echo $PayoutController->payoutList()->render();
    } else if ($action === 'payoutListForDivision') {
        echo $PayoutController->payoutListForDivision()->render();
    } else if ($action === 'payoutScheduled') {
        echo $PayoutController->payoutScheduled()->render();
    } else if ($action === 'regPayoutScheduledApi') {
        echo $PayoutController->regPayoutScheduledApi()->render();
    } else if ($action === 'payoutScheduledItemList') {
        echo $PayoutController->payoutScheduledItemList()->render();
    } else if ($action === 'pickingItemsDeleteApi') {
        echo $PayoutController->pickingItemsDeleteApi()->render();
    } else if ($action === 'pickingItemsRegistApi') {
        echo $PayoutController->pickingItemsRegistApi()->render();
    } else if ($action === 'pickingList') {
        echo $PayoutController->pickingList()->render();
    } else if ($action === 'pickingListSlip') {
        echo $PayoutController->pickingListSlip()->render();
    } else if ($action === 'pickingSlipDelete') {
        echo $PayoutController->pickingSlipDelete()->render();
    } else if ($action === 'pickingSlipCommit') {
        echo $PayoutController->pickingSlipCommit()->render();
    } else {
        echo $PayoutController->newPayout()->render();
    }
}
