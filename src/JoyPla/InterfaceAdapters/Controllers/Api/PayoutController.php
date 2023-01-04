<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use Exception;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\Payout\PayoutRegisterInputData;
use JoyPla\Application\InputPorts\Api\Payout\PayoutRegisterInputPortInterface;


class PayoutController extends Controller
{

    public function register($vars, PayoutRegisterInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('register_of_payouts')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_of_payouts');

        $payoutItems = $this->request->get('payoutItems');

        $user = $this->request->user();

        $inputData = new PayoutRegisterInputData(
            $user,
            $payoutItems,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }
}
