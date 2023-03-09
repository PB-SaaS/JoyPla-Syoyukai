<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Distributor {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Distributor\DistributorShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Distributor\DistributorShowOutputPortInterface;

    class DistributorShowPresenter implements DistributorShowOutputPortInterface
    {
        public function output(DistributorShowOutputData $outputData)
        {
            $viewModel = new DistributorViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['DistributorShowPresenter']
            ))->toJson();
        }
    }

    /**
     * Class DistributorViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Distributor
     */
    class DistributorViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(DistributorShowOutputData $source)
        {
            $this->data = $source->distributors;
            $this->count = count($source->distributors);
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
