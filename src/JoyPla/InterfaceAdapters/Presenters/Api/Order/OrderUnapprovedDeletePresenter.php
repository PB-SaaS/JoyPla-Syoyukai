<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedDeleteOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedDeleteOutputPortInterface;

    class OrderUnapprovedDeletePresenter implements
        OrderUnapprovedDeleteOutputPortInterface
    {
        public function output(OrderUnapprovedDeleteOutputData $outputData)
        {
            $viewModel = new OrderUnapprovedDeleteViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['OrderUnapprovedDeletePresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderUnapprovedDeleteViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(OrderUnapprovedDeleteOutputData $source)
        {
            $this->data = [];
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
