<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedItemDeleteOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedItemDeleteOutputPortInterface;

    class OrderUnapprovedItemDeletePresenter implements OrderUnapprovedItemDeleteOutputPortInterface
    {
        public function output(OrderUnapprovedItemDeleteOutputData $outputData)
        {
            $viewModel = new OrderUnapprovedItemDeleteViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message))->toJson();
        }
    }
        
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderUnapprovedItemDeleteViewModel
    {
        /**
         * Distributor constructor.
         * @param OrderUnapprovedItemDeleteOutputData $source
         */
        public function __construct(OrderUnapprovedItemDeleteOutputData $source)
        {
            $this->data = $source->data;
            $this->count = $source->count;
            $this->code = 200; 
            $this->message = "success";
        }
    }
}
