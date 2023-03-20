<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\OrderItemBulkUpdateOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderItemBulkUpdateOutputPortInterface;

    class OrderItemBulkUpdatePresenter implements
        OrderItemBulkUpdateOutputPortInterface
    {
        public function output(OrderItemBulkUpdateOutputData $outputData)
        {
            $viewModel = new OrderItemBulkUpdateViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['OrderItemBulkUpdatePresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderItemBulkUpdateViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(OrderItemBulkUpdateOutputData $source)
        {
            $this->data = array_map(function ($order) {
                return $order['orderId'];
            }, $source->orders);
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
