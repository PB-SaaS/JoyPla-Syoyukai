<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Received {
    use ApiResponse;
    use App\SpiralDb\HospitalUser;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedReturnRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Received\ReceivedReturnRegisterOutputPortInterface;

    class ReceivedReturnRegisterPresenter implements
        ReceivedReturnRegisterOutputPortInterface
    {
        public function output(ReceivedReturnRegisterOutputData $outputData)
        {
            $viewModel = new ReceivedReturnRegisterViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['ReceivedReturnRegisterPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Received
     */
    class ReceivedReturnRegisterViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(ReceivedReturnRegisterOutputData $source)
        {
            $this->data = $source->returns;
            $this->count = count($source->returns);
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
