<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnReceivedShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnReceivedShowOutputPortInterface;

    class OrderUnReceivedShowPresenter implements
        OrderUnReceivedShowOutputPortInterface
    {
        public function output(OrderUnReceivedShowOutputData $outputData)
        {
            $viewModel = new OrderUnReceivedShowViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['OrderUnReceivedShowPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderUnReceivedShowViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(OrderUnReceivedShowOutputData $source)
        {
            $this->data = $source->orders;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
