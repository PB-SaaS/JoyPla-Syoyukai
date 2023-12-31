<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Received {
    use ApiResponse;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedShowOutputPortInterface;

    class ReceivedShowPresenter implements ReceivedShowOutputPortInterface
    {
        public function output(ReceivedShowOutputData $outputData)
        {
            $viewModel = new ReceivedShowViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['ReceivedShowPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Received
     */
    class ReceivedShowViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(ReceivedShowOutputData $source)
        {
            $this->data = $source->receiveds;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
