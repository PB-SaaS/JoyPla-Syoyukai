<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\ReceivedReturn {
    use ApiResponse;
    use JoyPla\Application\OutputPorts\Api\ReceivedReturn\ReturnShowOutputData;
    use JoyPla\Application\OutputPorts\Api\ReceivedReturn\ReturnShowOutputPortInterface;

    class ReturnShowPresenter implements ReturnShowOutputPortInterface
    {
        public function output(ReturnShowOutputData $outputData)
        {
            $viewModel = new ReturnShowViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['ReturnShowPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Return
     */
    class ReturnShowViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(ReturnShowOutputData $source)
        {
            $this->data = $source->returns;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
