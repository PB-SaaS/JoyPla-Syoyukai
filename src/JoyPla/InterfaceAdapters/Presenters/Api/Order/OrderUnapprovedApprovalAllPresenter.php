<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Order {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedApprovalAllOutputData;
    use JoyPla\Application\OutputPorts\Api\Order\OrderUnapprovedApprovalAllOutputPortInterface;

    class OrderUnapprovedApprovalAllPresenter implements
        OrderUnapprovedApprovalAllOutputPortInterface
    {
        public function output(OrderUnapprovedApprovalAllOutputData $outputData)
        {
            $viewModel = new OrderUnapprovedApprovalAllViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['OrderUnapprovedApprovalAllPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Order
     */
    class OrderUnapprovedApprovalAllViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(
            OrderUnapprovedApprovalAllOutputData $source
        ) {
            $this->data = $source->data;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
