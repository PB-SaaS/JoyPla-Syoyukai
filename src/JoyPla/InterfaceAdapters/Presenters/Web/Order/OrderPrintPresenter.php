<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Order {

    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Order\OrderIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Order\OrderIndexOutputPortInterface;

    class OrderPrintPresenter implements OrderIndexOutputPortInterface
    {
        public function output(OrderIndexOutputData $outputData)
        {
            $viewModel = new OrderPrintViewModel($outputData);
            $order = $viewModel->order;
            $orderItems = $viewModel->orderItems;
            
            $body = View::forge('printLayout/Order/OrderSlip', compact('order','orderItems'), false)->render();
           
            echo view('printLayout/Common/Template', compact('body'), false)->render();
        }
    }
        
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Web\Order
     */
    class OrderPrintViewModel
    {
        /**
         * Distributor constructor.
         * @param OrderIndexOutputData $source
         */
        public function __construct(OrderIndexOutputData $source)
        {
            $this->order = $source->order;
            $this->orderItems = [[]];
            $x = 0;
            $count = 0;
            foreach($source->order['orderItems'] as $key => $item)
            {
                $count++;
                $item['id'] = $key + 1;
                $this->orderItems[$x][] = $item;
                if( ($count % 25 === 0 && $x === 0) || ( $count % 40 === 0 && $x > 0 ))
                {
                    $count = 0;
                    $x++;
                }
            }
        }
    }
}
