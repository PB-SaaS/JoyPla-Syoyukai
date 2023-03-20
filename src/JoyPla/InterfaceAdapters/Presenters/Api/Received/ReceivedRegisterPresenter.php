<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Received {
    use ApiResponse;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterOutputPortInterface;

    class ReceivedRegisterPresenter implements
        ReceivedRegisterOutputPortInterface
    {
        public function output(ReceivedRegisterOutputData $outputData)
        {
            $viewModel = new ReceivedRegisterViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['ReceivedRegisterPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Received
     */
    class ReceivedRegisterViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(ReceivedRegisterOutputData $source)
        {
            $this->data = $source->receiveds;
            $this->count = count($source->receiveds);
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
