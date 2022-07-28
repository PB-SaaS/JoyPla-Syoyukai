<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedUpdateOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedUpdateOutputPortInterface;

    class OrderUnapprovedUpdatePresenter implements OrderUnapprovedUpdateOutputPortInterface
    {
        public function output(OrderUnapprovedUpdateOutputData $outputData)
        {
            $viewModel = new OrderUnapprovedUpdateViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message))->toJson();
        }
    }
        
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderUnapprovedUpdateViewModel
    {
        /**
         * Distributor constructor.
         * @param OrderUnapprovedUpdateOutputData $source
         */
        public function __construct(OrderUnapprovedUpdateOutputData $source)
        {
            $this->data = $source->data;
            $this->count = $source->count;
            $this->code = 200; 
            $this->message = "success";
        }
    }
}
