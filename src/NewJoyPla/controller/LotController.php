<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\Division;
use App\Model\Stock;
use App\Model\InventoryAdjustmentTransaction;
use App\Model\InHospitalItem;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class LotController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if( $user_info->isHospitalUser() && !$user_info->isUser() )
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
            } 
            else 
            {
                $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            }
    
            $api_url = "%url/rel:mpgt:Lots%";
    
            $content = $this->view('NewJoyPla/view/LotContent', [
                'api_url' => $api_url,
                'user_info' => $user_info,
                'division'=> $division,
                'csrf_token' => Csrf::generate(16)
                ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla ロット調整',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function lotRegisterApi(): View
    {
        global $SPIRAL;
        try {
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $division_id = $SPIRAL->getParam('divisionId');
            if($division_id == '')
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $lots = $SPIRAL->getParam('lots');
            $lots = $this->requestUrldecode($lots);
            
            $insert_data = [];
            
            foreach($lots as $lot)
            {
                if($lot['lotNumber'] == "" || $lot['lotDate'] == "")
                {
                    throw new Exception('Invalid LotData',FactoryApiErrorCode::factory(100)->getCode());
                }
                if ((!preg_match('/^[a-zA-Z0-9!-\/:-@¥[-`{-~]+$/', $lot['lotNumber'])) || (strlen($lot['lotNumber']) > 20))
                {
                    throw new Exception('Invalid LotData',FactoryApiErrorCode::factory(100)->getCode());
                }
                $insert_data[] = [
                    'divisionId'=>$division_id,
                    'inHospitalItemId'=>$lot['recordId'],
                    'count'=>0,
                    'hospitalId'=>$user_info->getHospitalId(),
                    'orderWithinCount'=>'',
                    'pattern'=>'11',
                    'lotUniqueKey'=>$user_info->getHospitalId().$division_id.$lot['recordId'].$lot['lotNumber'].$lot['lotDate'],
                    'lotNumber'=>$lot['lotNumber'],
                    'lotDate'=>$lot['lotDate'],
                    'stockQuantity'=>$lot['lotCountNum'],
                    'rackName'=>'',
                    'constantByDiv'=>'',
                    'loginId'=>$user_info->getLoginId(),
                ];
            }
            $result = InventoryAdjustmentTransaction::insert($insert_data);
            
            $content = new ApiResponse($result->ids, $result->count , $result->code, $result->message, ['lotRegisterApi']);
            $content = $content->toJson();
            /** TODO
             *  spiralDatabaseのレスポンスをApiResponseに変更 
             **/
        } catch ( Exception $ex ) {
            $content = new ApiResponse([], 0 , $ex->getCode(), $ex->getMessage(), ['lotRegisterApi']);
            $content = $content->toJson();
        }finally {
            return $this->view('NewJoyPla/view/template/ApiResponse', [
                'content'   => $content,
            ],false);
        }
    }
    
    public function lotManagementList() :View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:Lots%";
            if($user_info->isUser())
            {
                $title = "ロット管理表 - 部署選択";
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'title' => 'ロット管理 - 部署選択',
                    'table' => '%sf:usr:search104:table%',
                    'api_url' => $api_url,
                    'user_info' => $user_info,
                    'param' => 'lotManagementListForDivision',
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
            }
            else
            {
                $title = "ロット管理表";
                $division = Division::where('hospitalId',$user_info->getHospitalId())->get();
                $division = $division->data->all();
                $content = $this->view('NewJoyPla/view/template/List', [
                        'title' => $title,
                        'table' => '%sf:usr:search38:mstfilter%',
                        'csrf_token' => Csrf::generate(16),
                        'print' => true,
                        'export' => true,
                        'script' => $this->view('NewJoyPla/view/Script/LotManagement', [
                            'division' => $division,
                            ] , false)->render()
                        ] , false);
            }
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla '.$title,
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function lotManagementListForDivision()
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            if(! $user_info->isUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            if ( \App\lib\isMypage() )
            {
                throw new Exception(FactoryApiErrorCode::factory(404)->getMessage(),FactoryApiErrorCode::factory(404)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:Lots%";
            $division = Division::where('hospitalId',$user_info->getHospitalId())->where('divisionId',$user_info->getDivisionId())->get();
            $division = $division->data->all();
            $title = "ロット管理";
            
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => $title,
                    'table' => '%sf:usr:search38:mstfilter%',
                    'csrf_token' => Csrf::generate(16),
                    'print' => true,
                    'export' => true,
                    'script' => $this->view('NewJoyPla/view/Script/LotManagement', [
                        'division' => $division,
                        ] , false)->render()
                    ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla '.$title,
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function adjustmentHistory()
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:Lots%";
            if( $user_info->isUser() )
            {
                
                $content = $this->view('NewJoyPla/view/template/DivisionSelectList', [
                    'title' => 'ロット管理 - 部署選択',
                    'table' => '%sf:usr:search104:table%',
                    'api_url' => $api_url,
                    'user_info' => $user_info,
                    'param' => 'adjustmentHistoryForDivision',
                    'csrf_token' => Csrf::generate(16)
                    ] , false); 
            } else 
            {
                
                $content = $this->view('NewJoyPla/view/template/List', [
                        'title' => 'ロット調整ログ',
                        'table' => '%sf:usr:search34:table%',
                        'csrf_token' => Csrf::generate(16)
                        ] , false);
            }
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla ロット調整ログ',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function adjustmentHistoryForDivision()
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            if($user_info->isDistributorUser())
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            }
            
            $api_url = "%url/rel:mpgt:Lots%";
            if( ! $user_info->isUser() )
            {
                throw new Exception(FactoryApiErrorCode::factory(191)->getMessage(),FactoryApiErrorCode::factory(191)->getCode());
            } 
                
            $content = $this->view('NewJoyPla/view/template/List', [
                    'title' => 'ロット調整ログ',
                    'table' => '%sf:usr:search34:table:mstfilter%',
                    'csrf_token' => Csrf::generate(16)
                    ] , false);
            
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
                ] , false);
        } finally {
            
            $head = $this->view('NewJoyPla/view/template/parts/Head', [] , false);
            $header = $this->view('NewJoyPla/src/HeaderForMypage', [
                'SPIRAL' => $SPIRAL
            ], false);
            
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPla/view/template/Template', [
                'title'     => 'JoyPla ロット調整ログ',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
}

$LotController = new LotController();


$action = $SPIRAL->getParam('Action');

{
    if($action == "lotRegister")
    {
        echo $LotController->lotRegisterApi()->render();
    }
    else if($action == "lotManagementList")
    {
        echo $LotController->LotManagementList()->render();
    }
    else if($action == "lotManagementListForDivision")
    {
        echo $LotController->LotManagementListForDivision()->render();
    }
    else if($action == "adjustmentHistory")
    {
        echo $LotController->adjustmentHistory()->render();
    }
    else if($action == "adjustmentHistoryForDivision")
    {
        echo $LotController->adjustmentHistoryForDivision()->render();
    }
    else
    {
        echo $LotController->index()->render();
    }
}