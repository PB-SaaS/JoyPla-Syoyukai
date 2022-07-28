<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Distributor\DistributorShowInputData;
use JoyPla\Application\InputPorts\Api\Distributor\DistributorShowInputPortInterface;

class DistributorController extends Controller
{
    public function show($vars ,DistributorShowInputPortInterface $inputPort ) 
    {
        global $SPIRAL;     
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $inputData = new DistributorShowInputData((new Auth(HospitalUser::class))->hospitalId);
        $inputPort->handle($inputData);
    }
}

