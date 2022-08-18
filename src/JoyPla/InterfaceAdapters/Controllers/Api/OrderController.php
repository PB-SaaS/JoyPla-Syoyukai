<?php

namespace JoyPla\InterfaceAdapters\Controllers\Api ;

use App\SpiralDb\HospitalUser;
use Auth;
use Csrf;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Routing\Router;
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
use JoyPla\Application\InputPorts\Api\Order\OrderUnReceivedShowInputData;
use JoyPla\Application\InputPorts\Api\Order\OrderUnReceivedShowInputPortInterface;
use JoyPla\Application\InputPorts\Api\Order\OrderRevisedInputData;
use JoyPla\Application\InputPorts\Api\Order\OrderRevisedInputPortInterface;
use JoyPla\Enterprise\Models\OrderStatus;

class OrderController extends Controller
{
    
    public function register($vars , OrderRegisterInputPortInterface $inputPort ) 
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        if(Gate::denies('register_of_unordered_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_of_unordered_slips');

        $orderItems = $this->request->get('orderItems');
        $integrate = $this->request->get('integrate');

        $user = $this->request->user();
        
        $inputData = new OrderRegisterInputData($user, $orderItems , ($integrate == 'true') , $gate->isOnlyMyDivision());
        $inputPort->handle($inputData , 2);
    } 

    public function fixedQuantityOrderRegister($vars , OrderRegisterInputPortInterface $inputPort)
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        if(Gate::denies('register_of_unordered_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('register_of_unordered_slips');

        $orderItems = $this->request->get('orderItems');

        $user = $this->request->user();
        
        $inputData = new OrderRegisterInputData($user, $orderItems , false , $gate->isOnlyMyDivision());
        $inputPort->handle($inputData , 1);
    }

    public function unreceivedShow($vars ,OrderUnReceivedShowInputPortInterface $inputPort ) 
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);
 
        $search = $this->request->get('search');
        $user = $this->request->user();

        if(Gate::denies('receipt'))
        {
            Router::abort(403); 
        }

        $gate = Gate::getGateInstance('receipt');

        if( $gate->isOnlyMyDivision() )
        {
            $search['divisionIds'] = [ $user->divisionId ];
        }

        $inputData = new OrderUnReceivedShowInputData(
            $user,
            $search
        );
        $inputPort->handle($inputData);
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
        
        $user = $this->request->user();

        if(Gate::denies('list_of_order_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_order_slips');

        
        if( $gate->isOnlyMyDivision() )
        {
            $search['divisionIds'] = [ $user->divisionId ];
        }

        $inputData = new OrderShowInputData($user , $search);
        $inputPort->handle($inputData);
    }

    public function unapprovedShow($vars , OrderShowInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);
        $search = $this->request->get('search');

        $user = $this->request->user();

        $search['orderStatus'] = [OrderStatus::UnOrdered];
        $search['orderDate'] = "";
        
        if(Gate::denies('list_of_unordered_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_unordered_slips');

        
        if( $gate->isOnlyMyDivision() )
        {
            $search['divisionIds'] = [ $user->divisionId ];
        }

        $inputData = new OrderShowInputData($user, $search);
        $inputPort->handle($inputData);
    }

    public function unapprovedItemDelete($vars , OrderUnapprovedItemDeleteInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $orderId = $vars['orderId'];
        $orderItemId = $vars['orderItemId'];
        
        if(Gate::denies('revision_of_unordered_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('revision_of_unordered_slips');
        
        $inputData = new OrderUnapprovedItemDeleteInputData($this->request->user(), $orderId, $orderItemId, $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }

    public function unapprovedUpdate($vars , OrderUnapprovedUpdateInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        if(Gate::denies('revision_of_unordered_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('revision_of_unordered_slips');

        $orderId = $vars['orderId'];
        $adjustment = $this->request->get('adjustment');
        $updateModel = $this->request->get('updateModel');
        $comment = $this->request->get('comment');

        $order = [
            'orderId' => $orderId,
            'adjustment' => $adjustment,
            'updateModel' => $updateModel,
            'comment' => $comment,
        ];
        
        $inputData = new OrderUnapprovedUpdateInputData(
            $this->request->user(), $order , $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function unapprovedDelete($vars , OrderUnapprovedDeleteInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $orderId = $vars['orderId'];
        
        if(Gate::denies('deletion_of_unordered_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('deletion_of_unordered_slips');

        $inputData = new OrderUnapprovedDeleteInputData(
            $this->request->user(), $orderId, $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function approval($vars , OrderUnapprovedApprovalInputPortInterface $inputPort )
    {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        $orderId = $vars['orderId'];

        if(Gate::denies('decision_of_order_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('decision_of_order_slips');

        
        $inputData = new OrderUnapprovedApprovalInputData(
            $this->request->user(), $orderId , $gate->isOnlyMyDivision()
        );
        $inputPort->handle($inputData);
    }

    public function fixedQuantityOrder($vars, FixedQuantityOrderInputPortInterface $inputPort ) {
        $token = $this->request->get('_csrf');
        Csrf::validate($token,true);

        if(Gate::denies('fixed_quantity_order_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('fixed_quantity_order_slips');

        $user = $this->request->user();
        $search = $this->request->get('search');

        if( $gate->isOnlyMyDivision() )
        {
            $search['divisionIds'] = [ $user->divisionId ];
        }

        $inputData = new FixedQuantityOrderInputData($user , $search );

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

        $inputData = new OrderRevisedInputData(($this->request->user()) , $vars['orderId'] , $revisedOrderQuantityModel);

        $inputPort->handle($inputData);
    }

    public function update($vars ) 
    {
    }
    
    public function delete($vars) 
    {
    }
}

 