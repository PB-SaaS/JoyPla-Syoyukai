<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api;

use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Distributor\DistributorIndexInputData;
use JoyPla\Application\InputPorts\Api\Distributor\DistributorIndexInputPortInterface;

class DistributorController extends Controller
{
    public function index($vars, DistributorIndexInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token, true);

        $inputData = new DistributorIndexInputData(
            $this->request->user()->hospitalId
        );
        $inputPort->handle($inputData);
    }
}
