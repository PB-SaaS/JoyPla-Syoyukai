<?php

namespace JoyPla\InterfaceAdapters\Controllers\Hospital\Top ;

use framework\Http\Controller;
use JoyPla\Application\InputPorts\Hospital\Top\TopIndexInputData;
use JoyPla\Application\InputPorts\Hospital\Top\TopIndexInputPortInterface;
use JoyPla\Application\InputPorts\Hospital\Top\TopOrderPageInputData;
use JoyPla\Application\InputPorts\Hospital\Top\TopOrderPageInputPortInterface;

class TopController extends Controller
{
    public function index($vars ,TopIndexInputPortInterface $inputPort ) {
        $inputData = new TopIndexInputData();
        $inputPort->handle($inputData);
    }

    public function orderpage($vars ,TopOrderPageInputPortInterface $inputPort ) {
        $inputData = new TopOrderPageInputData();
        $inputPort->handle($inputData);
    }
}

