<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Division\DivisionIndexInputData;
use JoyPla\Application\InputPorts\Api\Division\DivisionIndexInputPortInterface;

class DivisionController extends Controller
{
    public function index($vars, DivisionIndexInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $isOnlyUseData =
            $this->request->get('isOnlyUseData') === 'true' ||
            $this->request->get('isOnlyUseData') === '1';

        $isOnlyMyDivision =
            $this->request->get('isOnlyMyDivision') === 'true' ||
            $this->request->get('isOnlyMyDivision') === '1';

        $inputData = new DivisionIndexInputData(
            $this->request->user(),
            $isOnlyMyDivision,
            $isOnlyUseData
        );
        $inputPort->handle($inputData);
    }
}
