<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Order {
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Order\OrderIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Order\OrderIndexOutputPortInterface;

    class OrderIndexPresenter implements OrderIndexOutputPortInterface
    {
        public function output(OrderIndexOutputData $outputData)
        {
            $viewModel = new OrderIndexViewModel($outputData);
            $body = View::forge(
                'html/Order/Index',
                compact('viewModel'),
                false
            )->render();
            echo view('html/Common/Template', compact('body'), false)->render();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Web\Order
     */
    class OrderIndexViewModel
    {
        public array $order;
        public function __construct(OrderIndexOutputData $source)
        {
            $this->order = $source->order;
        }
    }
}
