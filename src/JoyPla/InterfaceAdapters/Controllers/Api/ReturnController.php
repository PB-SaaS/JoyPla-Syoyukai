<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\ReceivedReturn\ReturnShowInputData;
use JoyPla\Application\InputPorts\Api\ReceivedReturn\ReturnShowInputPortInterface;

class ReturnController extends Controller
{
    public function show($vars , ReturnShowInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $user = $this->request->user();
        $search = $this->request->get('search');
        
        if(Gate::denies('list_of_return_slips'))
        {
            Router::abort(403); 
        }

        $gate = Gate::getGateInstance('list_of_return_slips');

        if($gate->isOnlyMyDivision())
        {
            $search['divisionIds'] = [ $user->divisionId ];
        }

        $inputData = new ReturnShowInputData($user , $search);
        $inputPort->handle($inputData);
    }
}
