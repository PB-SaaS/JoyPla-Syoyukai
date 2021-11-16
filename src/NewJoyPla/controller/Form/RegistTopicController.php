<?php
namespace App\Controller;

use View;
use Controller;
use SpiralApiRequest;
use ApiResponse;
use Csrf;
use App\Lib\UserInfo;
use App\Model\DistributorUser;
use App\Model\HospitalUser;
use App\Model\DistributorAffiliationView;

use ApiErrorCode\FactoryApiErrorCode;
use stdClass;
use Exception;

class RegistTopicController extends Controller
{
    public function __construct()
    {
    }
    
    
    public function input()
    {
    }
    
    
    public function confirm()
    {
    }
    
    public function thank()
    {
        global $SPIRAL;
        
        try{
            $hospital = $SPIRAL->getContextByFieldTitle("hospitalId");
            if($hospital != "")
            {
            	$topicTitle = \App\Lib\html($SPIRAL->getContextByFieldTitle("topicTitle"));
            	$topicName = \App\Lib\html($SPIRAL->getContextByFieldTitle("topicName"));
            	
            	$subject = "[JoyPla] ".$topicName."さんがトピック「".$topicTitle."」を作成しました";
            	
            	$url = LOGIN_URL;
            	
                $mail_body = $this->view('NewJoyPla/view/Mail/RegistTopic', [
                    'name' => '%val:usr:name%',
                    'topicTitle' => $topicTitle,
                    'registUser' => $topicName,
                    'login_url' => LOGIN_URL,
                ] , false)->render();
                
                $select_name = $this->makeId($hospital);

                $hospital_user = HospitalUser::getNewInstance();
                $test = $hospital_user::selectName($select_name)->rule(
                    ['name'=>'hospitalId','label'=>'name_'.$hospital,'value1'=>$hospital,'condition'=>'matches']
                    )->filterCreate();

                $test = $hospital_user::selectRule($select_name)
                    ->body($mail_body)
                    ->subject($subject)
                    ->from(FROM_ADDRESS,FROM_NAME)
                    ->send();
            }
            
            /****
             * 卸ユーザーへの送信 
             */
            $distributor = $SPIRAL->getContextByFieldTitle("distributorId");
            if($distributor  != '' ){
            		
            	$topicTitle = \App\Lib\html($SPIRAL->getContextByFieldTitle("topicTitle"));
            	$topicName = \App\Lib\html($SPIRAL->getContextByFieldTitle("topicName"));
            	
            	$subject = "[JoyPla] ".$topicName."さんがトピック「".$topicTitle."」を作成しました";
            	
            	$url = LOGIN_URL;
            	
                $mail_body = $this->view('NewJoyPla/view/Mail/RegistTopic', [
                    'name' => '%val:usr:name%',
                    'topicTitle' => $topicTitle,
                    'registUser' => $topicName,
                    'login_url' => OROSHI_LOGIN_URL,
                ] , false)->render();
                
                $select_name = $this->makeId($distributor);

                $distributor_user = DistributorAffiliationView::getNewInstance();
                $test = $distributor_user::selectName($select_name)->rule(
                    ['name'=>'distributorId','label'=>'name_'.$distributor,'value1'=>$distributor,'condition'=>'matches']
                    )
                    ->rule([
                        'name'=>'invitingAgree',
                        'label'=>'invitingAgree',
                        'value1'=>'t',
                        'condition'=>'is_boolean'
                    ])->filterCreate();

                $test = $distributor_user::selectRule($select_name)
                    ->body($mail_body)
                    ->subject($subject)
                    ->from(FROM_ADDRESS,FROM_NAME)
                    ->send();
            }
           
        } catch ( Exception $ex ) {
            $content = $this->view('NewJoyPla/view/template/Error', [
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage(),
                ] , false);
        } finally {
            
        }
    }
}