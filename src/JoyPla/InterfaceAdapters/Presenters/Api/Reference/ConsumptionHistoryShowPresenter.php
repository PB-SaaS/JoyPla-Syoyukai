<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Reference {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Reference\ConsumptionHistoryShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Reference\ConsumptionHistoryShowOutputPortInterface;

    class ConsumptionHistoryShowPresenter implements
        ConsumptionHistoryShowOutputPortInterface
    {
        public function output(ConsumptionHistoryShowOutputData $outputData)
        {
            $viewModel = new ConsumptionHistoryShowViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['ConsumptionHistoryShowPresenter']
            ))->toJson();
        }
    }

    /**
     * Class ConsumptionHistoryShow
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Reference
     */
    class ConsumptionHistoryShowViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(ConsumptionHistoryShowOutputData $source)
        {
            $this->data = $source->histories;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
