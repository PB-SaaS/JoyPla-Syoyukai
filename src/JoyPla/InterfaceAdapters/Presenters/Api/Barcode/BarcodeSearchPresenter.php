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
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message))->toJson();
        }
    }
    
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Hospital\Api
     */
    class BarcodeSearchViewModel
    {
        /**
         * Distributor constructor.
         * @param BarcodeSearchOutputData $source
         */
        public function __construct(BarcodeSearchOutputData $source)
        {
            $this->data = [
                'item' => $source->inHospitalItems,
                'type' => $source->type,
            ];
            $this->count = $source->count;
            $this->code = 200; 
            $this->message = "success";
        }
    }
}
