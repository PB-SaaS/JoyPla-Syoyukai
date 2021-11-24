<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\CommentTr;
use App\Model\Topic;
use App\Model\Comment;
use App\Model\DistributorAffiliationView;
use App\Model\HospitalUser;
use App\Model\TenantMaster;

use ApiErrorCode\FactoryApiErrorCode;

use stdClass;
use Exception;

class TopicSlipController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(): View
    {
        global $SPIRAL;
        try {

            $user_info = new UserInfo($SPIRAL);
            
            $link = "%url/rel:mpgt:Topic%";
            $api_url = "%url/card:page_266279%";
            
            if($user_info->isDistributorUser())
            {
                $link = "%url/rel:mpgt:TopicD%";
            }
            
            $content = $this->view('NewJoyPla/view/TopicSlip', [
                'title' => 'トピック',
                'link' => $link,
                'return_items' => $return_items,
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
                'title'     => 'JoyPla トピック',
                'script' => '',
                'content'   => $content->render(),
                'head' => $head->render(),
                'header' => $header->render(),
                'baseUrl' => '',
            ],false);
        }
    }
    
    public function comment()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            
            $user_info = new UserInfo($SPIRAL);
            
            $comment_data = $SPIRAL->getParam('commentData');
            $comment_data = \App\Lib\requestUrldecode($comment_data);
            
            $record_id = (int)$SPIRAL->getCardId();
            $topic = Topic::find($record_id)->get();
            
            $topic = $topic->data->get(0);
            
            $comment_insert = [];
            if($user_info->isDistributorUser())
            {
                $comment_insert[] = [
                        'topicId' => $topic->topicId,
                        'comment' =>$comment_data['comment'],
                        'name' => $comment_data['name'],
                        'authKey' => $topic->authKey,
                        'O_Id' => $user_info->getLoginId(),
                    ];
            }
            
            if($user_info->isHospitalUser())
            {
                $comment_insert[] = [
                        'topicId' => $topic->topicId,
                        'comment' =>$comment_data['comment'],
                        'name' => $comment_data['name'],
                        'authKey' => $topic->authKey,
                        'B_Id' => $user_info->getLoginId(),
                    ];
            }
            
            $result = CommentTr::insert($comment_insert);
            
            
            $mail_body = $this->view('NewJoyPla/view/Mail/Comment', [
                'name' => '%val:usr:name%',
                'commentUser' => $comment_data['name'],
                'topicTitle' => $topic->topicTitle,
                'login_url' => OROSHI_LOGIN_URL,
            ] , false)->render();
            
            $select_name = $this->makeId($topic->distributorId);

            $test = DistributorAffiliationView::selectName($select_name)
                ->rule(
                    ['name'=>'distributorId','label'=>'name_'.$topic->distributorId,'value1'=>$topic->distributorId,'condition'=>'matches']
                )
                ->rule(
                    ['name'=>'invitingAgree','label'=>'invitingAgree','value1'=>'t','condition'=>'is_boolean']
                )->filterCreate();

            $test = DistributorAffiliationView::selectRule($select_name)
                ->body($mail_body)
                ->subject("[JoyPla] トピックにコメントがありました")
                ->from(FROM_ADDRESS,FROM_NAME)
                ->send();
            
            
            $mail_body = $this->view('NewJoyPla/view/Mail/Comment', [
                'name' => '%val:usr:name%',
                'commentUser' => $comment_data['name'],
                'topicTitle' => $topic->topicTitle,
                'login_url' => LOGIN_URL,
            ] , false)->render();
            
            $hospital_user = HospitalUser::getNewInstance();
            
            $select_name = $this->makeId($topic->hospitalId);
            $test = $hospital_user::selectName($select_name)
                ->rule(['name'=>'hospitalId','label'=>'name_'.$topic->hospitalId,'value1'=>$topic->hospitalId,'condition'=>'matches'])
                ->filterCreate();
                
            $test = $hospital_user::selectRule($select_name)
                ->body($mail_body)
                ->subject("[JoyPla] トピックにコメントがありました")
                ->from(FROM_ADDRESS,FROM_NAME)
                ->send();
            
            if($topic->adminViewFlg == "1")
            {  
                $select_name = $this->makeId($topic->tenantId);
    
                $test = TenantMaster::selectName($select_name)->rule([
                    'name'=>'tenantId',
                    'label'=>'name_'.$topic->tenantId,
                    'value1'=>$topic->tenantId,
                    'condition'=>'matches'
                ])->filterCreate();
    
                $test = TenantMaster::selectRule($select_name)
                    ->body($mail_body)
                    ->subject("[JoyPla] トピックにコメントがありました")
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
    
    public function commentDeleteApi()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            
            $user_info = new UserInfo($SPIRAL);
            
            $record_id = (int)$SPIRAL->getCardId();
            $topic = Topic::find($record_id)->get();
            $topic = $topic->data->get(0);
            
            $comment_id = (int)$SPIRAL->getParam("id");
            $comment = Comment::find($comment_id)->where('topicId',$topic->topicId)->get();
            $comment = $comment->data->get(0);
            
            if(
                ($user_info->isDistributorUser() && $comment->O_Id == $user_info->getLoginId())|| 
                ($user_info->isHospitalUser() && $comment->B_Id == $user_info->getLoginId()))
            {
                $comment->deleteFlg = "t";
            }
            
            $result = $comment->save();
            
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
    
    public function getCommentApi()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            
            $limit = 3;
            
            $user_info = new UserInfo($SPIRAL);
            
            $record_id = (int)$SPIRAL->getCardId();
            $topic = Topic::find($record_id)->get();
            $topic = $topic->data->get(0);
            
            $page = $SPIRAL->getParam('page');
            if($page == "")
            {
                $page = 1;
            }
            
            $result = Comment::where('topicId',$topic->topicId)->sort('id', 'desc')->page($page)->paginate($limit);
            $comments = $result->data->all();
            
            foreach($comments as $key => $comment)
            {
                if($user_info->isDistributorUser() && $comment->O_Id == $user_info->getLoginId())
                {
                    $comment->deletableFlag = true;
                }
                
                if($user_info->isHospitalUser() && $comment->B_Id == $user_info->getLoginId())
                {
                    $comment->deletableFlag = true;
                }
                if($comment->deleteFlg == "1")
                {
                    $comment->comment = "";
                    $comment->deletableFlag = false;
                }
                
                $comments[$key] = $comment;
            }

            $content = new ApiResponse($comments , $result->count , $result->code, $result->message, ['insert']);
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
$TopicSlipController = new TopicSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'comment')
    {
        echo $TopicSlipController->comment()->render();
    }
    else if($action === 'getCommentApi')
    {
        echo $TopicSlipController->getCommentApi()->render();
    }
    else if($action === 'commentDeleteApi')
    {
        echo $TopicSlipController->commentDeleteApi()->render();
    }
    else 
    {
        echo $TopicSlipController->index()->render();
    }
    
}

