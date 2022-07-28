<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Order {

    use App\SpiralDb\HospitalUser;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Order\OrderIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Order\OrderIndexOutputPortInterface;

    class OrderIndexPresenter implements OrderIndexOutputPortInterface
    {
        public function output(OrderIndexOutputData $outputData)
        {
            
            $viewModel = new OrderIndexViewModel($outputData);
            $body = View::forge('html/Order/Index', compact('viewModel'), false)->render();
            echo view('html/Common/Template', compact('body'), false)->render();
        }
    }
    
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Web\Order
     */
    class OrderIndexViewModel
    {
        /**
         * Distributor constructor.
         * @param OrderIndexOutputData $source
         */
        public function __construct(OrderIndexOutputData $source)
        {
            $this->order = $source->order;
        }
    }
}