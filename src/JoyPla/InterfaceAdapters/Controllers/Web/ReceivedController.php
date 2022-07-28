<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use App\SpiralDb\HospitalUser;
use Auth;
use framework\Http\Controller;
use framework\Http\View;
use JoyPla\Application\InputPorts\Web\Received\OrderReceivedSlipIndexInputData;
use JoyPla\Application\InputPorts\Web\Received\OrderReceivedSlipIndexInputPortInterface;

class ReceivedController extends Controller
{
    
    public function orderList($vars) 
    {
        $body = View::forge('html/Received/OrderList', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    } 

    
    public function orderReceivedSlipIndex($vars, OrderReceivedSlipIndexInputPortInterface $inputPort ) {
        $inputData = new OrderReceivedSlipIndexInputData((new Auth(HospitalUser::class))->hospitalId,$vars['orderId']);
        $inputPort->handle($inputData);
    }
}
 