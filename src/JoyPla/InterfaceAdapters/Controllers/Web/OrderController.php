<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use App\SpiralDb\HospitalUser;
use App\SpiralDb\Order as SpiralDbOrder;
use Auth;
use framework\Facades\Gate;
use framework\Http\Controller;
use framework\Http\View;
use framework\Routing\Router;
use JoyPla\Application\InputPorts\Web\Order\FixedQuantityOrderInputData;
use JoyPla\Application\InputPorts\Web\Order\FixedQuantityOrderInputPortInterface;
use JoyPla\Application\InputPorts\Web\Order\OrderIndexInputData;
use JoyPla\Application\InputPorts\Web\Order\OrderIndexInputPortInterface;
use JoyPla\Enterprise\Models\OrderStatus;

class OrderController extends Controller
{
    public function register($vars ) {
        if(Gate::denies('register_of_unordered_slips'))
        {
            Router::abort(403);
        }
        $body = View::forge('html/Order/Register', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function unapprovedShow(){ 
        if(Gate::denies('list_of_unordered_slips'))
        {
            Router::abort(403);
        }
        $body = View::forge('html/Order/UnapprovedShow', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function unapprovedIndex($vars, OrderIndexInputPortInterface $inputPort ) {
        if(Gate::denies('list_of_unordered_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_unordered_slips');

        $inputData = new OrderIndexInputData($this->request->user(),$vars['orderId'] , true , $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }

    public function fixedQuantityOrder($vars)
    {
        if(Gate::denies('fixed_quantity_order_slips'))
        {
            Router::abort(403);
        }
        $body = View::forge('html/Order/FixedQuantityOrder', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
    
    public function show(){
        if(Gate::denies('list_of_order_slips'))
        {
            Router::abort(403);
        }

        $body = View::forge('html/Order/Show', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
    
    public function index($vars, OrderIndexInputPortInterface $inputPort ) {
        if(Gate::denies('list_of_order_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_order_slips');

        $inputData = new OrderIndexInputData($this->request->user(),$vars['orderId'] , false , $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }
    
    public function print($vars, OrderIndexInputPortInterface $inputPort ) {
        if(Gate::denies('list_of_order_slips'))
        {
            Router::abort(403);
        }

        $gate = Gate::getGateInstance('list_of_order_slips');

        $inputData = new OrderIndexInputData($this->request->user(),$vars['orderId'] , false , $gate->isOnlyMyDivision());
        $inputPort->handle($inputData);
    }
}

