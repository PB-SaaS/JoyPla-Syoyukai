<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use framework\Http\Controller;
use framework\Http\View;
use framework\Library\SiValidator;
use framework\Library\SpiralDbRule;
use framework\Routing\Router;

class AgreeFormController extends Controller
{
    public function index($vars) {
        $user = ($this->request->user());

        if($user->termsAgreement == '2')
        {
            echo Router::redirect('/',$this->request);
            exit();
        }
        
        $body = View::forge('html/Agree/Index', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
    
    public function send($vars) {
        
        $token = $this->request->get('_token');
        Csrf::validate($token,true);

        $user = ($this->request->user());
        if($this->request->get('agree') == '1')
        {
            HospitalUser::where('loginId', $user->loginId)->update(
                [
                    'agreementDate' => 'now',
                    'termsAgreement' => '2'
                ]
            );

            $this->request->user()->termsAgreement = 2;

            echo Router::redirect('/',$this->request);
            exit();
        }
        
        echo Router::redirect('/agree',$this->request);
    }
}

