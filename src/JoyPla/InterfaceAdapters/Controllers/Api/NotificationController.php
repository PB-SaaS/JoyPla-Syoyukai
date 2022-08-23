<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Notification\NotificationShowInputData;
use JoyPla\Application\InputPorts\Api\Notification\NotificationShowInputPortInterface;

class NotificationController extends Controller
{
    public function show($vars , NotificationShowInputPortInterface $inputPort) 
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);
 
        $inputData = new NotificationShowInputData(
            $this->request->get('search')
        );
        $inputPort->handle($inputData);
    }
}

