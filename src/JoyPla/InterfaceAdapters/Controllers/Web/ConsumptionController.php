<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use App\SpiralDb\HospitalUser;
use Auth;
use framework\Http\Controller;
use framework\Http\View;
use JoyPla\Application\InputPorts\Web\Consumption\ConsumptionIndexInputData;
use JoyPla\Application\InputPorts\Web\Consumption\ConsumptionIndexInputPortInterface;

class ConsumptionController extends Controller
{
    public function register($vars ) {
        $body = View::forge('html/Consumption/ConsumptionRegister', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function show($vars){
        $body = View::forge('html/Consumption/ConsumptionShow', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function index($vars, ConsumptionIndexInputPortInterface $inputPort ) {
        global $SPIRAL;
        $inputData = new ConsumptionIndexInputData((new Auth(HospitalUser::class))->hospitalId,$vars['consumptionId'] );
        $inputPort->handle($inputData);
    }

    public function print($vars, ConsumptionIndexInputPortInterface $inputPort ) {
        global $SPIRAL;
        $inputData = new ConsumptionIndexInputData((new Auth(HospitalUser::class))->hospitalId,$vars['consumptionId'] );
        $inputPort->handle($inputData);
    }
}

