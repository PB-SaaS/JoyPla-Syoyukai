<?php

namespace JoyPla\InterfaceAdapters\Controllers\Web ;

use App\SpiralDb\HospitalUser;
use App\SpiralDb\Order as SpiralDbOrder;
use Auth;
use framework\Http\Controller;
use framework\Http\View;
use JoyPla\Application\InputPorts\Web\Order\FixedQuantityOrderInputData;
use JoyPla\Application\InputPorts\Web\Order\FixedQuantityOrderInputPortInterface;
use JoyPla\Application\InputPorts\Web\Order\OrderIndexInputData;
use JoyPla\Application\InputPorts\Web\Order\OrderIndexInputPortInterface;
use JoyPla\Enterprise\Models\OrderStatus;

class OrderController extends Controller
{
    public function register($vars ) {
        $body = View::forge('html/Order/Register', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function unapprovedShow(){ 
        $body = View::forge('html/Order/UnapprovedShow', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }

    public function unapprovedIndex($vars, OrderIndexInputPortInterface $inputPort ) {
        $inputData = new OrderIndexInputData((new Auth(HospitalUser::class))->hospitalId,$vars['orderId'] , true);
        $inputPort->handle($inputData);
    }

    public function fixedQuantityOrder($vars)
    {
        $body = View::forge('html/Order/FixedQuantityOrder', [], false)->render();

        $auth = new Auth(HospitalUser::class);
        $unOrder = SpiralDbOrder::where('hospitalId',$auth->hospitalId)->where('orderStatus',OrderStatus::UnOrdered)->value('id');
        if($unOrder->count == 0)
        {
            $body = <<< EOL
            <script>
            Swal.fire({
                title: '未発注書が存在するため定数発注は使用できません。',
                text: "未発注書一覧へ遷移します。",
                icon: 'warning',
                confirmButtonText: 'OK'
            }).then((result) => {
                location.href = _ROOT + "&path=/order/unapproved/show";   
            })
            </script>
            EOL;
        }
        echo view('html/Common/Template', compact('body'), false)->render();
    }
    
    public function show(){
        $body = View::forge('html/Order/Show', [], false)->render();
        echo view('html/Common/Template', compact('body'), false)->render();
    }
    
    public function index($vars, OrderIndexInputPortInterface $inputPort ) {
        $inputData = new OrderIndexInputData((new Auth(HospitalUser::class))->hospitalId,$vars['orderId'] , false);
        $inputPort->handle($inputData);
    }
}

