<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\TotalizationOutputData;
    use JoyPla\Application\OutputPorts\Api\ItemRequest\TotalizationOutputPortInterface;

    class TotalizationPresenter implements TotalizationOutputPortInterface
    {
        public function output(TotalizationOutputData $outputData)
        {
            $viewModel = new TotalizationViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['TotalizationPresenter']
            ))->toJson();
        }
    }

    /**
     * Class TotalizationViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\ItemRequest
     */
    class TotalizationViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(TotalizationOutputData $source)
        {
            $this->data = $source->totalRequestItems;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
