<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Received {
    use ApiResponse;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterByOrderSlipOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedRegisterByOrderSlipOutputPortInterface;

    class ReceivedRegisterByOrderSlipPresenter implements
        ReceivedRegisterByOrderSlipOutputPortInterface
    {
        public function output(
            ReceivedRegisterByOrderSlipOutputData $outputData
        ) {
            $viewModel = new ReceivedRegisterByOrderSlipViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['ReceivedRegisterByOrderSlipPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Received
     */
    class ReceivedRegisterByOrderSlipViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(
            ReceivedRegisterByOrderSlipOutputData $source
        ) {
            $this->data = $source->received;
            $this->count = count($source->received);
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
