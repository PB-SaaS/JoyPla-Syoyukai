<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionRegisterInputData;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionRegisterInputPortInterface;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionShowInputData;
use JoyPla\Application\InputPorts\Api\Consumption\ConsumptionShowInputPortInterface;

class ConsumptionController extends Controller
{
    public function show($vars , ConsumptionShowInputPortInterface $inputPort ) 
    {
        global $SPIRAL;
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $inputData = new ConsumptionShowInputData((new Auth(HospitalUser::class))->hospitalId,$this->request->get('search') );
        $inputPort->handle($inputData);
    }

    public function index($vars) 
    {
    }
    
    public function register($vars , ConsumptionRegisterInputPortInterface $inputPort ) 
    {
        global $SPIRAL;     
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $consumptionItems = $this->request->get('consumptionItems');
        $consumptionDate = $this->request->get('consumptionDate');

        $inputData = new ConsumptionRegisterInputData(
            (new Auth(HospitalUser::class))->hospitalId,
            $consumptionDate,
            $consumptionItems, 
            [
                'userName' => $SPIRAL->getContextByFieldTitle("name"),
                'loginId' => $SPIRAL->getContextByFieldTitle("loginId"),
            ]);
        $inputPort->handle($inputData);
    }

    public function update($vars ) 
    {
    }
    
    public function delete($vars) 
    {
    }
}

 