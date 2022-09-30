<?php

namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\Division;
use App\Model\UsedSlipHistoy;
use App\Model\Borrowing;
use App\Model\BillingHistory;
use App\Model\Billing;
use App\Model\OrderHistory;
use App\Model\Order;
use App\Model\ReceivingHistory;
use App\Model\Receiving;
use App\Model\InHospitalItem;
use App\Model\Hospital;
use App\Model\HospitalUser;
use App\Model\Distributor;
use App\Model\DistributorUser;
use App\Model\InHospitalItemView;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class BorrowingController extends Controller
{
    private $in_hospital_items = null ;
    public function __construct()
    {
    }

    /**
     * 貸出品登録
     */
    public function index(): View
    {
        global $SPIRAL;
        try {
            // GETで呼ばれた
            //$mytable = new mytable();
            // テンプレートにパラメータを渡し、HTMLを生成し返却

            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isHospitalUser()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $divisionData = Division::where('hospitalId', $user_info->getHospitalId())->get();

            $api_url = '';
            if ($user_info->isDistributorUser()) {
                $api_url = "%url/rel:mpgt:BorrowingForD%";
            }

            $borrowingAction = '';

            $hospital = Hospital::where('hospitalId', $user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);

            $content = $this->view('NewJoyPla/view/BorrowingRegistration', [
                'api_url' => $api_url,
                'isOldTopPage' => true,
                'label_api_url' => '%url/rel:mpgt:labelBSOAPI%',
                'user_info' => $user_info,
                'divisionData'=> $divisionData,
                'csrf_token' => Csrf::generate(16),
                'borrowingAction' => $borrowingAction,
                'useUnitPrice' => $hospital->billingUnitPrice,
                ], false);
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 貸出登録',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    /**
     * 貸出品リスト
     */
    public function borrowingList(): View
    {
        global $SPIRAL;
        try {
            // GETで呼ばれた
            //$mytable = new mytable();
            // テンプレートにパラメータを渡し、HTMLを生成し返却

            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isHospitalUser()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $api_url = '';
            $isOldTopPage = false;
            if ($user_info->isDistributorUser()) {
                $isOldTopPage = true;
                $api_url = "%url/rel:mpgt:BorrowingForD%";
            }

            $content = $this->view('NewJoyPla/view/BorrowingList', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'isOldTopPage' => $isOldTopPage,
                'csrf_token' => Csrf::generate(16)
            ], false);
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 貸出リスト',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    /**
     * 使用済みリスト（未承認）
     */
    public function unapprovedUsedSlip(): View
    {
        global $SPIRAL;
        try {
            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isHospitalUser()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }


            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '未承認使用伝票一覧',
                    'table' => '%sf:usr:search27%',
                    'submenulink' => "%url/rel:mpg:top%&page=page5",
                    'submenu' => '貸出',
                    'csrf_token' => Csrf::generate(16),
                    'print' => true
                    ], false);
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 未承認使用伝票一覧',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    /**
     * 使用済みリスト（承認）
     */
    public function approvedUsedSlip(): View
    {
        global $SPIRAL;
        try {
            // GETで呼ばれた
            //$mytable = new mytable();
            // テンプレートにパラメータを渡し、HTMLを生成し返却

            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isHospitalUser()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '承認済み使用伝票一覧',
                    'table' => '%sf:usr:search28%',
                    'csrf_token' => Csrf::generate(16),
                    'submenulink' => "%url/rel:mpg:top%&page=page5",
                    'submenu' => '貸出',
                    'print' => true
                    ], false);
        } catch (Exception $ex) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ], false);
        } finally {
            $head = $this->view('NewJoyPla/view/template/parts/Head', [], false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);

            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 承認済み使用伝票一覧',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ], false);
        }
    }

    /**
     * 貸出品登録
     */
    public function borrowingRegistApi(): View
    {
        global $SPIRAL;
        $content = '';

        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $result = $this->borrowingRegist();

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            /** TODO
             *  spiralDatabaseのレスポンスをApiResponseに変更
             **/
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content' => $content,
            ], false);
        }
    }

    private function usedSlipHisotoyRegist(array $borrowing_insert_items, int $used_slip_status = 1)
    {
        global $SPIRAL;
        $user_info = new UserInfo($SPIRAL);

        if ($user_info->isHospitalUser()) {
            throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
        }

        $used_slip_ids = [];
        $all_create_data = [ 'ids' => [] , 'history_data' => []];

        if ($this->in_hospital_items == null) {
            $in_hospital_items_instance = InHospitalItem::getInstance();
            foreach ($borrowing_insert_items as $data) {
                $in_hospital_items_instance = $in_hospital_items_instance::orWhere('inHospitalItemId', $data->inHospitalItemId);
            }
            $this->in_hospital_items = $in_hospital_items_instance::get();
        }
        foreach ($borrowing_insert_items as $key => $item) {
            $price = '';
            $quantity = '';
            $quantityUnit = '';
            $itemUnit = '';
            $distributorId = '';
            foreach ($this->in_hospital_items->data as $in_hp_item) {
                if ($item->inHospitalItemId == $in_hp_item->inHospitalItemId) {
                    $price = $in_hp_item->price;
                    $quantity = $in_hp_item->quantity;
                    $quantityUnit = $in_hp_item->quantityUnit;
                    $itemUnit = $in_hp_item->itemUnit;
                    $distributorId = $in_hp_item->distributorId;
                    break;
                }
            }
            $borrowing_insert_items[$key]->price = $price;
            $borrowing_insert_items[$key]->quantity = $quantity;
            $borrowing_insert_items[$key]->quantityUnit = $quantityUnit;
            $borrowing_insert_items[$key]->itemUnit = $itemUnit;
            $borrowing_insert_items[$key]->distributorId = $distributorId;

            $ids_key = $borrowing_insert_items[$key]->divisionId . $distributorId  . $borrowing_insert_items[$key]->usedDate;

            if (! isset($used_slip_ids[$ids_key])
            || ! $used_slip_ids[$ids_key]) {
                $id = $this->makeId('07');
                $used_slip_ids[$ids_key] = [
                    'usedSlipId' =>  $id,
                    'distributorId' => $borrowing_insert_items[$key]->distributorId,
                    'divisionId' => $borrowing_insert_items[$key]->divisionId,
                    'usedDate' => $borrowing_insert_items[$key]->usedDate,
                ];
            }
        }
        $all_create_data['ids'] = $used_slip_ids;

        $update_data = [];
        foreach ($borrowing_insert_items as $item) {
            $item->usedSlipId = $used_slip_ids[$item->divisionId . $item->distributorId . $item->usedDate]['usedSlipId'];//todo
            $update_data[] = (array)$item;
        }

        $used_slip_insert_data = [];

        $facility_name = '';
        $distributor_data = Distributor::where('distributorId', $user_info->getDistributorId())->get();
        $distributor_data = $distributor_data->data->all();
        $facility_name = $distributor_data[0]->distributorName;

        foreach ($used_slip_ids as $divisionId_distributorId_usedDate => $history_data) {
            $used_slip_price = [];
            $in_hospital_item_ids = [];
            foreach ($update_data as $update_item) {
                if ($update_item['usedSlipId'] === $history_data['usedSlipId']) {
                    $used_slip_price[] = (float)$update_item['price'] * (float)$update_item['borrowingNum'];
                    if (! in_array($update_item['inHospitalItemId'], $in_hospital_item_ids)) {
                        $in_hospital_item_ids[] = $update_item['inHospitalItemId'];
                    }
                }
            }
            $used_slip_insert_data[] = [
                'usedTime' => $history_data['usedDate'],
                'usedSlipId' => $history_data['usedSlipId'],
                'itemsNumber' => collect($in_hospital_item_ids)->count(),
                'totalAmount' => collect($used_slip_price)->sum(),
                'distributorId' => $history_data['distributorId'],
                'divisionId' => $history_data['divisionId'],
                'hospitalId' => $user_info->getHospitalId(),
                'usedSlipStatus' => $used_slip_status,
                'reportPersonName' => "[".$facility_name."] ".$user_info->getName(),
                'facility_name' => $facility_name
            ];
        }

        $all_create_data['history_data'] = $used_slip_insert_data;

        $result = Borrowing::bulkUpdate('borrowingId', $update_data);

        $result = UsedSlipHistoy::insert($used_slip_insert_data);
        return $all_create_data;
    }

    private function borrowingRegist()
    {
        global $SPIRAL;
        $user_info = new UserInfo($SPIRAL);

        if ($user_info->isHospitalUser()) {
            throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
        }

        $borrowing_items = $SPIRAL->getParam('borrowing');
        $borrowing_items = array_merge($borrowing_items); // 連番の再採番
        if ((! is_array($borrowing_items) || ! count($borrowing_items) > 0)) {
            throw new Exception(FactoryApiErrorCode::factory(900)->getMessage(), FactoryApiErrorCode::factory(900)->getCode());
        }
        $insert_data = [];
        $divisionId = $SPIRAL->getParam('divisionId');

        $in_hospital_item = InHospitalItemView::where('hospitalId', $user_info->getHospitalId());
        foreach ($borrowing_items as $key => $record) {
            $in_hospital_item->orWhere('inHospitalItemId', $record['recordId']);
        }
        $in_hospital_item = $in_hospital_item->get();
        foreach ($borrowing_items as $key => $item) {
            $exist = false;
            foreach ($in_hospital_item->data->all() as $in_hp_item) {
                $lot_flag = 0;
                if ($item['recordId'] == $in_hp_item->inHospitalItemId) {
                    $exist = true;
                    $lot_flag = (int)$in_hp_item->lotManagement;
                    break;
                }
            }

            if (!$exist) {
                throw new Exception('Borrowing item does not belong to the distributor', 999);
            }
            if ($lot_flag && $item['lotNumber'] == '' && $item['lotDate'] == '') {
                throw new Exception('invalid lot', 100);
            }
            if (($item['lotNumber'] != '' && $item['lotDate'] == '') || ($item['lotNumber'] == '' && $item['lotDate'] != '')) {
                throw new Exception('invalid lotNumber', 101);
            }
            if (($item['lotNumber'] != '') && ($item['lotDate'] != '')) {
                //if ((!ctype_alnum($item['lotNumber'])) || (strlen($item['lotNumber']) > 20))
                if ((!preg_match('/^[a-zA-Z0-9!-\/:-@¥[-`{-~]+$/', $item['lotNumber'])) || (strlen($item['lotNumber']) > 20)) {
                    throw new Exception('invalid lotNumber format', 102);
                }
            }

            $insert_data[$key] = [
                'inHospitalItemId' => $item['recordId'],
                'borrowingNum' => 1,
                'lotNumber' => $item['lotNumber'],
                'lotDate' => $item['lotDate'],
                'divisionId' => $divisionId,
            ];
        }
        $result = Borrowing::insert($insert_data);

        $result->insert_data = [];
        foreach ($borrowing_items as $key => $record) {
            $model = new Borrowing();
            $model->borrowingId = 'B_'.str_pad($result->ids->all()[$key], 10, "0", STR_PAD_LEFT);
            $model->usedSlipId = '';
            $model->inHospitalItemId = $insert_data[$key]['inHospitalItemId'];
            $model->borrowingNum = $insert_data[$key]['borrowingNum'];
            $model->lotNumber = $insert_data[$key]['lotNumber'];
            $model->lotDate = $insert_data[$key]['lotDate'];
            $model->divisionId = $insert_data[$key]['divisionId'];
            $model->usedDate = $record['usedDate'];
            $result->insert_data[] = $model;
        }
        return $result;
    }

    public function usedTemporaryReportApi()
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf'])) ? '' : $_POST['_csrf'];
            Csrf::validate($token, true);

            $user_info = new UserInfo($SPIRAL);

            if ($user_info->isHospitalUser()) {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(), FactoryApiErrorCode::factory(404)->getCode());
            }

            $all_create_data = [];

            $used_ids = $SPIRAL->getParam('used_ids');
            $used_date = $SPIRAL->getParam('used_date');

            $model = Borrowing::getInstance();
            foreach ($used_ids as $id) {
                Borrowing::orWhere('id', $id);
            }
            $result = Borrowing::get();

            $borrowing_items = [];
            foreach ($result->data->all() as $key => $record) {
                $record->usedDate = $used_date;
                $borrowing_items[$key] = $record;
            }

            $used_slip_create_data = $this->usedSlipHisotoyRegist($borrowing_items, 1);
            /** メールを作成 */
            foreach ($used_slip_create_data['history_data'] as $history) {
                $mail_body = $this->view('NewJoyPla/view/Mail/UsingRequest', [
                    'name' => '%val:usr:name%',
                    'distributor_name' => $history['facility_name'],
                    'distributor_user_name' => $user_info->getName(),
                    'used_date' => $history['usedTime'],
                    'used_slip_number' => $history['usedSlipId'],
                    'used_item_num' => $history['itemsNumber'],
                    'total_price' => "￥".number_format_jp((float)$history['totalAmount'], 2),
                    'login_url' => LOGIN_URL,
                ], false)->render();
                $select_name = $this->makeId($history['hospitalId']);

                $hospital_user = HospitalUser::getNewInstance();
                $test = $hospital_user::selectName($select_name)->rule(
                    ['name'=>'hospitalId','label'=>'name_'.$history['hospitalId'],'value1'=>$history['hospitalId'],'condition'=>'matches']
                )->filterCreate();

                $test = $hospital_user::selectRule($select_name)
                    ->body($mail_body)
                    ->subject("[JoyPla] 貸出品の使用申請がありました")
                    ->from(FROM_ADDRESS, FROM_NAME)
                    ->send();
            }

            $result->data = $all_create_data;
            $result->code = 0 ;
            $result->message = 'OK' ;
            $result->count = 0;

            $content = new ApiResponse($result->data, $result->count, $result->code, $result->message, ['insert']);
            $content = $content->toJson();
        } catch (Exception $ex) {
            $content = new ApiResponse([], 0, $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        } finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content' => $content,
            ], false);
        }
    }
}

/***
 * 実行
 */
$BorrowingController = new BorrowingController();

$action = $SPIRAL->getParam('Action');

{
    if ($action === 'borrowingRegistApi') {
        //貸出品の登録
        echo $BorrowingController->borrowingRegistApi()->render();
    } elseif ($action === 'usedTemporaryReportApi') {
        //貸出品リストから申請
        echo $BorrowingController->usedTemporaryReportApi()->render();
    } elseif ($action === 'borrowingList') {
        //貸出品リスト
        echo $BorrowingController->borrowingList()->render();
    } elseif ($action === 'unapprovedUsedSlip') {
        //承認リスト
        echo $BorrowingController->unapprovedUsedSlip()->render();
    } elseif ($action === 'approvedUsedSlip') {
        //未承認リスト
        echo $BorrowingController->approvedUsedSlip()->render();
    } else {
        //貸出品登録
        echo $BorrowingController->index()->render();
    }
}
