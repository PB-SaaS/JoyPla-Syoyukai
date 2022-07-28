<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Order\OrderShowInputData;
use JoyPla\Application\InputPorts\Api\Order\OrderShowInputPortInterface;
use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterByOrderSlipInputData;
use JoyPla\Application\InputPorts\Api\Received\ReceivedRegisterByOrderSlipInputPortInterface;
use JoyPla\Enterprise\Models\OrderStatus;
use JoyPla\Enterprise\Models\ReceivedStatus;

class ReceivedController extends Controller
{
    public function orderList($vars , OrderShowInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);
        $search = $this->request->get('search');
        
        if( !$search['orderStatus'] || count($search['orderStatus']) === 0 )
        {
            $search['orderStatus'] = [
                OrderStatus::OrderCompletion,
                OrderStatus::OrderFinished,
                OrderStatus::DeliveryDateReported,
                OrderStatus::PartOfTheCollectionIsIn,
            ];
        }

        $inputData = new OrderShowInputData((new Auth(HospitalUser::class))->hospitalId, $search);
        $inputPort->handle($inputData);
    }

    public function orderRegister($vars , ReceivedRegisterByOrderSlipInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);
        $registerModel = $this->request->get('registerModel');
        
        var_dump($registerModel);
        
        $inputData = new ReceivedRegisterByOrderSlipInputData((new Auth(HospitalUser::class))->hospitalId, $vars['orderId'], $registerModel);
    }

}

 