<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Barcode {
    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Barcode\BarcodeSearchOutputData;
    use JoyPla\Application\OutputPorts\Api\Barcode\BarcodeSearchOutputPortInterface;

    class BarcodeSearchPresenter implements BarcodeSearchOutputPortInterface
    {
        public function output(BarcodeSearchOutputData $outputData)
        {
            $viewModel = new BarcodeSearchViewModel($outputData);
            echo (new ApiResponse(
                $viewModel->data,
                $viewModel->count,
                $viewModel->code,
                $viewModel->message,
                ['BarcodeSearchPresenter']
            ))->toJson();
        }
    }

    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Hospital\Api
     */
    class BarcodeSearchViewModel
    {
        public array $data = [];
        public int $count = 0;
        public int $code = 0;
        public string $message = '';

        public function __construct(BarcodeSearchOutputData $source)
        {
            $this->data = [
                'item' => $source->inHospitalItems,
                'type' => $source->type,
            ];
            $this->count = $source->count;
            $this->code = 200;
            $this->message = 'success';
        }
    }
}
