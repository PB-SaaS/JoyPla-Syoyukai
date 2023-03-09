<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Distributor {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Distributor\DistributorIndexOutputData;
    use JoyPla\Application\OutputPorts\Api\Distributor\DistributorIndexOutputPortInterface;

    class DistributorIndexPresenter implements
        DistributorIndexOutputPortInterface
    {
        public function output(DistributorIndexOutputData $outputData)
        {
            $viewModel = new DistributorViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['DistributorIndexPresenter']
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

        public function __construct(DistributorIndexOutputData $source)
        {
            $this->data = $source->distributors;
            $this->count = count($source->distributors);
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
