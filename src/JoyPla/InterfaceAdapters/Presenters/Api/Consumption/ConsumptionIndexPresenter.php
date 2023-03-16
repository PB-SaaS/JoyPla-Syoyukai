<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Consumption {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionIndexOutputData;
    use JoyPla\Application\OutputPorts\Api\Consumption\ConsumptionIndexOutputPortInterface;

    class ConsumptionIndexPresenter implements
        ConsumptionIndexOutputPortInterface
    {
        public function output(ConsumptionIndexOutputData $outputData)
        {
            $viewModel = new ConsumptionIndexViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['ConsumptionIndexPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Consumption
     */
    class ConsumptionIndexViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(ConsumptionIndexOutputData $source)
        {
            $this->data = $source->consumptions;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
