<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Price {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Price\PriceRegisterOutputData;
    use JoyPla\Application\OutputPorts\Api\Price\PriceRegisterOutputPortInterface;

    class PriceRegisterPresenter implements PriceRegisterOutputPortInterface
    {
        public function output(PriceRegisterOutputData $outputData)
        {
            $viewModel = new PriceRegisterViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['PriceRegisterPresenter']
            ))->toJson();
        }
    }

    /**
     * Class PriceRegisterViewModel
     * @package JoyPla\InterfaceAdapters\Presenters\Api\Price
     */
    class PriceRegisterViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(PriceRegisterOutputData $source)
        {
            $this->data = $source->Price;
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
