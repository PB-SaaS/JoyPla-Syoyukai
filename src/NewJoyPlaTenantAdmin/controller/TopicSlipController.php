<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\Auth;
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
    
    public function index()
    {
        global $SPIRAL;
        try {

            $auth = new Auth();
            
            $content = $this->view('NewJoyPlaTenantAdmin/view/TopicList/Slip', [
                'title' => 'トピック',
                'api_url' => '%url/card:page_179194%',
                'csrf_token' => Csrf::generate(16)
                ] , false)->render();
    
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPlaTenantAdmin/view/Template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false)->render();
        } finally {
            $script = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/TableScript', [] , false)->render();
            $style = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/StyleCss', [] , false)->render();
            $sidemenu = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/SideMenu', [
                'n7' => 'uk-active',
                ] , false)->render();
            $head = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Head', [] , false)->render();
            $header = $this->view('NewJoyPlaTenantAdmin/view/Template/Parts/Header', [], false)->render();
            // テンプレートにパラメータを渡し、HTMLを生成し返却
            return $this->view('NewJoyPlaTenantAdmin/view/Template/Base', [
                'back_url' => "%url/rel:mpgt:TopPage%&Action=topics&table_cache=true",
                'back_text' => 'トピック一覧',
                'title'     => 'JoyPla-Tenant-Master トピック',
                'sidemenu'  => $sidemenu,
                'content'   => $content,
                'head' => $head,
                'header' => $header,
                'style' => $style,
                'before_script' => $script,
            ],false);
        }
    }
    
    public function comment()
    {
        global $SPIRAL;
        try{
            $token = (!isset($_POST['_csrf']))? '' : $_POST['_csrf'];
            Csrf::validate($token,true);
            
            $auth = new Auth();
            
            $comment_data = $SPIRAL->getParam('commentData');
            $comment_data = $this->requestUrldecode($comment_data);
            
            $record_id = (int)$SPIRAL->getCardId();
            $topic = Topic::find($record_id)->get();
            
            $topic = $topic->data->get(0);
            
            $comment_insert = [];
            $comment_insert[] = [
                    'topicId' => $topic->topicId,
                    'comment' =>$comment_data['comment'],
                    'name' => $comment_data['name'],
                    'authKey' => $topic->authKey,
                    'M_Id' => $auth->loginId,
                ];
                
            $result = CommentTr::insert($comment_insert);
            
            {
                $mail_body = $this->view('NewJoyPlaTenantAdmin/view/Mail/Comment', [
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
            }
            
            {
                $mail_body = $this->view('NewJoyPlaTenantAdmin/view/Mail/Comment', [
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
                
                $mail_body = $this->view('NewJoyPlaTenantAdmin/view/Mail/Comment', [
                    'name' => '%val:usr:name%',
                    'commentUser' => $comment_data['name'],
                    'topicTitle' => $topic->topicTitle,
                    'login_url' => TENANT_ADMIN_LOGIN_URL,
                ] , false)->render();    
            }
                    
            {
                $mail_body = $this->view('NewJoyPlaTenantAdmin/view/Mail/Comment', [
                    'name' => '%val:usr:name%',
                    'commentUser' => $comment_data['name'],
                    'topicTitle' => $topic->topicTitle,
                    'login_url' => TENANT_ADMIN_LOGIN_URL,
                ] , false)->render();
                
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
            return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
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
            
            $auth = new Auth();
            
            $record_id = (int)$SPIRAL->getCardId();
            $topic = Topic::find($record_id)->get();
            $topic = $topic->data->get(0);
            
            $comment_id = (int)$SPIRAL->getParam("id");
            $comment = Comment::find($comment_id)->where('topicId',$topic->topicId)->get();
            $comment = $comment->data->get(0);
            
            if($comment->M_Id == $auth->loginId)
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
            return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
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
            
            $auth = new Auth();
            
            $limit = 3;
            
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
                if($comment->M_Id == $auth->loginId)
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
            return $this->view('NewJoyPlaTenantAdmin/view/Template/ApiResponseBase', [
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

