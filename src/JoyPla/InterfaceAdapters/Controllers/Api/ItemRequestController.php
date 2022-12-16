<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use Exception;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestRegisterInputData;
use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestRegisterInputPortInterface;
//use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestDeleteInputData;
//use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestDeleteInputPortInterface;
//use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestShowInputData;
//use JoyPla\Application\InputPorts\Api\ItemRequest\ItemRequestShowInputPortInterface;

class ItemRequestController extends Controller
{
    /*
    public function show($vars, ItemRequestShowInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('list_of_ItemRequest_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_ItemRequest_slips');

        $search = $this->request->get('search');

        if ($gate->isOnlyMyDivision()) {
            $search['divisionIds'] = [$this->request->user()->divisionId];
        }

        $inputData = new ItemRequestShowInputData($this->request->user()->hospitalId, $search);
        $inputPort->handle($inputData);
    }

    public function index($vars)
    {
    }

    */
    public function register($vars, ItemRequestRegisterInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('register_of_item_requests')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_of_item_requests');

        $requestItems = $this->request->get('requestItems');

        $requestType = (int)$this->request->get('requestType');

        $user = $this->request->user();

        $inputData = new ItemRequestRegisterInputData(
            $user,
            $requestItems,
            $requestType,
            $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    /*
    public function update($vars)
    {
    }

    public function delete($vars, ItemRequestDeleteInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        if (Gate::denies('cancellation_of_ItemRequest_slips')) {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('cancellation_of_ItemRequest_slips');

        $inputData = new ItemRequestDeleteInputData($this->request->user(), $vars['ItemRequestId'], $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }
    */
}
