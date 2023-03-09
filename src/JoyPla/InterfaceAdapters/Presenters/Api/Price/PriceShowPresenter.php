<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Price {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Price\PriceShowOutputData;
    use JoyPla\Application\OutputPorts\Api\Price\PriceShowOutputPortInterface;

    class PriceShowPresenter implements PriceShowOutputPortInterface
    {
        public function output(PriceShowOutputData $outputData)
        {
            $viewModel = new PriceShowViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['PriceShowPresenter']
            ))->toJson();
        }
    }

    /**
     * Class PriceShowViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Price
     */
    class PriceShowViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(PriceShowOutputData $source)
        {
            $this->data = $source->Prices;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
