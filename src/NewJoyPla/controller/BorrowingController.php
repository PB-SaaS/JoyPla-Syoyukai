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
use App\Model\AssociationTR;
use App\Model\InHospitalItemView;
use App\Model\DistributorAffiliationView;

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
            $param = array();
    
            $user_info = new UserInfo($SPIRAL);
    
            if( ($user_info->isHospitalUser() && !$user_info->isUser())
            || $user_info->isDistributorUser() ) {
                $divisionData = Division::where('hospitalId',$user_info->getHospitalId())->get();
            } else {
                $divisionData = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }
    
            $api_url = "%url/rel:mpgt:Borrowing%";
            if( $user_info->isDistributorUser())
            {
                $api_url = "%url/rel:mpgt:BorrowingForD%";
            }
    
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            if($user_info->isAdmin() || $user_info->isApprover())
            {
                $borrowingAction = 'borrowingRegistrationToUsedReportApi';
            }
            if($user_info->isUser()) 
            {
                $borrowingAction = 'borrowingRegistrationToUnapprovedUsedSlipApi';
            }
            if($user_info->isDistributorUser())
            {
                $borrowingAction = '';
            }
            
            $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital = $hospital->data->get(0);
            
            $content = $this->view('NewJoyPla/view/BorrowingRegistration', [
                'api_url' => $api_url,
                'label_api_url' => '%url/rel:mpgt:labelBarcodeSAPI%',
                'user_info' => $user_info,
                'divisionData'=> $divisionData,
                'csrf_token' => Csrf::generate(16),
                'borrowingAction' => $borrowingAction,
                'useUnitPrice' => $hospital->billingUnitPrice,
                ] , false);
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla 貸出登録',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
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
            $param = array();
    
            $user_info = new UserInfo($SPIRAL);
    
            $api_url = "%url/rel:mpgt:Borrowing%";
            if( $user_info->isDistributorUser())
            {
                $api_url = "%url/rel:mpgt:BorrowingForD%";
            }
    
            if (($user_info->isHospitalUser() && ( $user_info->isAdmin() || $user_info->isApprover() ) || $user_info->isDistributorUser() ))
            {
                $content = $this->view('NewJoyPla/view/BorrowingList', [
                    'api_url' => $api_url,
                    'user_info' => $user_info,
                    'csrf_token' => Csrf::generate(16)
                ] , false);
                
            } else {
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'table' => '%sf:usr:search56:table%',
                    'title' => '貸出リスト - 部署選択',
                    'param' => 'borrowingListDivision',
                    ] , false);
            }
            
    
            
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
                'title'     => 'JoyPla 貸出リスト',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }

    public function borrowingListDivision()
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ($user_info->isHospitalUser() && ( $user_info->isApprover() || $user_info->isAdmin()) )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ( \App\lib\isMypage() )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
    
            $api_url = "%url/rel:mpgt:Borrowing%";
            
            $content = $this->view('NewJoyPla/view/BorrowingList', [
                'api_url' => $api_url,
                'user_info' => $user_info,
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
                'title'     => 'JoyPla 貸出リスト',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
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
    
            if (($user_info->isHospitalUser() && ( $user_info->isAdmin() || $user_info->isApprover() ) || $user_info->isDistributorUser() ))
            {
                $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '使用申請一覧',
                    'table' => '%sf:usr:search27:mstfilter%',
                    'csrf_token' => Csrf::generate(16),
                    'print' => true
                    ] , false);
                
            } else {
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'table' => '%sf:usr:search56:table%',
                    'title' => '使用申請一覧 - 部署選択',
                    'param' => 'unapprovedUsedSlipDivision',
                    ] , false);
            }
            
                
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
                'title'     => 'JoyPla 使用申請一覧 - 部署選択',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    /**
     * 使用済みリスト（未承認）
     */
    public function unapprovedUsedSlipDivision(): View
    {
        global $SPIRAL;
        try {
    
            $user_info = new UserInfo($SPIRAL);
    
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ($user_info->isHospitalUser() && ( $user_info->isApprover() || $user_info->isAdmin()) )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ( \App\lib\isMypage() )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '使用申請一覧',
                    'table' => '%sf:usr:search27:mstfilter%',
                    'csrf_token' => Csrf::generate(16),
                    'print' => true
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
                'title'     => 'JoyPla 使用申請一覧',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
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
            $param = array();
    
            $user_info = new UserInfo($SPIRAL);
    
            
            if (($user_info->isHospitalUser() && ( $user_info->isAdmin() || $user_info->isApprover() ) || $user_info->isDistributorUser() ))
            {
                $content = $this->view('NewJoyPla/view/template/List', [
                        'title' => '貸出伝票一覧',
                        'table' => '%sf:usr:search28:mstfilter%',
                        'csrf_token' => Csrf::generate(16),
                        'print' => true
                        ] , false);
                
            } else {
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'table' => '%sf:usr:search56:table%',
                    'title' => '貸出伝票一覧 - 部署選択',
                    'param' => 'approvedUsedSlipDivision',
                    ] , false);
            }
        
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
                'title'     => 'JoyPla 貸出伝票一覧',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    
    
    /**
     * 使用済みリスト（承認）
     */
    public function approvedUsedSlipDivision(): View
    {
        global $SPIRAL;
        try {
            // GETで呼ばれた
            //$mytable = new mytable();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            $param = array();
    
            $user_info = new UserInfo($SPIRAL);
    
            if ($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ($user_info->isHospitalUser() && ( $user_info->isApprover() || $user_info->isAdmin()) )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            if ( \App\lib\isMypage() )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => '貸出伝票一覧',
                    'table' => '%sf:usr:search28:mstfilter%',
                    'csrf_token' => Csrf::generate(16),
                    'print' => true
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
                'title'     => 'JoyPla 貸出伝票一覧',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
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
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $result = $this->borrowingRegist();
            
            $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();
            /** TODO
             *  spiralDatabaseのレスポンスをApiResponseに変更 
             **/
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }

    /**
     * 貸出品登録から使用済み報告
     * 管理者のみ
     */
    public function borrowingRegistrationToUsedReportApi()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);

            if(! ( $user_info->isHospitalUser() && !$user_info->isUser() ))
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            $borrowing_regist_result = $this->borrowingRegist();

            $used_slip_create_data = $this->usedSlipHisotoyRegist($borrowing_regist_result->insert_data , 2);

            $all_create_data = $this->usedReportApi($borrowing_regist_result->insert_data);

            $result = $this->association($used_slip_create_data['ids'],$all_create_data['ids']);

            /** メールを作成 */
            foreach($used_slip_create_data['history_data'] as $history)
            {
                $mail_body = $this->view('NewJoyPla/view/Mail/UsingRegistration', [
                    'name' => '%val:usr:name%',
                    'hospital_name' => $history['facility_name'],
                    'hospital_user_name' => $user_info->getName(),
                    'used_date' => $history['usedTime'],
                    'used_slip_number' => $history['usedSlipId'],
                    'used_item_num' => $history['itemsNumber'],
                    'total_price' => "￥".number_format((float)$history['totalAmount'],2),
                    'login_url' => OROSHI_LOGIN_URL,
                ] , false)->render();
                $select_name = $this->makeId($history['distributorId']);
                
                $distributor = DistributorAffiliationView::getNewInstance();
                $test = $distributor->selectName($select_name)->rule(
                    ['name'=>'distributorId','label'=>'name_'.$history['distributorId'],'value1'=>$history['distributorId'],'condition'=>'matches']
                    )->rule(
                        ['name'=>'invitingAgree','label'=>'invitingAgree','value1'=>'t','condition'=>'is_boolean']
                    )->filterCreate();

                $test = $distributor->selectRule($select_name)
                    ->body($mail_body)
                    ->subject("[JoyPla] 貸出品の使用登録がありました")
                    ->from(FROM_ADDRESS,FROM_NAME)
                    ->send();
            }
        
            $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    public function borrowingRegistrationToUnapprovedUsedSlipApi()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);

            if( $user_info->isDistributorUser() )
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if(! ( $user_info->isHospitalUser() && $user_info->isUser() ))
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $borrowing_regist_result = $this->borrowingRegist();

            $used_slip_create_data = $this->usedSlipHisotoyRegist($borrowing_regist_result->insert_data , 1);

            //$all_create_data = $this->usedReportApi($borrowing_regist_result->insert_data);

            //$result = $this->association($used_slip_create_data['ids'],$all_create_data['ids']);
        
            $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();

        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        } 
    }

    private function association(array $used_slip_ids , array $other_ids)
    {
        $insert_data = [];
        foreach($used_slip_ids as $divisionId_distributorId_usedDate => $data)
        {
            $insert_data[] = [
                'usedSlipId' => $used_slip_ids[$divisionId_distributorId_usedDate]['usedSlipId'],
                'orderNumber' => $other_ids[$divisionId_distributorId_usedDate]['orderHistoryId'],
                'receivingHId' => $other_ids[$divisionId_distributorId_usedDate]['receivingHistoryId'],
                'billingNumber' => $other_ids[$divisionId_distributorId_usedDate]['billingHistoryId'],
            ];
        }

        $result = AssociationTR::insert($insert_data);

        return $result;
    }

    private function usedSlipHisotoyRegist(array $borrowing_insert_items , int $used_slip_status = 1)
    {
        global $SPIRAL;
        $user_info = new UserInfo($SPIRAL);
        
        $used_slip_ids = [];
        $all_create_data = [ 'ids' => [] , 'history_data' => []];

        if($this->in_hospital_items == null)
        {
            $in_hospital_items_instance = InHospitalItem::getInstance();
            foreach($borrowing_insert_items as $data)
            {
                $in_hospital_items_instance = $in_hospital_items_instance::orWhere('inHospitalItemId',$data->inHospitalItemId);
            }
            $this->in_hospital_items = $in_hospital_items_instance::get();
        }
        foreach($borrowing_insert_items as $key => $item)
        {
            $price = '';
            $quantity = '';
            $quantityUnit = '';
            $itemUnit = '';
            $distributorId = '';
            foreach($this->in_hospital_items->data->all() as $in_hp_item)
            {
                if($item->inHospitalItemId == $in_hp_item->inHospitalItemId)
                {
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

            if(! isset($used_slip_ids[$ids_key])
            || ! $used_slip_ids[$ids_key])
            {
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
        foreach($borrowing_insert_items as $item)
        {
            $item->usedSlipId = $used_slip_ids[$item->divisionId . $item->distributorId . $item->usedDate]['usedSlipId'];//todo 
            unset($item->registrationTime);
            $update_data[] = (array)$item;
        }

        $used_slip_insert_data = [];

        $facility_name = "";
        if( $user_info->isHospitalUser())
        {
            $hospital_data = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
            $hospital_data = $hospital_data->data->get(0);
            $facility_name = $hospital_data->hospitalName;
        } 
        else if( $user_info->isDistributorUser())
        {
            $distributor_data = Distributor::where('distributorId',$user_info->getDistributorId())->get();
            $distributor_data = $distributor_data->data->get(0);
            $facility_name = $distributor_data->distributorName;
        }

        foreach($used_slip_ids as $divisionId_distributorId_usedDate => $history_data)
        {
            $used_slip_price = [];
            $in_hospital_item_ids = [];
            foreach($update_data as $update_item)
            {
                if($update_item['usedSlipId'] === $history_data['usedSlipId'])
                {
                    $used_slip_price[] = (float)$update_item['price'] * (float)$update_item['borrowingNum'];
                    if(! in_array($update_item['inHospitalItemId'], $in_hospital_item_ids))
                    {
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
        
        $result = Borrowing::bulkUpdate('borrowingId',$update_data);

        $result = UsedSlipHistoy::insert($used_slip_insert_data);
        return $all_create_data;
    }

    private function borrowingRegist()
    {
        global $SPIRAL;
        $user_info = new UserInfo($SPIRAL);
        
        $borrowing_items = $SPIRAL->getParam('borrowing');
        $borrowing_items = $this->requestUrldecode($borrowing_items);
        
        $borrowing_items = array_merge($borrowing_items); // 連番の再採番
        if(( ! is_array($borrowing_items) || ! count($borrowing_items) > 0) )
        {
            throw new Exception(FactoryApiErrorCode::factory(900)->getMessage(),FactoryApiErrorCode::factory(900)->getCode());
        }
        $insert_data = [];
        $divisionId = $SPIRAL->getParam('divisionId');

        $in_hospital_item = InHospitalItemView::where('hospitalId', $user_info->getHospitalId());
        foreach($borrowing_items as $key => $record)
        {
            $in_hospital_item->orWhere('inHospitalItemId',$record['recordId']);
        }
        $in_hospital_item = $in_hospital_item->get();
        foreach($borrowing_items as $key =>  $item)
        {
            foreach($in_hospital_item->data->all() as $in_hp_item)
            {
                $lot_flag = 0;
                if($item['recordId'] == $in_hp_item->inHospitalItemId)
                {
                    $lot_flag = (int)$in_hp_item->lotManagement;
                    break;
                }
            }
            if($lot_flag && $item['lotNumber'] == '' && $item['lotDate'] == '' )
            {
                throw new Exception('invalid lot',100);
            }
            if( ($item['lotNumber'] != '' && $item['lotDate'] == '' ) || ($item['lotNumber'] == '' && $item['lotDate'] != ''))
            {
                throw new Exception('invalid lotNumber',101);
            }
            if (($item['lotNumber'] != '') && ($item['lotDate'] != '')) 
            {
                //if ((!ctype_alnum($item['lotNumber'])) || (strlen($item['lotNumber']) > 20))
                if ((!preg_match('/^[a-zA-Z0-9!-\/:-@¥[-`{-~]+$/', $item['lotNumber'])) || (strlen($item['lotNumber']) > 20))
                {
                    throw new Exception('invalid lotNumber format',102);
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
        foreach($borrowing_items as $key => $record)
        {
            $model = new Borrowing();
            $model->borrowingId = 'B_'.str_pad($result->ids->all()[$key], 10, "0", STR_PAD_LEFT);
            $model->usedSlipId = '';
            $model->inHospitalItemId = $insert_data[$key]['inHospitalItemId'];
            $model->borrowingNum = $insert_data[$key]['borrowingNum'];
            $model->lotNumber = $insert_data[$key]['lotNumber'];
            $model->lotDate = $insert_data[$key]['lotDate'];
            $model->divisionId = $insert_data[$key]['divisionId'];
            
            list($Y, $m, $d) = explode('-', $record['usedDate']);
             
            if (checkdate($m, $d, $Y) === true) {
              $usedDate = $record['usedDate'];
            } else {
              $usedDate = '2999-12-31';
            }
            $model->usedDate = $usedDate;
            $result->insert_data[] = $model;
        }
        return $result ;
    }

    private function usedReportApi(array $borrowing_data)
    {
        global $SPIRAL;
        $all_create_data = [ 'ids' => [] , 'borrowingData' => []];

        $user_info = new UserInfo($SPIRAL);
        
        $hospital = Hospital::where('hospitalId',$user_info->getHospitalId())->get();
        $hospital = $hospital->data->get(0);
        if($this->in_hospital_items == null)
        {
            $in_hospital_items_instance = InHospitalItem::getInstance();
            foreach($borrowing_data as $data)
            {
                $in_hospital_items_instance = $in_hospital_items_instance::orWhere('inHospitalItemId',$data->inHospitalItemId);
            }
            $this->in_hospital_items = $in_hospital_items_instance::get();
        }
        $used_report = [];
        foreach($borrowing_data as $borrowing_key => $data)
        {
            $used_report[$borrowing_key] = new StdClass;
            foreach($this->in_hospital_items->data->all() as $key => $val)
            {
                if($val->inHospitalItemId == $data->inHospitalItemId)
                {
                    $used_report[$borrowing_key]->inHospitalItemId = $val->inHospitalItemId;
                    $used_report[$borrowing_key]->hospitalId = $val->hospitalId;
                    $used_report[$borrowing_key]->distributorId = $val->distributorId;
                    $used_report[$borrowing_key]->catalogNo = $val->catalogNo;
                    $used_report[$borrowing_key]->serialNo = $val->serialNo;
                    $used_report[$borrowing_key]->quantity = $val->quantity;
                    $used_report[$borrowing_key]->quantityUnit = $val->quantityUnit;
                    $used_report[$borrowing_key]->itemUnit = $val->itemUnit;
                    $used_report[$borrowing_key]->notUsedFlag = $val->notUsedFlag;
                    $used_report[$borrowing_key]->itemId = $val->itemId;
                    $used_report[$borrowing_key]->itemName = $val->itemName;
                    $used_report[$borrowing_key]->itemCode = $val->itemCode;
                    $used_report[$borrowing_key]->itemStandard = $val->itemStandard;
                    $used_report[$borrowing_key]->itemJANCode = $val->itemJANCode;
                    $used_report[$borrowing_key]->notice = $val->notice;
                    $used_report[$borrowing_key]->makerName = $val->makerName;
                    $used_report[$borrowing_key]->price = $val->price;
                    $used_report[$borrowing_key]->unitPrice = $val->unitPrice;

                    $used_report[$borrowing_key]->countNum = $data->borrowingNum;
                    $used_report[$borrowing_key]->lotNumber = $data->lotNumber;
                    $used_report[$borrowing_key]->lotDate = $data->lotDate;
                    $used_report[$borrowing_key]->divisionId = $data->divisionId;
                    $used_report[$borrowing_key]->usedDate = $data->usedDate;
                }
            }
        }
        $all_create_data['borrowingData'] = $used_report;

        $history_ids = [];
        foreach($used_report as $item)
        {
            if(! isset($history_ids[$item->divisionId . $item->distributorId . $item->usedDate]) || ! $history_ids[$item->divisionId . $item->distributorId . $item->usedDate])
            {
                $order_id = $this->makeId('03');
                $receiving_id = $this->makeId('04');
                $billing_id = $this->makeId('02');
                $history_ids[$item->divisionId . $item->distributorId . $item->usedDate] = [ 
                    'orderHistoryId' => $order_id,
                    'receivingHistoryId' => $receiving_id,
                    'billingHistoryId' => $billing_id,
                    'divisionId' => $item->divisionId,
                    'distributorId' => $item->distributorId,
                    'usedDate' => $item->usedDate,
                ];
            }
        }
        $all_create_data['ids'] = $history_ids;
        /****** 発注登録 ******/

        $order_insert_data = [];
        foreach($used_report as $item)
        {
            if(!$order_insert_data[$item->inHospitalItemId])
            {
                $order_insert_data[$item->inHospitalItemId] = [
                    'registrationTime' => $item->usedDate,
                    'orderTime' => $item->usedDate,
                    'receivingTime' => $item->usedDate,
                    'orderCNumber' => $this->makeId('BO'),
                    'hospitalId' => $item->hospitalId,
                    'inHospitalItemId' => $item->inHospitalItemId,
                    'orderNumber' => $history_ids[$item->divisionId . $item->distributorId . $item->usedDate]['orderHistoryId'],
                    'price' => $item->price,
                    'orderQuantity' => 0,
                    'orderPrice' => 0,
                    'receivingFlag' => 1,
                    'receivingNum' => 0,
                    'quantity' => $item->quantity,
                    'quantityUnit' => $item->quantityUnit,
                    'itemUnit' => $item->itemUnit,
                    'divisionId' => $item->divisionId,
                    'distributorId' => $item->distributorId,
                ];
            }
            $order_insert_data[$item->inHospitalItemId]['orderQuantity'] = $order_insert_data[$item->inHospitalItemId]['orderQuantity'] + (float)$item->countNum;
            $order_insert_data[$item->inHospitalItemId]['orderPrice'] = $order_insert_data[$item->inHospitalItemId]['orderPrice'] + ( (float)$item->price * (float)$item->countNum );
            $order_insert_data[$item->inHospitalItemId]['receivingNum'] = $order_insert_data[$item->inHospitalItemId]['receivingNum'] + (float)$item->countNum;
        }

        $order_history_insert_data = [];
        foreach($history_ids as $divisionId_distributorId_usedDate => $history_data)
        {
            $order_price = [];
            $in_hospital_item_ids = [];
            foreach($order_insert_data as $insert_record)
            {
                if($insert_record['orderNumber'] === $history_data['orderHistoryId'])
                {
                    $order_price[] = (float)$insert_record['orderPrice'];
                    if(! in_array($insert_record['inHospitalItemId'], $in_hospital_item_ids))
                    {
                        $in_hospital_item_ids[] = $insert_record['inHospitalItemId'];
                    }
                }
            }
            $order_history_insert_data[] = [
                'registrationTime' => $history_data['usedDate'],
                'orderTime' => $history_data['usedDate'],
                'receivingTime' => $history_data['usedDate'],
                'orderNumber' => $history_data['orderHistoryId'],
                'hospitalId' => $user_info->getHospitalId(),
                'divisionId' => $history_data['divisionId'],
                'itemsNumber' => collect($in_hospital_item_ids)->count(),
                'totalAmount' => collect($order_price)->sum(),
                'orderStatus' => 8,
                'distributorId' => $history_data['distributorId'],
                'ordererUserName' => $user_info->getName(),
                'ordercomment' => '貸出品',
            ];
        }
        /****** 発注登録 ******/
        /****** 入荷登録 ******/

        $receiving_insert_data = [];
        foreach($used_report as $item)
        {
            foreach($order_insert_data as $order_data)
            {
                $orderCNumber = '';
                if($item->inHospitalItemId == $order_data['inHospitalItemId'])
                {
                    $orderCNumber = $order_data['orderCNumber'];
                    break; //ループ終了
                }
            }
            $receiving_insert_data[] = [
                'registrationTime' => $item->usedDate,
                'orderCNumber' => $orderCNumber,
                'receivingCount' => (float)$item->countNum,
                'receivingHId' => $history_ids[$item->divisionId . $item->distributorId . $item->usedDate]['receivingHistoryId'],
                'inHospitalItemId' => $item->inHospitalItemId,
                'price' => (float)$item->price,
                'receivingPrice' => (float)$item->price * (float)$item->countNum,
                'hospitalId' => $item->hospitalId,
                'totalReturnCount' => 0,
                'divisionId' => $item->divisionId,
                'distributorId' => $item->distributorId,
                'adjAmount' => 0,
                'priceAfterAdj' => 0,
                'lotNumber' => $item->lotNumber,
                'lotDate' => $item->lotDate,
            ];
        }

        $receiving_history_insert_data = [];
        foreach($history_ids as $divisionId_distributorId_usedDate => $history_data)
        {
            $receiving_price = [];
            $in_hospital_item_ids = [];
            foreach($receiving_insert_data as $insert_record)
            {
                if($insert_record['receivingHId'] === $history_data['receivingHistoryId'])
                {
                    $receiving_price[] = (float)$insert_record['receivingPrice'];
                    if(! in_array($insert_record['inHospitalItemId'], $in_hospital_item_ids))
                    {
                        $in_hospital_item_ids[] = $insert_record['inHospitalItemId'];
                    }
                }
            }
            $receiving_history_insert_data[] = [
                'registrationTime' => $history_data['usedDate'],
                'receivingHId' => $history_data['receivingHistoryId'],
                'distributorId' => $history_data['distributorId'],
                'orderHistoryId' => $history_data['orderHistoryId'],
                'hospitalId' => $user_info->getHospitalId(),
                'itemsNumber' => collect($in_hospital_item_ids)->count(),
                'totalAmount' => collect($receiving_price)->sum(),
                'divisionId' => $history_data['divisionId'],
                'recevingStatus' => 2,
            ];
        }

        /****** 入荷登録 ******/
        /****** 消費登録 ******/
        $billing_insert_data = [];
        foreach($used_report as $item)
        {
            $unitPrice = ($hospital->billingUnitPrice)
                ?(float)$item->unitPrice 
                :(((float)$item->price == 0 || (float)$item->quantity == 0)
                    ? 0 
                    : (float)$item->price / (float)$item->quantity);
            $billing_insert_data[] = [
                'registrationTime' => $item->usedDate,
                'inHospitalItemId' => $item->inHospitalItemId,
                'billingNumber' => $history_ids[$item->divisionId . $item->distributorId . $item->usedDate]['billingHistoryId'],
                'price' => (float)$item->price,
                'billingQuantity' => ((float)$item->quantity * (float)$item->countNum),
                'billingAmount' => $unitPrice * ((float)$item->quantity * (float)$item->countNum),
                'hospitalId' => $item->hospitalId,
                'divisionId' => $item->divisionId,
                'quantity' => $item->quantity,
                'quantityUnit' => $item->quantityUnit,
                'itemUnit' => $item->itemUnit,
                'lotNumber' => $item->lotNumber,
                'lotDate' => $item->lotDate,
                'unitPrice' => $item->unitPrice,
            ];
        }

        $billing_history_insert_data = [];
        foreach($history_ids as $divisionId_distributorId_usedDate => $history_data)
        {
            $total_amount = [];
            $in_hospital_item_ids = [];
            foreach($billing_insert_data as $insert_record)
            {
                if($insert_record['billingNumber'] === $history_data['billingHistoryId'])
                {
                    $total_amount[] = (float)$insert_record['billingAmount'];
                    if(! in_array($insert_record['inHospitalItemId'], $in_hospital_item_ids))
                    {
                        $in_hospital_item_ids[] = $insert_record['inHospitalItemId'];
                    }
                }
            }
            $billing_history_insert_data[] = [
                'registrationTime' => $history_data['usedDate'],
                'billingNumber' => $history_data['billingHistoryId'],
                'hospitalId' => $user_info->getHospitalId(),
                'divisionId' => $history_data['divisionId'],
                'itemsNumber' => collect($in_hospital_item_ids)->count(),
                'totalAmount' => collect($total_amount)->sum(),
                'billingStatus' => 2
            ];
        }

        /****** 貸出更新 ******/
        $result = OrderHistory::insert($order_history_insert_data);
        
        $result = Order::insert($order_insert_data);

        $result = ReceivingHistory::insert($receiving_history_insert_data);
        
        $result = Receiving::insert($receiving_insert_data);

        $result = BillingHistory::insert($billing_history_insert_data);
        
        $result = Billing::insert($billing_insert_data);
        
        return $all_create_data;
    }
    
    public function usedTemporaryReportApi()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);
            $all_create_data = [];

            $used_ids = $SPIRAL->getParam('used_ids');
            $used_date = $SPIRAL->getParam('used_date');

            $model = Borrowing::getInstance();
            foreach($used_ids as $id)
            {
                Borrowing::orWhere('id',$id);
            }
            $result = Borrowing::get();
            
            $borrowing_items = [];
            foreach($result->data->all() as $key => $record)
            {
                $record->usedDate = $used_date;
                $borrowing_items[$key] = $record;
            }

            $used_slip_create_data = $this->usedSlipHisotoyRegist($borrowing_items , 1);
            /** メールを作成 */
            foreach($used_slip_create_data['history_data'] as $history)
            {  
                $mail_body = $this->view('NewJoyPla/view/Mail/UsingRequest', [
                    'name' => '%val:usr:name%',
                    'distributor_name' => $history['facility_name'],
                    'distributor_user_name' => $user_info->getName(),
                    'used_date' => $history['usedTime'],
                    'used_slip_number' => $history['usedSlipId'],
                    'used_item_num' => $history['itemsNumber'],
                    'total_price' => "￥".number_format((float)$history['totalAmount'],2),
                    'login_url' => LOGIN_URL,
                ] , false)->render();
                $select_name = $this->makeId($history['hospitalId']);

                $hospital_user = HospitalUser::getNewInstance();
                $test = $hospital_user::selectName($select_name)->rule(
                    ['name'=>'hospitalId','label'=>'name_'.$history['hospitalId'],'value1'=>$history['hospitalId'],'condition'=>'matches']
                    )->filterCreate();

                $test = $hospital_user::selectRule($select_name)
                    ->body($mail_body)
                    ->subject("[JoyPla] 貸出品の使用申請がありました")
                    ->from(FROM_ADDRESS,FROM_NAME)
                    ->send();
            }

            $result->data = $all_create_data;
            $result->code = 0 ;
            $result->message = 'OK' ;
            $result->count = 0;

            $content = new ApiResponse($result->data , $result->count , $result->code, $result->message, ['insert']);
            $content = $content->toJson();
         
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['insert']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
}

/***
 * 実行
 */
$BorrowingController = new BorrowingController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'borrowingRegistApi')
    {
        //貸出品の登録
        echo $BorrowingController->borrowingRegistApi()->render();
    } 
    else if($action === 'usedTemporaryReportApi')
    {
        //貸出品リストから申請
        echo $BorrowingController->usedTemporaryReportApi()->render();
    }
    else if($action === 'borrowingRegistrationToUsedReportApi')
    {
        //貸出品登録と承認を同時実行
        echo $BorrowingController->borrowingRegistrationToUsedReportApi()->render();
    } 
    else if($action === 'borrowingRegistrationToUnapprovedUsedSlipApi')
    {
        //貸出品登録と未承認を同時実行
        echo $BorrowingController->borrowingRegistrationToUnapprovedUsedSlipApi()->render();
    }
    else if($action === 'borrowingList')
    {
        //貸出品リスト
        echo $BorrowingController->borrowingList()->render();
    }
    else if($action === 'borrowingListDivision')
    {
        //貸出品リスト(部署)
        echo $BorrowingController->borrowingListDivision()->render();
    }
    else if($action === 'unapprovedUsedSlip')
    {
        //承認リスト
        echo $BorrowingController->unapprovedUsedSlip()->render();
    }
    else if($action === 'unapprovedUsedSlipDivision')
    {
        //承認リスト
        echo $BorrowingController->unapprovedUsedSlipDivision()->render();
    }
    else if($action === 'approvedUsedSlip')
    {
        //未承認リスト
        echo $BorrowingController->approvedUsedSlip()->render();
    }
    else if($action === 'approvedUsedSlipDivision')
    {
        //貸出品リスト(部署)
        echo $BorrowingController->approvedUsedSlipDivision()->render();
    }
    else 
    {
        //貸出品登録
        echo $BorrowingController->index()->render();
    }
}
