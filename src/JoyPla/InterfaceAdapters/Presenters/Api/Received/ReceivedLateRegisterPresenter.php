<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Received {
    use ApiResponse;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedLateRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedLateRegisterOutputPortInterface;

    class ReceivedLateRegisterPresenter implements
        ReceivedLateRegisterOutputPortInterface
    {
        public function output(ReceivedLateRegisterOutputData $outputData)
        {
            $viewModel = new ReceivedLateRegisterViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['ReceivedLateRegisterPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Received
     */
    class ReceivedLateRegisterViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(ReceivedLateRegisterOutputData $source)
        {
            $this->data = $source->receiveds;
            $this->count = count($source->receiveds);
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
