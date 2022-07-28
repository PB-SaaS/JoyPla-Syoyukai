<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\OrderShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderShowOutputPortInterface;

    class OrderShowPresenter implements OrderShowOutputPortInterface
    {
        public function output(OrderShowOutputData $outputData)
        {
            $viewModel = new OrderShowViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message))->toJson();
        }
    }
        
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderShowViewModel
    {
        /**
         * Distributor constructor.
         * @param OrderShowOutputData $source
         */
        public function __construct(OrderShowOutputData $source)
        {
            $this->data = $source->orders;
            $this->count = $source->count;
            $this->code = 200; 
            $this->message = "success";
        }
    }
}
