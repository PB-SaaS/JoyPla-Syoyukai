<?php

namespace JoyPla\InterfaceAdapters\Presenters\Api\Barcode {

    use ApiResponse;
    use framework\Http\View;
    use JoyPla\Application\OutputPorts\Api\Barcode\BarcodeOrderSearchOutputData;
    use JoyPla\Application\OutputPorts\Api\Barcode\BarcodeOrderSearchOutputPortInterface;

    class BarcodeOrderSearchPresenter implements BarcodeOrderSearchOutputPortInterface
    {
        public function output(BarcodeOrderSearchOutputData $outputData)
        {
            $viewModel = new BarcodeOrderSearchViewModel($outputData);
            echo (new ApiResponse( $viewModel->data, $viewModel->count , $viewModel->code, $viewModel->message))->toJson();
        }
    }
    
    /**
     * Class Distributor
     * @package JoyPla\InterfaceAdapters\Presenters\Hospital\Api
     */
    class BarcodeOrderSearchViewModel
    {
        /**
         * Distributor constructor.
         * @param BarcodeOrderSearchOutputData $source
         */
        public function __construct(BarcodeOrderSearchOutputData $source)
        {
            $this->data = [
                'item' => $source->orders,
                'type' => $source->type,
            ];
            $this->count = $source->count;
            $this->code = 200; 
            $this->message = "success";
        }
    }
}
