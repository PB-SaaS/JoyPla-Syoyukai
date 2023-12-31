<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Consumption {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionRegisterOutputPortInterface;

    class ConsumptionRegisterPresenter implements
        ConsumptionRegisterOutputPortInterface
    {
        public function output(ConsumptionRegisterOutputData $outputData)
        {
            $viewModel = new ConsumptionRegisterViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['ConsumptionRegisterPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Hospital\Api
     */
    class ConsumptionRegisterViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(ConsumptionRegisterOutputData $source)
        {
            $this->data = $source->ids;
            $this->count = count($source->ids);
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
