<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\OrderRevisedOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderRevisedOutputPortInterface;

    class OrderRevisedPresenter implements OrderRevisedOutputPortInterface
    {
        public function output(OrderRevisedOutputData $outputData)
        {
            $viewModel = new OrderRevisedViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message, ['OrderRevisedPresenter']))->toJson();
        }
    }
        
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderRevisedViewModel
    {
        /**
         * Distributor constructor.
         * @param OrderRevisedOutputData $source
         */
        public function __construct(OrderRevisedOutputData $source)
        {
            $this->data = [];
            $this->count = 1;
            $this->code = 200; 
            $this->message = "success";
        }
    }
}
