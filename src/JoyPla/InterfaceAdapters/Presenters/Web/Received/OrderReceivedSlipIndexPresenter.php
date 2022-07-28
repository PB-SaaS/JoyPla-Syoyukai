<?php

namespace JoyPla\InterfaceAdapters\Presenters\Web\Received {

    use App\SpiralDb\HospitalUser;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Web\Received\OrderReceivedSlipIndexOutputData;
    use JoyPla\Application\OutputPorts\Web\Received\OrderReceivedSlipIndexOutputPortInterface;

    class OrderReceivedSlipIndexPresenter implements OrderReceivedSlipIndexOutputPortInterface
    {
        public function output(OrderReceivedSlipIndexOutputData $outputData)
        {
            $viewModel = new OrderReceivedSlipIndexViewModel($outputData);
            $body = View::forge('html/Received/OrderReceivedSlipIndex', compact('viewModel'), false)->render();
            echo view('html/Common/Template', compact('body'), false)->render();
        }
    }
    
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Web\Received
     */
    class OrderReceivedSlipIndexViewModel
    {
        /**
         * Distributor constructor.
         * @param OrderReceivedSlipIndexOutputData $source
         */
        public function __construct(OrderReceivedSlipIndexOutputData $source)
        {
            $this->order = $source->order;
        }
    }
}