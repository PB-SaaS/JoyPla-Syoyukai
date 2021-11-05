<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;

use App\Lib\UserInfo;
use App\Model\Comment;
use App\Model\Topic;

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
            $comment_insert[] = [
                    'topicId' => $topic->topicId,
                    'comment' =>$comment_data['comment'],
                    'name' => $comment_data['name'],
                    'authKey' => $topic->authKey,
                ];
            
            $result = Comment::insert($comment_insert);

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
$TopicSlipController = new TopicSlipController();

$action = $SPIRAL->getParam('Action');

{
    if($action === 'comment')
    {
        echo $TopicSlipController->comment()->render();
    }
    else 
    {
        echo $TopicSlipController->index()->render();
    }
    
}

