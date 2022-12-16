<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use Exception;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\Reference\ConsumptionHistoryShowInputData;
use JoyPla\Application\InputPorts\Api\Reference\ConsumptionHistoryShowInputPortInterface;

class ReferenceController extends Controller
{
    public function consumption($vars, ConsumptionHistoryShowInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('list_of_consumption_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_consumption_slips');

        $search = $this->request->get('search');

        if ($gate->isOnlyMyDivision()) {
            $search['divisionIds'] = [$this->request->user()->divisionId];
        } else {
            $search['divisionIds'] = $this->request->get('divisionIds');
        }

        $inputData = new ConsumptionHistoryShowInputData($this->request->user()->hospitalId, $search);
        $inputPort->handle($inputData);
    }
}
