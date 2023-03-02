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
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['OrderShowPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderShowViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(OrderShowOutputData $source)
        {
            $this->data = $source->orders;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
