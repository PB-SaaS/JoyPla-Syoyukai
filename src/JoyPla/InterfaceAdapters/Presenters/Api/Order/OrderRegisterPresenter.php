<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\OrderRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderRegisterOutputPortInterface;

    class OrderRegisterPresenter implements OrderRegisterOutputPortInterface
    {
        public function output(OrderRegisterOutputData $outputData)
        {
            $viewModel = new OrderRegisterViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['OrderRegisterPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderRegisterViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(OrderRegisterOutputData $source)
        {
            $this->data = $source->ids;
            $this->count = count($source->ids);
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
