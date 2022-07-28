<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemShowInputData;
use JoyPla\Application\InputPorts\Api\InHospitalItem\InHospitalItemShowInputPortInterface;

class InHospitalItemController extends Controller
{
    public function show($vars ,InHospitalItemShowInputPortInterface $inputPort ) 
    {
        global $SPIRAL;     
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);
 
        $inputData = new InHospitalItemShowInputData(
            (new Auth(HospitalUser::class))->hospitalId,
            $this->request->get('search')
        );
        $inputPort->handle($inputData);
    }
}

