<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\Acceptance\AcceptanceRegisterInputData;
use JoyPla\Application\InputPorts\Api\Acceptance\AcceptanceRegisterInputPortInterface;

class AcceptanceController extends Controller
{
    
    public function register($vars, AcceptanceRegisterInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('register_of_payouts')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_of_payouts');

        //$isOnlyPayout = ( $this->request->get('isOnlyPayout') === 'true' );

        $acceptanceItems = $this->request->get('acceptanceItems');

        $user = $this->request->user();

        $inputData = new AcceptanceRegisterInputData(
            $user,
            $acceptanceItems,
            $gate->isOnlyMyDivision(),
        );
        $inputPort->handle($inputData);
    }
}
