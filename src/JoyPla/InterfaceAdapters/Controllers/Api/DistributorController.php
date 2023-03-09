<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Distributor\DistributorShowInputData;
use JoyPla\Application\InputPorts\Api\Distributor\DistributorShowInputPortInterface;

class DistributorController extends Controller
{
    public function show($vars, DistributorShowInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $inputData = new DistributorShowInputData(
            $this->request->user()->hospitalId
        );
        $inputPort->handle($inputData);
    }
}
