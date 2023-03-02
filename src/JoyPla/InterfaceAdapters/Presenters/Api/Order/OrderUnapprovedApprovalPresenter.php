<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedApprovalOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedApprovalOutputPortInterface;

    class OrderUnapprovedApprovalPresenter implements
        OrderUnapprovedApprovalOutputPortInterface
    {
        public function output(OrderUnapprovedApprovalOutputData $outputData)
        {
            $viewModel = new OrderUnapprovedApprovalViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['OrderUnapprovedApprovalPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderUnapprovedApprovalViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(OrderUnapprovedApprovalOutputData $source)
        {
            $this->data = $source->data;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
