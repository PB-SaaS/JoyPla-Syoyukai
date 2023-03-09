<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedItemDeleteOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedItemDeleteOutputPortInterface;

    class OrderUnapprovedItemDeletePresenter implements
        OrderUnapprovedItemDeleteOutputPortInterface
    {
        public function output(OrderUnapprovedItemDeleteOutputData $outputData)
        {
            $viewModel = new OrderUnapprovedItemDeleteViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['OrderUnapprovedItemDeletePresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderUnapprovedItemDeleteViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(OrderUnapprovedItemDeleteOutputData $source)
        {
            $this->data = $source->data;
            $this->count = 1;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
