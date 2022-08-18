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
        
        $isOnlyMyDivision = ( $this->request->get('isOnlyMyDivision') === 'true' || $this->request->get('isOnlyMyDivision') === '1');

        $inputData = new DivisionShowInputData($this->request->user() , $isOnlyMyDivision);
        $inputPort->handle($inputData);
    }
}

