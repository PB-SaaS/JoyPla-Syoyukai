<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use framework\Http\Controller;
use JoyPla\Application\InputPorts\Api\Order\OrderRegisterInputData;
use JoyPla\Application\InputPorts\Api\Order\OrderRegisterInputPortInterface;
use JoyPla\Application\InputPorts\Api\Order\OrderShowInputData;
use JoyPla\Application\InputPorts\Api\Order\OrderShowInputPortInterface;
use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedApprovalInputData;
use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedApprovalInputPortInterface;
use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedDeleteInputData;
use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedDeleteInputPortInterface;
use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedItemDeleteInputData;
use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedItemDeleteInputPortInterface;
use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedUpdateInputData;
use JoyPla\Application\InputPorts\Api\Order\OrderUnapprovedUpdateInputPortInterface;
use JoyPla\Application\InputPorts\Api\Order\FixedQuantityOrderInputData;
use JoyPla\Application\InputPorts\Api\Order\FixedQuantityOrderInputPortInterface;
use JoyPla\Application\InputPorts\Api\Order\OrderRevisedInputData;
use JoyPla\Application\InputPorts\Api\Order\OrderRevisedInputPortInterface;
use JoyPla\Enterprise\Models\OrderStatus;

class OrderController extends Controller
{
    
    public function register($vars , OrderRegisterInputPortInterface $inputPort ) 
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $orderItems = $this->request->get('orderItems');
        $integrate = $this->request->get('integrate');

        $user = new Auth(HospitalUser::class);
        
        $inputData = new OrderRegisterInputData($user,(new Auth(HospitalUser::class))->hospitalId, $orderItems , ($integrate == 'true') );
        $inputPort->handle($inputData , 2);
    } 

    public function fixedQuantityOrderRegister($vars , OrderRegisterInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $orderItems = $this->request->get('orderItems');

        $user = new Auth(HospitalUser::class);
        
        $inputData = new OrderRegisterInputData($user,(new Auth(HospitalUser::class))->hospitalId, $orderItems , false );
        $inputPort->handle($inputData , 1);
    }
    
    public function show($vars , OrderShowInputPortInterface $inputPort )
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
                OrderStatus::ReceivingIsComplete,
                OrderStatus::DeliveryIsCanceled,
                OrderStatus::Borrowing
            ];
        }

        $inputData = new OrderShowInputData((new Auth(HospitalUser::class))->hospitalId, $search);
        $inputPort->handle($inputData);
    }

    public function unapprovedShow($vars , OrderShowInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);
        $search = $this->request->get('search');
        $search['orderStatus'] = [OrderStatus::UnOrdered];
        $search['orderDate'] = "";

        $inputData = new OrderShowInputData((new Auth(HospitalUser::class))->hospitalId, $search);
        $inputPort->handle($inputData);
    }

    public function unapprovedItemDelete($vars , OrderUnapprovedItemDeleteInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $orderId = $vars['orderId'];
        $orderItemId = $vars['orderItemId'];
        
        $inputData = new OrderUnapprovedItemDeleteInputData((new Auth(HospitalUser::class))->hospitalId, $orderId, $orderItemId);
        $inputPort->handle($inputData);
    }

    public function unapprovedUpdate($vars , OrderUnapprovedUpdateInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $orderId = $vars['orderId'];
        $adjustment = $this->request->get('adjustment');
        $updateModel = $this->request->get('updateModel');

        $order = [
            'orderId' => $orderId,
            'adjustment' => $adjustment,
            'updateModel' => $updateModel,
        ];
        
        $inputData = new OrderUnapprovedUpdateInputData(
            (new Auth(HospitalUser::class))->hospitalId, $order
        );
        $inputPort->handle($inputData);
    }

    public function unapprovedDelete($vars , OrderUnapprovedDeleteInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $orderId = $vars['orderId'];
        
        $inputData = new OrderUnapprovedDeleteInputData(
            (new Auth(HospitalUser::class))->hospitalId, $orderId
        );
        $inputPort->handle($inputData);
    }

    public function approval($vars , OrderUnapprovedApprovalInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $orderId = $vars['orderId'];
        
        $inputData = new OrderUnapprovedApprovalInputData(
            (new Auth(HospitalUser::class))->hospitalId, $orderId
        );
        $inputPort->handle($inputData);
    }

    public function fixedQuantityOrder($vars, FixedQuantityOrderInputPortInterface $inputPort ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $inputData = new FixedQuantityOrderInputData((new Auth(HospitalUser::class)) , $this->request->get('search'));

        $inputPort->handle($inputData);
    }

    public function revised($vars, OrderRevisedInputPortInterface $inputPort ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $revisedOrderQuantityModel = $this->request->get('revisedOrderQuantityModel');

        if(!is_array($revisedOrderQuantityModel))
        {
            $revisedOrderQuantityModel = [];
        }

        $inputData = new OrderRevisedInputData((new Auth(HospitalUser::class)) , $vars['orderId'] , $revisedOrderQuantityModel);

        $inputPort->handle($inputData);
    }

    public function update($vars ) 
    {
    }
    
    public function delete($vars) 
    {
    }
}

 