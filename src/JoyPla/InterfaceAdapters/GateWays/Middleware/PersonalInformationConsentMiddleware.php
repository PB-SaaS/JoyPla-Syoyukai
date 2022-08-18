<?php


namespace JoyPla\InterfaceAdapters\GateWays\Middleware;

use App\Http\Middleware\Middleware;
use App\Http\Middleware\MiddlewareInterface;
use App\SpiralDb\HospitalUser;
use App\SpiralDb\Order;
use Auth;
use framework\Http\Request;
use framework\Routing\Router;
use JoyPla\Enterprise\Models\OrderStatus;

class PersonalInformationConsentMiddleware extends Middleware implements MiddlewareInterface {

    public function process(array $vars) : void
    {
        $auth = $this->request->user();
        if($auth->termsAgreement != '2')
        {
            Router::redirect('/agree',$this->request);
            exit();
        }
    }
}