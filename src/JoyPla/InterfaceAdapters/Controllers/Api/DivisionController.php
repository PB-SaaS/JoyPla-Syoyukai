<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Division\DivisionShowInputData;
use JoyPla\Application\InputPorts\Api\Division\DivisionShowInputPortInterface;

class DivisionController extends Controller
{
    public function show($vars ,DivisionShowInputPortInterface $inputPort ) 
    {
        global $SPIRAL;     
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $inputData = new DivisionShowInputData((new Auth(HospitalUser::class))->hospitalId);
        $inputPort->handle($inputData);
    }
}

